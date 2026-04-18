<?php

class pluginVisitsStats extends Plugin
{

	public function init()
	{
		global $L;
		$this->dbFields = array(
			'label'         => $L->g('Visits'),
			'excludeAdmins' => false
		);
	}

	public function adminHead()
	{
		if ($GLOBALS['ADMIN_VIEW'] !== 'dashboard') {
			return false;
		}

		$html  = $this->includeCSS('chart.min.css');
		$html .= $this->includeJS('chart.bundle.min.js');
		return $html;
	}

	// Plugin form for settings
	public function form()
	{
		global $L;

		$html  = '<div class="mb-3">';
		$html .= '<label class="form-label" for="label">' . $L->get('Label') . '</label>';
		$html .= '<input class="form-control" id="label" name="label" type="text" dir="auto" value="' . $this->getValue('label') . '">';
		$html .= '<div class="form-text">' . $L->get('This title is almost always used in the sidebar of the site') . '</div>';
		$html .= '</div>';

		if (defined('BLUDIT_PRO')) {
			$html .= '<div class="mb-3">';
			$html .= '<label class="form-label" for="excludeAdmins">' . $L->get('Exclude administrators users') . '</label>';
			$html .= '<select class="form-select" id="excludeAdmins" name="excludeAdmins">';
			$html .= '<option value="true" ' . ($this->getValue('excludeAdmins') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
			$html .= '<option value="false" ' . ($this->getValue('excludeAdmins') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
			$html .= '</select>';
			$html .= '</div>';
		}

		return $html;
	}

	public function siteBodyEnd()
	{
		$this->addVisitor();
		$this->deleteOldLogs();
	}

	// Delete log files older than 7 days
	public function deleteOldLogs()
	{
		$logs = Filesystem::listFiles($this->workspace(), '*', 'log', true);
		$remove = array_slice($logs, 7);
		foreach ($remove as $log) {
			Filesystem::rmfile($log);
		}
	}

	// Returns the number of visits for a given date (YYYY-MM-DD)
	public function visits($date)
	{
		$file = $this->workspace() . $date . '.log';
		if (!file_exists($file)) {
			return 0;
		}
		$handle = @fopen($file, 'rb');
		if ($handle === false) {
			return 0;
		}

		$lines = 0;
		while (!feof($handle)) {
			$lines += substr_count(fread($handle, 8192), PHP_EOL);
		}
		@fclose($handle);
		return $lines;
	}

	// Returns the number of unique visitors for a given date (YYYY-MM-DD)
	public function uniqueVisitors($date)
	{
		$file = $this->workspace() . $date . '.log';
		if (!file_exists($file)) {
			return 0;
		}
		$lines = @file($file);
		if (empty($lines)) {
			return 0;
		}

		$seen = array();
		foreach ($lines as $line) {
			$data = json_decode($line);
			if ($data) {
				$seen[$data[0]] = true;
			}
		}
		return count($seen);
	}

	// Returns visits and unique-visitor counts for the last $days days
	// Returns ['labels'=>[], 'visits'=>[], 'unique'=>[], 'total'=>int]
	public function getLastDaysData($days = 7)
	{
		$result = array('labels' => array(), 'visits' => array(), 'unique' => array(), 'total' => 0);
		for ($i = $days - 1; $i >= 0; $i--) {
			$date  = Date::currentOffset('Y-m-d', '-' . $i . ' day');
			$v     = $this->visits($date);
			$u     = $this->uniqueVisitors($date);
			$result['labels'][] = Date::format($date, 'Y-m-d', 'D');
			$result['visits'][] = $v;
			$result['unique'][] = $u;
			$result['total']   += $v;
		}
		return $result;
	}

	// Add a line to the current day log file
	public function addVisitor()
	{
		if (Cookie::get('BLUDIT-KEY') && defined('BLUDIT_PRO') && $this->getValue('excludeAdmins')) {
			return false;
		}
		$currentTime = Date::current('Y-m-d H:i:s');
		$ip          = TCP::getIP();
		$hashIP      = md5($ip);

		$line        = json_encode(array($hashIP, $currentTime));
		$currentDate = Date::current('Y-m-d');
		$logFile     = $this->workspace() . $currentDate . '.log';

		return file_put_contents($logFile, $line . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
	}
}
