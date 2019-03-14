<?php defined('BLUDIT') or die('Bludit CMS.');

class Language extends dbJSON {
	public $data;
	public $db;
	public $currentLanguage;
	public $dates;
	public $unicodeChars;

	function __construct($currentLanguage)
	{
		$this->data = array();
		$this->db = array();
		$this->currentLanguage = $currentLanguage;
		$this->dates = array();
		$this->unicodeChars = array();

		// Load default language
		$filename = PATH_LANGUAGES.DEFAULT_LANGUAGE_FILE;
		if (Sanitize::pathFile($filename)) {
			$Tmp = new dbJSON($filename, false);
			$this->db = array_merge($this->db, $Tmp->db);
		}

		// If the user defined a new language replace the content of the default language
		// If the new dictionary has missing keys this are going to take from the default language
		$filename = PATH_LANGUAGES.$currentLanguage.'.json';
		if (Sanitize::pathFile($filename) && (DEFAULT_LANGUAGE_FILE!==$currentLanguage.'.json')) {
			$Tmp = new dbJSON($filename, false);
			$this->db = array_merge($this->db, $Tmp->db);
		}

		// Language-data
		$this->data = $this->db['language-data'];
		unset($this->db['language-data']);

		// Dates
		if (isset($this->db['dates'])) {
			$this->dates = $this->db['dates'];
			unset($this->db['dates']);
		}

		// Unicode chars
		if (isset($this->db['unicode-chars'])) {
			$this->unicodeChars = $this->db['unicode-chars'];
			unset($this->db['unicode-chars']);
		}
	}

	public function locale()
	{
		if (isset($this->data['locale'])) {
			return $this->data['locale'];
		}

		return $this->currentLanguage;
	}

	public function currentLanguage()
	{
		return $this->currentLanguage;
	}

	public function currentLanguageShortVersion()
	{
		$current = $this->currentLanguage;
		$explode = explode('_', $current);
		return $explode[0];
	}

	// Return the translation, if the translation doesn't exist returns the English translation
	public function get($string)
	{
		$key = Text::lowercase($string);
		$key = Text::replace(' ', '-', $key);
		$key = Text::replace('.', '', $key);

		//file_put_contents(DEBUG_FILE, $key.PHP_EOL, FILE_APPEND);

		if (isset($this->db[$key])) {
			return $this->db[$key];
		}

		//$line = '"'.$key.'": "'.$string.'",';
		//file_put_contents(DEBUG_FILE, $line.PHP_EOL, FILE_APPEND);
		return $string;
	}

	// Returns translation
	public function g($string)
	{
		return $this->get($string);
	}

	// Print translation
	public function printMe($string)
	{
		echo $this->get($string);
	}

	// Print translation
	public function p($string)
	{
		echo $this->get($string);
	}

	// Add keys=>values to the current dicionary
	// This method don't overwrite the current value
	public function add($array)
	{
		$this->db = array_merge($this->db, $array);
	}

	// Returns an array with all dictionaries
	public function getLanguageList()
	{
		$files = Filesystem::listFiles(PATH_LANGUAGES, '*', 'json');
		$tmp = array();
		foreach($files as $file) {
			$t = new dbJSON($file, false);
			if (isset($t->db['language-data']['native'])) {
				$native = $t->db['language-data']['native'];
				$locale = basename($file, '.json');
				$tmp[$locale] = $native;
			}
		}
		return $tmp;
	}

	// Returns array with all the dates and months
	public function getDates()
	{
		return $this->dates;
	}

	// Returns array with all the special characters from this language
	public function getunicodeChars()
	{
		return $this->unicodeChars;
	}
}