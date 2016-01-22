<?php defined('BLUDIT') or die('Bludit CMS.');

class dbLanguage extends dbJSON
{
	public $data;
	public $db;
	public $currentLocale;

	function __construct($locale)
	{
		$this->data = array();
		$this->db = array();
		$this->currentLocale = 'en_US';

		// Default language en_US
		$filename = PATH_LANGUAGES.'en_US.json';
		if( Sanitize::pathFile($filename) )
		{
			$Tmp = new dbJSON($filename, false);
			$this->db = array_merge($this->db, $Tmp->db);
		}

		// User language
		$filename = PATH_LANGUAGES.$locale.'.json';
		if( Sanitize::pathFile($filename) && ($locale!=="en_US") )
		{
			$this->currentLocale = $locale;
			$Tmp = new dbJSON($filename, false);
			$this->db = array_merge($this->db, $Tmp->db);
		}

		$this->data = $this->db['language-data'];
		unset($this->db['language-data']);
	}

	public function getCurrentLocale()
	{
		return $this->currentLocale;
	}

	// Return the translation, if the translation does'n exist then return the English translation.
	public function get($string)
	{
		$key = Text::lowercase($string);
		$key = Text::replace(' ', '-', $key);

		if(isset($this->db[$key])) {
			return $this->db[$key];
		}

		return '';
	}

	// Returns translation.
	public function g($string)
	{
		return $this->get($string);
	}

	// Print translation.
	public function printMe($string)
	{
		echo $this->get($string);
	}

	// Print translation.
	public function p($string)
	{
		echo $this->get($string);
	}

	public function add($array)
	{
		$this->db = array_merge($this->db, $array);
	}

	// Returns the item from plugin-data.
	public function getData($key)
	{
		if(isset($this->data[$key])) {
			return $this->data[$key];
		}

		return '';
	}

	// Returns an array with all dictionaries.
	public function getLanguageList()
	{
		$files = Filesystem::listFiles(PATH_LANGUAGES, '*', 'json');

		$tmp = array();

		foreach($files as $file)
		{
			$t = new dbJSON($file, false);

			// Check if the JSON is complete.
			if(isset($t->db['language-data']['native']))
			{
				$native = $t->db['language-data']['native'];
				$locale = basename($file, '.json');
				$tmp[$locale] = $native;
			}
		}

		return $tmp;
	}
}
