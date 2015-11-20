<?php defined('BLUDIT') or die('Bludit CMS.');

class dbJSON
{
	public $db;
	public $dbBackup;
	public $file;
	public $firstLine;

	// $file, the JSON file.
	// $firstLine, TRUE if you want to remove the first line, FALSE otherwise.
	function __construct($file, $firstLine=true)
	{
		$this->file = $file;
		$this->db = array();
		$this->dbBackup = array();
		$this->firstLine = $firstLine;

		if(file_exists($file))
		{
			// Read JSON file.
			$lines = file($file);

			// Remove the first line, the first line is for security reasons.
			if($firstLine) {
				unset($lines[0]);
			}

			// Regenerate the JSON file.
			$implode = implode($lines);

			// Unserialize, JSON to Array.
			$array = $this->unserialize($implode);

			if(empty($array)) {
				Log::set(__METHOD__.LOG_SEP.'Invalid JSON file: '.$file.', cannot be decoded. Check the file content.');
			}
			else {
				$this->db = $array;
				$this->dbBackup = $array;
			}
		}
		else
		{
			Log::set(__METHOD__.LOG_SEP.'File '.$file.' does not exists');
		}
	}

	public function restoreDB()
	{
		$this->db = $this->dbBackup;
		return true;
	}

	// Returns the amount of database items.
	public function count()
	{
		return count($this->db);
	}

	public function getField($field)
	{
		if(isset($this->db[$field])) {
			return $this->db[$field];
		}

		return $this->dbFields[$field]['value'];
	}

	// Save the JSON file.
	public function save()
	{
		$data = '';

		if($this->firstLine) {
			$data  = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>".PHP_EOL;
		}

		// Serialize database
		$data .= $this->serialize($this->db);

		// Backup the new database.
		$this->dbBackup = $this->db;

		// LOCK_EX flag to prevent anyone else writing to the file at the same time.
		return file_put_contents($this->file, $data, LOCK_EX);
	}

	private function serialize($data)
	{
		// DEBUG: La idea es siempre serializar en json, habria que ver si siempre esta cargado json_enconde y decode
		if(JSON) {
			return json_encode($data, JSON_PRETTY_PRINT);
		}

		return serialize($data);
	}

	private function unserialize($data)
	{
		// DEBUG: La idea es siempre serializar en json, habria que ver si siempre esta cargado json_enconde y decode
		if(JSON) {
			return json_decode($data, true);
		}

		return unserialize($data);
	}

}