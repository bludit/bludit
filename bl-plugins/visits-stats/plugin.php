<?php

class pluginVisitsStats extends Plugin
{

	private $loadOnViews = array(
		'dashboard' // Load this plugin only in the Dashboard
	);

	public function init()
	{
		global $L;
		$this->dbFields = array(
			'label' => $L->g('Visits'),
			'excludeAdmins' => false
		);
	}

	public function adminHead()
	{
		if (!in_array($GLOBALS['ADMIN_VIEW'], $this->loadOnViews)) {
			return false;
		}

		$html  = $this->includeCSS('chart.min.css');
		$html .= $this->includeJS('chart.bundle.min.js');
		return $html;
	}

	public function dashboard()
	{
		global $L;
		$label = $this->getValue('label');

		$currentDate = Date::current('Y-m-d');
		$visitsToday = $this->visits($currentDate);
		$uniqueVisitors = $this->uniqueVisitors($currentDate);

		$numberOfDays = 6;
		for ($i = $numberOfDays; $i >= 0; $i--) {
			$dateWithOffset = Date::currentOffset('Y-m-d', '-' . $i . ' day');
			$visits[$i] = $this->visits($dateWithOffset);
			$unique[$i] = $this->uniqueVisitors($dateWithOffset);
			$days[$i] = Date::format($dateWithOffset, 'Y-m-d', 'D');
		}

		$labels = "'" . implode("','", $days) . "'";
		$seriesVisits = implode(',', $visits);
		$seriesVisitors = implode(',', $unique);

		$labelVisits = $L->g('Visits');
		$labelVisitors = $L->g('Visitors');

		return <<<EOF
<div class="pluginVisitsStats mt-4 mb-4 pb-4 border-bottom">
	<h3 class="m-0 p-0"><i class="bi bi-bar-chart"></i>$label</h3>
	<canvas id="visits-stats"></canvas>
</div>

<script>
var ctx = document.getElementById('visits-stats');
new Chart(ctx, {
	type: 'bar',
	data: {
		labels: [$labels],
		datasets: [{
			backgroundColor: 'rgb(13,110,253)',
			borderColor: 'rgb(13,110,253)',
			label: '$labelVisitors',
			data: [$seriesVisitors]
		},
		{
			backgroundColor: 'rgb(255, 193, 3)',
			borderColor: 'rgb(255, 193, 3)',
			label: '$labelVisits',
			data: [$seriesVisits]
		}]
	},
	options: {
		scales: {
			yAxes: [{
				ticks: {
					beginAtZero: true,
					stepSize: 1
				}
			}]
		}
	}
});
</script>
EOF;
	}

	// Plugin form for settings
	public function form()
	{
		global $L;

		$html  = '<div class="mb-3">';
		$html .= '<label class="form-label" for="label">' . $L->get('Label') . '</label>';
		$html .= '<input class="form-control" id="label" name="label" type="text" value="' . $this->getValue('label') . '">';
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
	}

	// Delete old logs
	public function deleteOldLogs()
	{
		$logs = Filesystem::listFiles($this->workspace(), '*', 'log', true);
		// Keep only 7 days of logs
		$remove = array_slice($logs, 7);
		foreach ($remove as $log) {
			Filesystem::rmfile($log);
		}
	}

	// Returns the number of visits by date
	public function visits($date)
	{
		$file = $this->workspace() . $date . '.log';
		$handle = @fopen($file, 'rb');
		if ($handle === false) {
			return 0;
		}

		// The amount of visits are the number of lines on the file
		$lines = 0;
		while (!feof($handle)) {
			$lines += substr_count(fread($handle, 8192), PHP_EOL);
		}
		@fclose($handle);
		return $lines;
	}

	// Returns the number of unique visitors by date
	public function uniqueVisitors($date)
	{
		$file = $this->workspace() . $date . '.log';
		$lines = @file($file);
		if (empty($lines)) {
			return 0;
		}

		$tmp = array();
		foreach ($lines as $line) {
			$data = json_decode($line);
			$hashIP = $data[0];
			$tmp[$hashIP] = true;
		}
		return count($tmp);
	}

	// Add a line to the current log
	// The line is a json array with the hash IP of the visitor and the time
	public function addVisitor()
	{
		if (Cookie::get('BLUDIT-KEY') && defined('BLUDIT_PRO') && $this->getValue('excludeAdmins')) {
			return false;
		}
		$currentTime = Date::current('Y-m-d H:i:s');
		$ip = TCP::getIP();
		$hashIP = md5($ip);

		$line = json_encode(array($hashIP, $currentTime));
		$currentDate = Date::current('Y-m-d');
		$logFile = $this->workspace() . $currentDate . '.log';

		return file_put_contents($logFile, $line . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
	}
}
