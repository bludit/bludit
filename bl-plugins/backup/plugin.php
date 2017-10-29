<?php

class pluginBackup extends Plugin {

	public function init()
	{
		// Disable default form buttons (Save and Cancel)
		$this->formButtons = false;
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

	// Redefine workspace
	public function workspace()
	{
		return PATH_CONTENT.'backup'.DS;
	}

	public function form()
	{
		$this->createBackup();
	}

	public function createBackup()
	{
		$currentDate = Date::current(BACKUP_DATE_FORMAT);

		// Create backup directory with the current date
		$tmp = $this->workspace().'backup-'.$currentDate;

		// Copy pages directory
		$destination = $tmp.DS.'pages';
		mkdir($destination, 0755, true);
		$source = rtrim(PATH_PAGES, '/');
		Filesystem::copyRecursive($source, $destination);

		// Copy databases directory
		$destination = $tmp.DS.'databases';
		mkdir($destination, 0755, true);
		$source = rtrim(PATH_DATABASES, '/');
		Filesystem::copyRecursive($source, $destination);

		// Copy uploads directory
		$destination = $tmp.DS.'uploads';
		mkdir($destination, 0755, true);
		$source = rtrim(PATH_UPLOADS, '/');
		Filesystem::copyRecursive($source, $destination);

		// Compress backup directory
		if (Filesystem::zip($tmp, $tmp.'.zip')) {
			Filesystem::deleteRecursive($tmp);
		}
	}

	// Copy the content from the backup to /bl-content/
	private function replaceContent($idExecution)
	{
		$source = $this->workspace().$idExecution;
		$dest = rtrim(PATH_CONTENT, '/');
		return Filesystem::copyRecursive($source, $dest);
	}

	// Delete old backups until the $idExecution
	private function cleanUp($idExecution)
	{
		$backups = $this->getBackupsDirectories();
		foreach ($backups as $dir) {
			$backupIDExecution = basename($dir);
			Filesystem::deleteRecursive($dir);
			if($backupIDExecution==$idExecution) {
				return true;
			}
		}
		return true;
	}

	// Returns array with all backups directories sorted by date newer first
	private function getBackupsDirectories()
	{
		$workspace = $this->workspace();
		return Filesystem::listDirectories($workspace, $regex='*', $sortByDate=true);
	}
}