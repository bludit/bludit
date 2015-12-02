<?php defined('BLUDIT') or die('Bludit CMS.');

class Plugin {

	// (string) Plugin's directory name
	public $directoryName;

	// (string) Database path and filename
	public $filenameDb;

	// (array) Database unserialized
	public $db;

	// (array) Database fields, only for initialize.
	public $dbFields;

	// (string) Plugin's class name.
	public $className;

	// (array) Plugin's information.
	public $data;

	function __construct()
	{
		$this->data = array(
			'name'=>'',
			'description'=>'',
			'author'=>'',
			'email'=>'',
			'website'=>'',
			'version'=>'',
			'releaseDate'=>''
		);

		$this->dbFields = array();

		$reflector = new ReflectionClass(get_class($this));

		// Directory name
		$this->directoryName = basename(dirname($reflector->getFileName()));

		// Class Name
		$this->className = $reflector->getName();

		// Initialize dbFields from the children.
		$this->init();

		// Init empty database
		$this->db = $this->dbFields;

		$this->filenameDb = PATH_PLUGINS_DATABASES.$this->directoryName.DS.'db.php';

		// If the plugin installed then get the database.
		if($this->installed())
		{
			$Tmp = new dbJSON($this->filenameDb);
			$this->db = $Tmp->db;
		}
	}

	public function htmlPath()
	{
		return HTML_PATH_PLUGINS.$this->directoryName.'/';
	}

	// Returns the item from plugin-data.
	public function getData($key)
	{
		if(isset($this->data[$key])) {
			return $this->data[$key];
		}

		return '';
	}

	public function setData($array)
	{
		$this->data = $array;
	}

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

	public function setDb($array)
	{
		$tmp = array();

		// All fields will be sanitize before save.
		foreach($array as $key=>$value) {
			$tmp[$key] = Sanitize::html($value);
		}

		$this->db = $tmp;

		// Save db on file
		$Tmp = new dbJSON($this->filenameDb);
		$Tmp->db = $tmp;
		$Tmp->save();
	}

	public function name()
	{
		return $this->getData('name');
	}

	public function description()
	{
		return $this->getData('description');
	}

	public function author()
	{
		return $this->getData('author');
	}

	public function email()
	{
		return $this->getData('email');
	}

	public function website()
	{
		return $this->getData('website');
	}

	public function version()
	{
		return $this->getData('version');
	}

	public function releaseDate()
	{
		return $this->getData('releaseDate');
	}

	public function className()
	{
		return $this->className;
	}

	public function directoryName()
	{
		return $this->directoryName;
	}

	// Return TRUE if the installation success, otherwise FALSE.
	public function install($position=0)
	{
		if($this->installed()) {
			return false;
		}

		// Create plugin directory for databases and others files.
		mkdir(PATH_PLUGINS_DATABASES.$this->directoryName, 0755, true);

		// Create database
		$this->dbFields['position'] = $position;
		$this->setDb($this->dbFields);

		return true;
	}

	public function uninstall()
	{
		unlink($this->filenameDb);
		rmdir(PATH_PLUGINS_DATABASES.$this->directoryName);
	}

	public function installed()
	{
		return file_exists($this->filenameDb);
	}

	public function init()
	{
		// This method is used on childre classes.
		// The user can define your own dbFields.
	}

}
