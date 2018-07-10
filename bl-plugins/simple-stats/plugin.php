<?php
/*
	This plugin use the javascript library https://github.com/gionkunz/chartist-js
*/
class pluginSimpleStats extends Plugin {

	private $loadOnController = array(
		'dashboard'
	);

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Visits',
			'numberOfDays'=>7,
			'excludeAdmins'=>false
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$Language->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		if (defined('BLUDIT_PRO')) {
			$html .= '<div>';
			$html .= '<label>'.$Language->get('Exclude administrators users').'</label>';
			$html .= '<select name="excludeAdmins">';
			$html .= '<option value="true" '.($this->getValue('excludeAdmins')===true?'selected':'').'>'.$Language->get('Enabled').'</option>';
			$html .= '<option value="false" '.($this->getValue('excludeAdmins')===false?'selected':'').'>'.$Language->get('Disabled').'</option>';
			$html .= '</select>';
			$html .= '</div>';
		}

		return $html;
	}

	public function adminHead()
	{
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		// Include plugin's CSS files
		$html  = $this->includeCSS('chartist.min.css');
		$html .= $this->includeCSS('style.css');

		// Include plugin's Javascript files
		$html .= $this->includeJS('chartist.min.js');

		return $html;
	}

	public function dashboard()
	{
		$label = $this->getValue('label');
		$currentDate = Date::current('Y-m-d');
		$visitsToday = $this->visits($currentDate);
		$uniqueVisitors = $this->uniqueVisitors($currentDate);

$html = <<<EOF
<div class="simple-stats-plugin">
	<div class="container mt-5 pt-5 border-top">
		<h4 class="pb-3">$label</h4>
		<div class="row">
			<div class="col">
			<div class="ct-chart ct-perfect-fourth"></div>
			<p class="legends visits-today">Visits today: $visitsToday</p>
			<p class="legends unique-today">Unique visitors today: $uniqueVisitors</p>
			</div>
		</div>
	</div>
</div>
EOF;
		$numberOfDays = $this->getValue('numberOfDays');
		$numberOfDays = $numberOfDays - 1;
		for ($i=$numberOfDays; $i >= 0 ; $i--) {
			$dateWithOffset = Date::currentOffset('Y-m-d', '-'.$i.' day');
			$visits[$i] = $this->visits($dateWithOffset);
			$unique[$i] = $this->uniqueVisitors($dateWithOffset);
			$days[$i] = Date::format($dateWithOffset, 'Y-m-d', 'D');
		}

		$labels = "'" . implode("','", $days) . "'";
		$seriesVisits = implode(',', $visits);
		$seriesUnique = implode(',', $unique);

$script = <<<EOF
<script>
	var data = {
		labels: [$labels],
		series: [
			[$seriesVisits],
			[$seriesUnique]
		]
	};

	var options = {
		height: 250,
		onlyInteger: true
	};

	new Chartist.Line('.ct-chart', data, options);
</script>
EOF;

		$this->deleteOldLogs();

		return $html.PHP_EOL.$script.PHP_EOL;
	}

	public function siteBodyEnd()
	{
		$this->addVisitor();
	}

	// Keep only 7 days of logs, remove the old ones
	public function deleteOldLogs()
	{
		$logs = Filesystem::listFiles($this->workspace(), '*', 'log', true);
		$remove = array_slice($logs, 7);

		foreach ($remove as $log) {
			Filesystem::rmfile($log);
		}
	}

	// Returns the amount of visits by date
	public function visits($date)
	{
		$file = $this->workspace().$date.'.log';
		$handle = @fopen($file, 'rb');
		if ($handle===false) {
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

	// Returns the amount of unique visitors by date
	public function uniqueVisitors($date)
	{
		$file = $this->workspace().$date.'.log';
		$lines = @file($file);
		if (empty($lines)) {
			return 0;
		}
		$tmp = array();
		foreach ($lines as $line) {
			$key = json_decode($line);
			$tmp[$key[0]] = true;
		}
		return count($tmp);
	}

	// Add a line to the current log
	// The line is a json array with the hash IP of the visitor and the time
	public function addVisitor()
	{
		// Exclude administrators visits
		global $Login;
		if ($this->getValue('excludeAdmins') && defined('BLUDIT_PRO')) {
			if ($Login->role()=='admin') {
				return false;
			}
		}

                $currentTime = Date::current('Y-m-d H:i:s');
		$ip = TCP::getIP();
		if (empty($ip)) {
			$ip = session_id();
		}
		$hashIP = md5($ip);

		$line = json_encode(array($hashIP, $currentTime));
		$currentDate = Date::current('Y-m-d');
		$file = $this->workspace().$currentDate.'.log';

		return file_put_contents($file, $line.PHP_EOL, FILE_APPEND | LOCK_EX)!==false;
	}

}