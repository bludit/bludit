<?php defined('BLUDIT') or die('Bludit CMS.');

class dbJSON
{
	public $db;
	public $file;
	public $firstLine;

	function __construct($file, $firstLine=true)
	{
		$this->file = $file;
		$this->db = array();
		$this->firstLine = $firstLine;

		if(file_exists($file))
		{
			$lines = file($file);

			if($firstLine)
			{
				// Remove the first line.
				unset($lines[0]);
			}

			$implode = implode($lines);

			$this->db = $this->unserialize($implode);
		}
		else
		{
			Log::set(__METHOD__.LOG_SEP.'File '.$file.' dosent exists');
		}
	}

	public function save()
	{
		if($this->firstLine)
			$data  = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>".PHP_EOL;
		else
			$data = '';

		$data .= $this->serialize($this->db);

		// LOCK_EX flag to prevent anyone else writing to the file at the same time.
		file_put_contents($this->file, $data, LOCK_EX);
	}

	private function serialize($data)
	{
		// DEBUG: La idea es siempre serializar en json, habria que ver si siempre esta cargado json_enconde y decode
		if(JSON)
			return json_encode($data, JSON_PRETTY_PRINT);

		return serialize($data);
	}

	private function unserialize($data)
	{
		// DEBUG: La idea es siempre serializar en json, habria que ver si siempre esta cargado json_enconde y decode
		if(JSON)
			return json_decode($data, true);

		return unserialize($data);
	}

	// DEBUG, ver si sirve para la instalacion, sino borrar
	public function setDb($db)
	{
		$this->db = $db;

		return $this->save();
	}

	// DEBUG, se puede borrar
	public function show()
	{
		var_dump($this->db);
	}

}