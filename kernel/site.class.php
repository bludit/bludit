<?php defined('BLUDIT') or die('Bludit CMS.');

class Site extends DB_SERIALIZE
{
	function __construct()
	{
		parent::__construct(PATH_DATABASES.'site.php');

		// Set timezone
		$this->setTimezone( $this->timezone() );

		// Set locale
		$this->setLocale( $this->locale() );
	}

	// Returns an array with site configuration.
	function get()
	{
		return $this->vars;
	}

	// Returns an array with the filters for the url.
	public function urlFilters()
	{
		return $this->vars['urlFilters'];
	}

	// Returns the site title.
	public function title()
	{
		return $this->vars['title'];
	}

	// Returns the site slogan.
	public function slogan()
	{
		return $this->vars['slogan'];
	}

	// Returns the site theme name.
	public function theme()
	{
		return $this->vars['theme'];
	}

	// Returns the admin theme name.
	public function adminTheme()
	{
		return $this->vars['adminTheme'];
	}

	// Returns the footer text.
	public function footer()
	{
		return $this->vars['footer'];
	}

	// Returns the timezone.
	public function timezone()
	{
		return $this->vars['timezone'];
	}

	// Returns the current language.
	public function language()
	{
		return $this->vars['language'];
	}

	// Returns the current locale.
	public function locale()
	{
		return $this->vars['locale'];
	}

	// Returns the current homepage.
	public function homepage()
	{
		return $this->vars['homepage'];
	}

	// Set the locale.
	public function setLocale($locale)
	{
		if(setlocale(LC_ALL, $locale.'.UTF-8')!==false)
			return true;

		if(setlocale(LC_ALL, $locale.'.UTF8')!==false)
			return true;

		return setlocale(LC_ALL, $locale);
	}

	// Set the timezone.
	public function setTimezone($timezone)
	{
		return date_default_timezone_set($timezone);
	}

}

?>
