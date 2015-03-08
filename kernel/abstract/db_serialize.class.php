<?php

// Database serialize
class DB_SERIALIZE
{
	public $vars;
	public $file;
	public $firstLine;

	function __construct($file, $firstLine=true)
	{
		$this->file = $file;

		$lines = file($file);

		$this->firstLine = $firstLine;

		if($firstLine)
		{
			// Remove the first line.
			unset($lines[0]);
		}

		$implode = implode($lines);

		$this->vars = $this->unserialize($implode);
	}

	public function save()
	{
		if($this->firstLine)
			$data  = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>".PHP_EOL;
		else
			$data = '';

		$data .= $this->serialize($this->vars);

		// LOCK_EX flag to prevent anyone else writing to the file at the same time.
		return file_put_contents($this->file, $data, LOCK_EX);
	}

	// DEBUG, ver si sirve para la instalacion, sino borrar
	public function setDb($db)
	{
		$this->vars = $db;
		
		return $this->save();
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

	// DEBUG, se puede borrar
	function show()
	{
		var_dump($this->vars);
	}



}

?>
