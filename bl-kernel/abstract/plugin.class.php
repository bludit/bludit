<?php defined('BLUDIT') or die('Bludit CMS.');

class Plugin {

	// (string) directory name, just the name
	// Ex: sitemap
	public $directoryName;

	// (string) Absoulute database filename and path
	// Ex: /www/bludit/bl-content/plugins/sitemap/db.php
	public $filenameDb;

	// (string) Absoulute metadata filename and path
	// Ex: /www/bludit/bl-plugins/sitemap/metadata.json
	public $filenameMetadata;

	// (array) Plugin metadata
	// Ex: array('author'=>'',...., 'notes'=>'')
	public $metadata;

	// (string) Class name
	// Ex: pluginSitemap
	public $className;

	// (array) Database unserialized
	public $db;

	// (array) Database fields, only for initialize
	public $dbFields;

	// (boolean) Enable or disable default Save and Cancel button on plugin settings
	public $formButtons;

	function __construct()
	{
		$this->dbFields = array();

		$reflector = new ReflectionClass(get_class($this));

		// Directory name
		$this->directoryName = basename(dirname($reflector->getFileName()));

		// Class Name
		$this->className = $reflector->getName();

		$this->formButtons = true;

		// Call the method init() from the children
		$this->init();

		// Init empty database with default values
		$this->db = $this->dbFields;

		$this->filenameDb = PATH_PLUGINS_DATABASES.$this->directoryName.DS.'db.php';

		// --- Metadata ---
		$this->filenameMetadata = PATH_PLUGINS.$this->directoryName().DS.'metadata.json';
		$metadataString = file_get_contents($this->filenameMetadata);
		$this->metadata = json_decode($metadataString, true);

		// If the plugin is installed then get the database
		if($this->installed()) {
			$Tmp = new dbJSON($this->filenameDb);
			$this->db = $Tmp->db;
		}
	}

	// DEPRECATED
	// 2017-06-19
	public function setDb($args)
	{
		foreach($this->dbFields as $key=>$value) {
			if( isset($args[$key]) ) {
				$value = Sanitize::html( $args[$key] );
				if($value==='false') { $value = false; }
				elseif($value==='true') { $value = true; }
				settype($value, gettype($this->dbFields[$key]));
				$this->db[$key] = $value;
			}
		}

		$this->save();
	}

	public function save()
	{
		$tmp = new dbJSON($this->filenameDb);
		$tmp->db = $this->db;
		return $tmp->save();
	}

	public function htmlPath()
	{
		return HTML_PATH_PLUGINS.$this->directoryName.'/';
	}

	public function phpPath()
	{
		return PATH_PLUGINS.$this->directoryName.DS;
	}

	public function phpPathDB()
	{
		return PATH_PLUGINS_DATABASES.$this->directoryName.DS;
	}

	// Returns the value of the key from the metadata of the plugin, FALSE if the key doen't exit
	public function getMetadata($key)
	{
		if(isset($this->metadata[$key])) {
			return $this->metadata[$key];
		}

		return false;
	}

	// Set a key / value on the metadata of the plugin
	public function setMetadata($key, $value)
	{
		$this->metadata[$key] = $value;
		return true;
	}

	// Returns the value of the field from the database
	// (string) $field
	// (boolean) $html, TRUE returns the value sanitized, FALSE unsanitized
	public function getValue($field, $html=true)
	{
		if( isset($this->db[$field]) ) {
			if($html) {
				return $this->db[$field];
			}
			else {
				return Sanitize::htmlDecode($this->db[$field]);
			}
		}
		return false;
	}

	// DEPRECATED
	// 2017-06-16
	public function getDbField($key, $html=true)
	{
		if(isset($this->db[$key])) {

			if($html) {
				// All fields from DBField are sanitized.
				return $this->db[$key];
			}
			else {
				// Decode HTML tags, this action unsanitized the variable.
				return Sanitize::htmlDecode($this->db[$key]);
			}
		}

		return '';
	}

	public function name()
	{
		return $this->getMetadata('name');
	}

	public function description()
	{
		return $this->getMetadata('description');
	}

	public function author()
	{
		return $this->getMetadata('author');
	}

	public function email()
	{
		return $this->getMetadata('email');
	}

	public function website()
	{
		return $this->getMetadata('website');
	}

	public function position()
	{
		return $this->getValue('position');
	}

	public function version()
	{
		return $this->getMetadata('version');
	}

	public function releaseDate()
	{
		return $this->getMetadata('releaseDate');
	}

	public function className()
	{
		return $this->className;
	}

	public function formButtons()
	{
		return $this->formButtons;
	}

	public function isCompatible()
	{
		$bluditRoot = explode('.', BLUDIT_VERSION);
		$compatible = explode(',', $this->getMetadata('compatible'));
		foreach( $compatible as $version ) {
			$root = explode('.', $version);
			if( $root[0]==$bluditRoot[0] && $root[1]==$bluditRoot[1] ) {
				return true;
			}
		}
		return false;
	}

	public function directoryName()
	{
		return $this->directoryName;
	}

	// Return TRUE if the installation success, otherwise FALSE.
	public function install($position=1)
	{
		if($this->installed()) {
			return false;
		}

		// Create plugin directory for databases and other files
		mkdir(PATH_PLUGINS_DATABASES.$this->directoryName, 0755, true);

		// Create database
		$this->dbFields['position'] = $position;
		$this->setDb($this->dbFields);

		return true;
	}

	public function uninstall()
	{
		$path = PATH_PLUGINS_DATABASES.$this->directoryName;
		return Filesystem::deleteRecursive($path);
	}

	public function installed()
	{
		return file_exists($this->filenameDb);
	}

	public function workspace()
	{
		return PATH_PLUGINS_DATABASES.$this->directoryName.DS;
	}

	public function init()
	{
		// This method is used on childre classes.
		// The user can define his own field of the database
	}

	public function post()
	{
		$args = $_POST;
		foreach($this->dbFields as $key=>$value) {
			if( isset($args[$key]) ) {
				$value = Sanitize::html( $args[$key] );
				if($value==='false') { $value = false; }
				elseif($value==='true') { $value = true; }
				settype($value, gettype($this->dbFields[$key]));
				$this->db[$key] = $value;
			}
		}
		return $this->save();
	}

	// Returns the parameters after the URI, FALSE if the URI doesn't match with the webhook
	// Example: https://www.mybludit.com/api/foo/bar
	public function webhook($URI=false, $returnsAfterURI=false, $fixed=true)
	{
		global $Url;

		if (empty($URI)) {
			return false;
		}

		// Check URI start with the webhook
		$startString = HTML_PATH_ROOT.$URI;
		$URI = $Url->uri();
		$length = mb_strlen($startString, CHARSET);
		if (mb_substr($URI, 0, $length)!=$startString) {
			return false;
		}

		$afterURI = mb_substr($URI, $length);
		if (!empty($afterURI)) {
			if ($fixed) {
				return false;
			}
			if ($afterURI[0]!='/') {
				return false;
			}
		}

		if ($returnsAfterURI) {
			return $afterURI;
		}

		Log::set(__METHOD__.LOG_SEP.'Webhook requested.');
		return true;
	}

}