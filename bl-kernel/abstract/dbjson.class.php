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

	// Returns the value from the field.
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

	// Returns a JSON encoded string on success or FALSE on failure.
	private function serialize($data)
	{
		return json_encode($data, JSON_PRETTY_PRINT);
	}

	// Returns the value encoded in json in appropriate PHP type.
	private function unserialize($data)
	{
		// NULL is returned if the json cannot be decoded.
		$decode = json_decode($data, true);

		// If NULL returns false.
		if(empty($decode)) {
			return false;
		}

		return $decode;
	}

}