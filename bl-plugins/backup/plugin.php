<?php

class pluginBackup extends Plugin {

	// List of directories to backup
	private $directoriesToBackup = array(
		PATH_PAGES,
		PATH_DATABASES,
		PATH_UPLOADS
	);

	// This variable define if the extension zip is loaded
	private $zip = false;

	public function init()
	{
		// Disable default form buttons (Save and Cancel)
		$this->formButtons = false;

		// Check for zip extension installed
		$this->zip = extension_loaded('zip');
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

	public function post()
	{
		if (isset($_POST['createBackup'])) {
			return $this->createBackup();
		} elseif (isset($_POST['restoreBackup'])) {
			return $this->restoreBackup($_POST['restoreBackup']);
		} elseif (isset($_POST['deleteBackup'])) {
			return $this->deleteBackup($_POST['deleteBackup']);
		}

		return false;
	}

	public function form()
	{
		global $Language;

		$backups = Filesystem::listDirectories($this->workspace(), '*', true);
		if ($this->zip) {
			$backups = Filesystem::listFiles($this->workspace(), '*', 'zip', true);
		}

		$html  = '<div>';
		$html .= '<button name="createBackup" value="true" class="left small blue" type="submit"><i class="uk-icon-plus"></i> '.$Language->get('create-backup').'</button>';
		$html .= '</div>';
		$html .= '<hr>';

		foreach ($backups as $backup) {
			$filename = pathinfo($backup,PATHINFO_FILENAME);
			$basename = pathinfo($backup,PATHINFO_BASENAME);

			$html .= '<div>';
			$html .= '<h3>'.Date::format($filename, BACKUP_DATE_FORMAT, 'F j, Y, g:i a').'</h3>';
			// Allow download if a zip file
			if ($this->zip) {
				$html .= '<a class="uk-button small left blue" href="'.DOMAIN_CONTENT.'backup/'.$filename.'.zip"><i class="uk-icon-download"></i> '.$Language->get('download').'</a>';
			}
			$html .= '<button name="restoreBackup" value="'.$filename.'" class="uk-button small left" type="submit"><i class="uk-icon-clock-o"></i> '.$Language->get('restore-backup').'</button>';
			$html .= '<button name="deleteBackup"  value="'.$filename.'" class="uk-button small left" type="submit"><i class="uk-icon-trash-o"></i> '.$Language->get('delete-backup').'</button>';
			$html .= '</div>';
			$html .= '<hr>';
		}
		return $html;
	}

	public function createBackup()
	{
		// Current backup directory
		$currentDate = Date::current(BACKUP_DATE_FORMAT);
		$backupDir = $this->workspace().$currentDate;

		// Copy directories to backup directory
		// $directoriesToBackup is a private variable of this class
		foreach ($this->directoriesToBackup as $dir) {
			$destination = $backupDir.DS.basename($dir);
			Filesystem::copyRecursive($dir, $destination);
		}

		// Compress backup directory
		if ($this->zip) {
			if (Filesystem::zip($backupDir, $backupDir.'.zip')) {
				Filesystem::deleteRecursive($backupDir);
			}
		}

		return true;
	}

	public function restoreBackup($filename)
	{
		// Remove current files
		foreach ($this->directoriesToBackup as $dir) {
			Filesystem::deleteRecursive($dir);
		}

		// Recover backuped files
		// Zip format
		if ($this->zip) {
			$tmp = $this->workspace().$filename.'.zip';
			return Filesystem::unzip($tmp, PATH_CONTENT);
		}

		// Directory format
		$tmp = $this->workspace().$filename;
		return Filesystem::copyRecursive($tmp, PATH_CONTENT);
	}

	public function deleteBackup($filename)
	{
		// Zip format
		if ($this->zip) {
			$tmp = $this->workspace().$filename.'.zip';
			return Filesystem::rmfile($tmp);
		}

		// Directory format
		$tmp = $this->workspace().$filename;
		return Filesystem::deleteRecursive($tmp);
	}
}