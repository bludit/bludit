<?php

class pluginUpdater extends Plugin {

	// Define if the extension zip is loaded
	private $zip = false;
	private $urlLatestVersionFile = 'https://';
	private $localLatestVersionFile = '';

	public function init()
	{
		// Disable default form buttons (Save and Cancel)
		$this->formButtons = false;

		// Check for zip extension installed
		$this->zip = extension_loaded('zip');
		
		// Local full path of the file of the latest version of Bludit
		$this->localLatestVersionFile = $this->workspace().DS.'bludit-latest.zip';
	}

	// Redefine workspace
	public function workspace()
	{
		return PATH_CONTENT.'updater'.DS;
	}

	// Install the plugin and create the workspace directory
	public function install($position=0)
	{
		parent::install($position);
		$workspace = $this->workspace();
		return mkdir($workspace, 0755, true);
	}

	// Uninstall the plugin and delete the workspace directory
	public function uninstall()
	{
		parent::uninstall();
		$workspace = $this->workspace();
		return Filesystem::deleteRecursive($workspace);
	}

	// Check if the root directory is writable
	public function isWritable()
	{
		return is_writable(PATH_ROOT);
	}

	// Create a copy of all the system and compress it
	// Returns the name of the backup directory
	public function makeFullBackup()
	{
		$currentDate = Date::current(BACKUP_DATE_FORMAT);
		$backupDirectory = $this->workspace().$currentDate;

		// Copy all files from PATH_ROOT to $backupDirectory, also omit the directory $backupDirectory
		Filesystem::copyRecursive(PATH_ROOT, $backupDirectory, $backupDirectory);

		// Compress the backup directory
		if (Filesystem::zip($backupDirectory, $backupDirectory.'.zip')) {
			Filesystem::deleteRecursive($backupDirectory);
		}

		return $backupDirectory;
	}

	// Download the latest version of Bludit
	public function downloadLatestVersion()
	{
		return TCP::download($this->urlLatestVersionFile, $this->localLatestVersionFile);
	}

	public function validChecksum()
	{
		// IMPLEMENT !!!	
		return true;
	}

	// Unzip the latest version and replace the old files
	public function upgradeFiles()
	{
		return Filesystem::unzip($this->localLatestVersionFile, PATH_ROOT);
	}

	public function post()
	{
		if (isset($_POST['updateNow'])) {
			echo 'Making a backup';
			$this->makeFullBackup();
			
			echo 'Downloading the latest version of Bludit';
			$this->downloadLatestVersion();
			
			echo 'Validating checksum';
			if ($this->validChecksum()) {
				echo 'Updating files';
				return $this->upgradeFiles();
			}
		}

		return false;
	}

	public function form()
	{
		global $Language;

		if ($this->zip===false) {
			//return '<div class="alert alert-success">The extension zip file is not installed, to use this plugin you need install the extension first.</div>';
		}

		$html  = '<div>';
		$html .= '<button name="updateNow" value="true" class="btn btn-primary" type="submit">'.$Language->get('Update Now').'</button>';
		$html .= '</div>';

		return $html;
	}

}
