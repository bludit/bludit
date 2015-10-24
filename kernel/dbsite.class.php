<?php defined('BLUDIT') or die('Bludit CMS.');

class dbSite extends dbJSON
{
	private $dbFields = array(
		'title'=>		array('inFile'=>false, 'value'=>'I am Guybrush Threepwood, mighty developer'),
		'slogan'=>		array('inFile'=>false, 'value'=>''),
		'description'=>		array('inFile'=>false, 'value'=>''),
		'footer'=>		array('inFile'=>false, 'value'=>'I wanna be a pirate!'),
		'postsperpage'=>	array('inFile'=>false, 'value'=>''),
		'language'=>		array('inFile'=>false, 'value'=>'en'),
		'locale'=>		array('inFile'=>false, 'value'=>'en_US'),
		'timezone'=>		array('inFile'=>false, 'value'=>'America/Argentina/Buenos_Aires'),
		'theme'=>		array('inFile'=>false, 'value'=>'pure'),
		'adminTheme'=>		array('inFile'=>false, 'value'=>'default'),
		'homepage'=>		array('inFile'=>false, 'value'=>''),
		'uriPage'=>		array('inFile'=>false, 'value'=>'/'),
		'uriPost'=>		array('inFile'=>false, 'value'=>'/post/'),
		'uriTag'=>		array('inFile'=>false, 'value'=>'/tag/'),
		'url'=>			array('inFile'=>false, 'value'=>''),
		'cliMode'=>		array('inFile'=>false, 'value'=>true),
		'emailFrom'=>		array('inFile'=>false, 'value'=>'')
	);

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
		return $this->db;
	}

	public function set($args)
	{
		foreach($args as $field=>$value)
		{
			if( isset($this->dbFields[$field]) )
			{
				$this->db[$field] = Sanitize::html($value);
			}
		}

		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

	// Returns an array with the filters for the url.
	public function uriFilters($filter='')
	{
		$filters['admin'] = '/admin/';
		$filters['post'] = $this->db['uriPost'];
		$filters['page'] = $this->db['uriPage'];
		$filters['tag'] = $this->db['uriTag'];

		if(empty($filter)) {
			return $filters;
		}

		return $filters[$filter];
	}

	public function urlPost()
	{
		return $this->url().ltrim($this->db['uriPost'], '/');
	}

	public function urlPage()
	{
		return $this->url().ltrim($this->db['uriPage'], '/');
	}

	public function urlTag()
	{
		return $this->url().ltrim($this->db['uriTag'], '/');
	}

	// Returns the site title.
	public function title()
	{
		return $this->db['title'];
	}

	public function emailFrom()
	{
		return $this->db['emailFrom'];
	}

	// Returns the site slogan.
	public function slogan()
	{
		return $this->db['slogan'];
	}

	// Returns the site description.
	public function description()
	{
		return $this->db['description'];
	}

	// Returns the site theme name.
	public function theme()
	{
		return $this->db['theme'];
	}

	// Returns the admin theme name.
	public function adminTheme()
	{
		return $this->db['adminTheme'];
	}

	// Returns the footer text.
	public function footer()
	{
		return $this->db['footer'];
	}

	// Returns the url site.
	public function url()
	{
		return $this->db['url'];
	}

	// Returns TRUE if the cli mode is enabled, otherwise FALSE.
	public function cliMode()
	{
		return $this->db['cliMode'];
	}

	// Returns the relative home link
	public function homeLink()
	{
		return HTML_PATH_ROOT;
	}

	// Returns the timezone.
	public function timezone()
	{
		return $this->db['timezone'];
	}

	// Returns posts per page.
	public function postsPerPage()
	{
		return $this->db['postsperpage'];
	}

	// Returns the current language.
	public function language()
	{
		return $this->db['language'];
	}

	// Returns the current locale.
	public function locale()
	{
		return $this->db['locale'];
	}

	// Returns the current language in short format.
	public function shortLanguage()
	{
		$locale = $this->locale();
		$explode = explode('_', $locale);
		$short = array_shift($explode);

		return $short;
	}

	// Returns the current homepage.
	public function homepage()
	{
		return $this->db['homepage'];
	}

	// Set the locale.
	public function setLocale($locale)
	{
		if(setlocale(LC_ALL, $locale.'.UTF-8')!==false) {
			return true;
		}

		if(setlocale(LC_ALL, $locale.'.UTF8')!==false) {
			return true;
		}

		return setlocale(LC_ALL, $locale);
	}

	// Set the timezone.
	public function setTimezone($timezone)
	{
		return date_default_timezone_set($timezone);
	}

}