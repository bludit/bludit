<?php defined('BLUDIT') or die('Bludit CMS.');

class dbJSON
{
	public $db;
	public $file;
	public $firstLine;

	// $file, the JSON file.
	// $firstLine, TRUE if you want to remove the first line, FALSE otherwise.
	function __construct($file, $firstLine=true)
	{
		$this->file = $file;
		$this->db = array();
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
			$this->db = $this->unserialize($implode);
		}
		else
		{
			Log::set(__METHOD__.LOG_SEP.'File '.$file.' does not exists');
		}
	}

	// Returns the amount of database items.
	public function count()
	{
		return count($this->db);
	}

	// Save the JSON file.
	public function save()
	{
		if($this->firstLine) {
			$data  = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>".PHP_EOL;
		}
		else {
			$data = '';
		}

		$data .= $this->serialize($this->db);

		// LOCK_EX flag to prevent anyone else writing to the file at the same time.
		file_put_contents($this->file, $data, LOCK_EX);
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
