<?php defined('BLUDIT') or die('Bludit CMS.');

class dbLanguage extends dbJSON
{
	public $en_US;
	private $data;

	function __construct($language)
	{
		$this->data = array();

		// Default language en_US
		$filename = PATH_LANGUAGES.'en_US.json';
		if(file_exists($filename))
		{
			parent::__construct($filename, false);
			$this->en_US = $this->db;
		}

		// User language
		$filename = PATH_LANGUAGES.$language.'.json';
		if(file_exists($filename))
		{
			parent::__construct($filename, false);
			$this->data = $this->db['language-data'];
		}
	}

	public function getLanguageList()
	{
		$files = glob(PATH_LANGUAGES.'*.json');

		$tmp = array();

		foreach($files as $file)
		{
			$t = new dbJSON($file, false);
			$native = $t->db['language-data']['native'];
			$locale = basename($file, '.json');
			$tmp[$locale] = $native;
		}

		return $tmp;
	}

	// Return the translation, if the translation does'n exist then return the English translation.
	public function get($text)
	{
		$key = Text::lowercase($text);
		$key = Text::replace(' ', '-', $key);

		if(isset($this->db[$key]))
			return $this->db[$key];

		// If the key is not translated then return the English translation.
		return $this->en_US[$key];
	}

	// Print the translation.
	public function p($text)
	{
		echo $this->get($text);
	}

}
