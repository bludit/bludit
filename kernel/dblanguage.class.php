<?php defined('BLUDIT') or die('Bludit CMS.');

class dbLanguage extends dbJSON
{
	public $en_EN;

	function __construct($language)
	{
		parent::__construct(PATH_LANGUAGES.'en_EN.json', false);
		$this->en_EN = $this->db;

		parent::__construct(PATH_LANGUAGES.$language.'.json', false);
	}

	// Return the translation, if the translation does'n exist then return the English translation.
	public function get($text)
	{
		$key = Text::lowercase($text);
		$key = Text::replace(' ', '-', $key);

		if(isset($this->db[$key]))
			return $this->db[$key];

		// If the key is not translated then return the English translation.
		return $this->en_EN[$key];
	}

	// Print the translation.
	public function p($text)
	{
		echo $this->get($text);
	}

}
