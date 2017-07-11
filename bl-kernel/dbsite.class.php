<?php defined('BLUDIT') or die('Bludit CMS.');

class dbSite extends dbJSON
{
	public $dbFields = array(
		'title'=>		array('inFile'=>false, 'value'=>'I am Guybrush Threepwood, mighty developer'),
		'slogan'=>		array('inFile'=>false, 'value'=>''),
		'description'=>		array('inFile'=>false, 'value'=>''),
		'footer'=>		array('inFile'=>false, 'value'=>'I wanna be a pirate!'),
		'itemsPerPage'=>	array('inFile'=>false, 'value'=>6),
		'language'=>		array('inFile'=>false, 'value'=>'en'),
		'locale'=>		array('inFile'=>false, 'value'=>'en_US'),
		'timezone'=>		array('inFile'=>false, 'value'=>'America/Argentina/Buenos_Aires'),
		'theme'=>		array('inFile'=>false, 'value'=>'pure'),
		'adminTheme'=>		array('inFile'=>false, 'value'=>'default'),
		'homepage'=>		array('inFile'=>false, 'value'=>''),
		'uriPage'=>		array('inFile'=>false, 'value'=>'/'),
		'uriTag'=>		array('inFile'=>false, 'value'=>'/tag/'),
		'uriCategory'=>		array('inFile'=>false, 'value'=>'/category/'),
		'url'=>			array('inFile'=>false, 'value'=>''),
		'emailFrom'=>		array('inFile'=>false, 'value'=>''),
		'dateFormat'=>		array('inFile'=>false, 'value'=>'F j, Y'),
		'timeFormat'=>		array('inFile'=>false, 'value'=>'g:i a'),
		'currentBuild'=>	array('inFile'=>false, 'value'=>0),
		'twitter'=>		array('inFile'=>false, 'value'=>''),
		'facebook'=>		array('inFile'=>false, 'value'=>''),
		'googlePlus'=>		array('inFile'=>false, 'value'=>''),
		'instagram'=>		array('inFile'=>false, 'value'=>''),
		'github'=>		array('inFile'=>false, 'value'=>''),
		'orderBy'=>		array('inFile'=>false, 'value'=>'date') // date or position
	);

	function __construct()
	{
		parent::__construct(DB_SITE);

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
		foreach($args as $field=>$value) {
			if( isset($this->dbFields[$field]) ) {
				$this->db[$field] = Sanitize::html($value);
			}
		}

		return $this->save();
	}

	// Returns an array with the filters for the url
	// or returns a string with the filter defined on $filter
	public function uriFilters($filter='')
	{
		$filters['admin'] = ADMIN_URI_FILTER;
		$filters['page'] = $this->getField('uriPage');
		$filters['tag'] = $this->getField('uriTag');
		$filters['category'] = $this->getField('uriCategory');

		if(empty($filter)) {
			return $filters;
		}

		return $filters[$filter];
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

	public function urlCategory()
	{
		$filter = $this->getField('uriCategory');
		return $this->url().ltrim($filter, '/');

	}

	// Returns the URL of the rss.xml file
	// You need to have enabled the plugin RSS
	public function rss()
	{
		return DOMAIN_BASE.'rss.xml';
	}

	// Returns the URL of the sitemap.xml file
	// You need to have enabled the plugin Sitemap
	public function sitemap()
	{
		return DOMAIN_BASE.'sitemap.xml';
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

	public function orderBy()
	{
		return $this->getField('orderBy');
	}

	public function pageError()
	{
		return $this->getField('pageError');
	}

	public function pageAbout()
	{
		return $this->getField('pageAbout');
	}

	public function pageContact()
	{
		return $this->getField('pageContact');
	}

	// Returns the site title
	public function title()
	{
		return $this->getField('title');
	}

	// Returns the site slogan
	public function slogan()
	{
		return $this->getField('slogan');
	}

	// Returns the site description
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

	// Returns the site theme name
	public function theme()
	{
		return $this->getField('theme');
	}

	// Returns the admin theme name
	public function adminTheme()
	{
		return $this->getField('adminTheme');
	}

	// Returns the footer text
	public function footer()
	{
		return $this->getField('footer');
	}

	// Returns the full domain and base url
	// For example, https://www.domain.com/bludit
	public function url()
	{
		return $this->getField('url');
	}

	// Returns the protocol and the domain, without the base url
	// For example, http://www.domain.com
	public function domain()
	{
		// If the URL field is not set, try detect the domain.
		if(Text::isEmpty( $this->url() )) {
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

	// Returns the amount of pages per page
	public function itemsPerPage()
	{
		return $this->getField('itemsPerPage');
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

	// Returns the current homepage, FALSE if not defined homepage
	public function homepage()
	{
		$homepage = $this->getField('homepage');
		if( empty($homepage) ) {
			return false;
		}
		return $homepage;
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