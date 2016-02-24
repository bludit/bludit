<?php defined('BLUDIT') or die('Bludit CMS.');

class dbSite extends dbJSON
{
	public $dbFields = array(
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
		'uriBlog'=>		array('inFile'=>false, 'value'=>'/blog/'),
		'url'=>			array('inFile'=>false, 'value'=>''),
		'cliMode'=>		array('inFile'=>false, 'value'=>true),
		'emailFrom'=>		array('inFile'=>false, 'value'=>''),
		'dateFormat'=>		array('inFile'=>false, 'value'=>'F j, Y'),
		'timeFormat'=>		array('inFile'=>false, 'value'=>'g:i a'),
		'currentBuild'=>	array('inFile'=>false, 'value'=>0),
		'twitter'=>		array('inFile'=>false, 'value'=>''),
		'facebook'=>		array('inFile'=>false, 'value'=>''),
		'googlePlus'=>		array('inFile'=>false, 'value'=>''),
		'instagram'=>		array('inFile'=>false, 'value'=>''),
		'github'=>		array('inFile'=>false, 'value'=>'')
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
		$filters['post'] = $this->getField('uriPost');
		$filters['page'] = $this->getField('uriPage');
		$filters['tag'] = $this->getField('uriTag');
		$filters['blog'] = $this->getField('uriBlog');

		if(empty($filter)) {
			return $filters;
		}

		return $filters[$filter];
	}

	public function urlPost()
	{
		$filter = $this->getField('uriPost');
		return $this->url().ltrim($filter, '/');
	}

	public function urlPage()
	{
		$filter = $this->getField('uriPage');
		return $this->url().ltrim($filter, '/');
	}

	public function urlTag()
	{
		$filter = $this->getField('uriTag');
		return $this->url().ltrim($filter, '/');
	}

	public function urlBlog()
	{
		$filter = $this->getField('uriBlog');
		return $this->url().ltrim($filter, '/');
	}

	public function twitter()
	{
		return $this->getField('twitter');
	}

	public function facebook()
	{
		return $this->getField('facebook');
	}

	public function instagram()
	{
		return $this->getField('instagram');
	}

	public function github()
	{
		return $this->getField('github');
	}

	public function googlePlus()
	{
		return $this->getField('googlePlus');
	}

	// Returns the site title.
	public function title()
	{
		return $this->getField('title');
	}

	// Returns the site slogan.
	public function slogan()
	{
		return $this->getField('slogan');
	}

	// Returns the site description.
	public function description()
	{
		return $this->getField('description');
	}

	public function emailFrom()
	{
		return $this->getField('emailFrom');
	}

	public function dateFormat()
	{
		return $this->getField('dateFormat');
	}

	public function timeFormat()
	{
		return $this->getField('timeFormat');
	}

	// Returns the site theme name.
	public function theme()
	{
		return $this->getField('theme');
	}

	// Returns the admin theme name.
	public function adminTheme()
	{
		return $this->getField('adminTheme');
	}

	// Returns the footer text.
	public function footer()
	{
		return $this->getField('footer');
	}

	// Returns the full domain and base url.
	// For example, http://www.domain.com/bludit/
	public function url()
	{
		return $this->getField('url');
	}

	// Returns the protocol and the domain, without the base url.
	// For example, http://www.domain.com
	public function domain()
	{
		// If the URL field is not set, try detect the domain.
		if(Text::isEmpty( $this->url() ))
		{
			if(!empty($_SERVER['HTTPS'])) {
				$protocol = 'https://';
			}
			else {
				$protocol = 'http://';
			}

			$domain = trim($_SERVER['HTTP_HOST'], '/');

			return $protocol.$domain;
		}

		// Parse the domain from the field URL.
		$parse = parse_url($this->url());

		$domain = trim($parse['host'], '/');

		return $parse['scheme'].'://'.$domain;
	}

	// Returns TRUE if the cli mode is enabled, otherwise FALSE.
	public function cliMode()
	{
		return $this->getField('cliMode');
	}

	// Returns the relative home link
	public function homeLink()
	{
		return HTML_PATH_ROOT;
	}

	// Returns the timezone.
	public function timezone()
	{
		return $this->getField('timezone');
	}

	// Returns the current build / version of Bludit.
	public function currentBuild()
	{
		return $this->getField('currentBuild');
	}

	// Returns posts per page.
	public function postsPerPage()
	{
		return $this->getField('postsperpage');
	}

	// Returns the current language.
	public function language()
	{
		return $this->getField('language');
	}

	// Returns the current locale.
	public function locale()
	{
		return $this->getField('locale');
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
		return $this->getField('homepage');
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