<?php

class Plugin {

	// (string) Plugin's directory
	public $directoryName;
	
	// (string) Database path and filename
	public $fileDb;
	
	// (array) Database
	public $db;

	// (array) Database fields, only for initialize.
	public $dbFields;

	public $className;

	function __construct()
	{
		$reflector = new ReflectionClass(get_class($this));

		// Directory name
		$this->directoryName = basename(dirname($reflector->getFileName()));

		// Class Name
		$this->className = $reflector->getName();

		// Init empty database
		$this->db = array();

		$this->fileDb = PATH_PLUGINS_DATABASES.$this->directoryName.'/db.php';

		// If the plugin installed then get the database.
		if($this->installed())
		{
			$Tmp = new DB_SERIALIZE($this->fileDb);
			$this->db = $Tmp->vars;
		}
	}

	// Return TRUE if the installation success, otherwise FALSE.
	public function install()
	{
		if($this->installed())
			return false;

		// Create plugin directory for databases and others files.
		if( !mkdir(PATH_PLUGINS_DATABASES.$this->directoryName, 0755, true) )
			return false;

		if( !empty($this->dbFields) )
		{
			$Tmp = new DB_SERIALIZE($this->fileDb);
			$Tmp->setDb($this->dbFields);
		}

		return true;
	}

	public function uninstall()
	{

	}

	public function installed()
	{
		return file_exists($this->fileDb);
	}

	public function init()
	{
		
	}	

	// DEBUG: Ver si se usa
	public function showdb()
	{
		print_r( $this->db );
	}

	// EVENTS

	// Before the posts load.
	public function beforePostsLoad()
	{
		return false;
	}

	// After the posts load.
	public function afterPostsLoad()
	{
		return false;
	}

	// Before the pages load.
	public function beforePagesLoad()
	{
		return false;
	}

	// After the pages load.
	public function afterPagesLoad()
	{
		return false;
	}

	public function onSiteHead()
	{
		return false;
	}

	public function onSiteBody()
	{
		return false;	
	}

	public function onSidebar()
	{
		return false;	
	}


}

?>
