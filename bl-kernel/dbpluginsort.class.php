<?php
class dbPluginSort extends dbJSON {

	public $dbFields = array(
		'pluginList'=>	array('inFile'=>false, 'value'=>array())
	);

	function __construct() {
		parent::__construct(PATH_DATABASES.'plugin-positions.php');
	}

	public function getDB() {
		return $this->db;
	}

	public function get($dir, $name) {
		if (isset($this->db[$dir][$name])) {
			return $this->db[$dir][$name];
		}
		return 0;
	}

	public function set($args)
	{
		$i = 1;
		array_walk($args, '&Sanitize::html');
		$pluginList = array_shift($args);

		foreach(array_keys($args) as $plugin) {
			$this->db[$pluginList][$plugin] = $i++;
		}
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}
}
?>