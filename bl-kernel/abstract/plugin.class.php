<?php defined('BLUDIT') or die('Bludit CMS.');

class Plugin
{

	// (string) directory name, just the name
	// Ex: sitemap
	public $directoryName;

	// (string) Absolute database filename and path
	// Ex: /www/bludit/bl-content/plugins/sitemap/db.php
	public $filenameDb;

	// (string) Absolute metadata filename and path
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

	// (array) List of custom hooks
	public $customHooks;

	function __construct()
	{
		$this->dbFields = array();
		$this->customHooks = array();

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

		$this->filenameDb = PATH_PLUGINS_DATABASES . $this->directoryName . DS . 'db.php';

		// --- Metadata ---
		$this->filenameMetadata = PATH_PLUGINS . $this->directoryName() . DS . 'metadata.json';
		$metadataString = file_get_contents($this->filenameMetadata);
		$this->metadata = json_decode($metadataString, true);

		// If the plugin is installed then get the database
		if ($this->installed()) {
			$Tmp = new dbJSON($this->filenameDb);
			$this->db = $Tmp->db;
			$this->prepare();
		}
	}

	public function save()
	{
		$tmp = new dbJSON($this->filenameDb);
		$tmp->db = $this->db;
		return $tmp->save();
	}

	public function includeCSS($filename)
	{
		return '<link rel="stylesheet" type="text/css" href="' . $this->domainPath() . 'css/' . $filename . '?version=' . BLUDIT_VERSION . '">' . PHP_EOL;
	}

	public function includeJS($filename)
	{
		return '<script charset="utf-8" src="' . $this->domainPath() . 'js/' . $filename . '?version=' . BLUDIT_VERSION . '"></script>' . PHP_EOL;
	}

	// Returns absolute URL and path of the plugin directory
	// This function helps to include CSS or Javascript files with absolute URL
	public function domainPath()
	{
		return DOMAIN_PLUGINS . $this->directoryName . '/';
	}

	// Returns relative path of the plugin directory
	// This function helps to include CSS or Javascript files with relative URL
	public function htmlPath()
	{
		return HTML_PATH_PLUGINS . $this->directoryName . '/';
	}

	// Returns absolute path of the plugin directory
	// This function helps to include PHP libraries or some file at server level
	public function phpPath()
	{
		return PATH_PLUGINS . $this->directoryName . DS;
	}

	public function phpPathDB()
	{
		return PATH_PLUGINS_DATABASES . $this->directoryName . DS;
	}

	// Returns the value of the key from the metadata of the plugin, FALSE if the key doesn't exist
	public function getMetadata($key)
	{
		if (isset($this->metadata[$key])) {
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
	public function getValue($field, $html = true)
	{
		if (isset($this->db[$field])) {
			if ($html) {
				return $this->db[$field];
			} else {
				return Sanitize::htmlDecode($this->db[$field]);
			}
		}
		return $this->dbFields[$field];
	}

	public function label()
	{
		return $this->getMetadata('label');
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
		foreach ($compatible as $version) {
			$root = explode('.', $version);
			if ($root[0] == $bluditRoot[0] && $root[1] == $bluditRoot[1]) {
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
	public function install($position = 1)
	{
		if ($this->installed()) {
			return false;
		}

		// Create workspace
		$workspace = $this->workspace();
		mkdir($workspace, DIR_PERMISSIONS, true);

		// Create plugin directory for the database
		mkdir(PATH_PLUGINS_DATABASES . $this->directoryName, DIR_PERMISSIONS, true);

		$this->dbFields['position'] = $position;
		// Sanitize default values to store in the file
		foreach ($this->dbFields as $key => $value) {
			$value = Sanitize::html($value);
			settype($value, gettype($this->dbFields[$key]));
			$this->db[$key] = $value;
		}

		// Create the database
		return $this->save();
	}

	public function uninstall()
	{
		// Delete database
		$path = PATH_PLUGINS_DATABASES . $this->directoryName;
		Filesystem::deleteRecursive($path);

		// Delete workspace
		$workspace = $this->workspace();
		Filesystem::deleteRecursive($workspace);

		return true;
	}

	// Returns TRUE if the plugin is installed
	// This function just check if the database of the plugin is created
	public function installed()
	{
		return file_exists($this->filenameDb);
	}

	public function workspace()
	{
		return PATH_WORKSPACES . $this->directoryName . DS;
	}

	public function init()
	{
		// This method is used on children classes
		// The user can define his own field of the database
	}

	public function prepare()
	{
		// This method is used on children classes
		// The user can prepare the plugin, when it is installed
	}

	public function post()
	{
		$args = $_POST;
		foreach ($this->dbFields as $field => $value) {
			if (isset($args[$field])) {
				$finalValue = Sanitize::html($args[$field]);
				if ($finalValue === 'false') {
					$finalValue = false;
				} elseif ($finalValue === 'true') {
					$finalValue = true;
				}
				settype($finalValue, gettype($value));
				$this->db[$field] = $finalValue;
			}
		}
		return $this->save();
	}

	public function type()
	{
		return $this->getMetadata('type');
	}

	public function setField($field, $value)
	{
		$this->db[$field] = Sanitize::html($value);
		return $this->save();
	}

	public function setPosition($position)
	{
		return $this->setField('position', $position);
	}

	// Returns the parameters after the URI, FALSE if the URI doesn't match with the webhook
	// Example: https://www.mybludit.com/api/foo/bar
	public function webhook($URI = false, $returnsAfterURI = false, $fixed = true)
	{
		global $url;

		if (empty($URI)) {
			return false;
		}

		// Check URI start with the webhook
		$startString = HTML_PATH_ROOT . $URI;
		$URI = $url->uri();
		$length = mb_strlen($startString, CHARSET);
		if (mb_substr($URI, 0, $length) != $startString) {
			return false;
		}

		$afterURI = mb_substr($URI, $length);
		if (!empty($afterURI)) {
			if ($fixed) {
				return false;
			}
			if ($afterURI[0] != '/') {
				return false;
			}
		}

		if ($returnsAfterURI) {
			return $afterURI;
		}

		Log::set(__METHOD__ . LOG_SEP . 'Webhook requested.');
		return true;
	}
}
