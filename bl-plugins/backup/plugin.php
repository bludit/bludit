<?php

class pluginBackup extends Plugin {

	// Directories to backup
	private $directoriesToBackup = array(
		PATH_PAGES,
		PATH_DATABASES,
		PATH_UPLOADS
	);

	// This variable define if the extension zip is loaded
	private $zip = false;

	// The last request status
	private $lastStatus = null;

	// The last request message
	private $lastMessage = null;

	public function init()
	{
		// Disable default form buttons (Save and Cancel)
		$this->formButtons = false;

		// Check for zip extension installed
		$this->zip = extension_loaded('zip');

		// Get Last Message
		if (empty($_POST) && !empty(Session::get("BACKUP-MESSAGE"))) {
			$this->lastStatus = Session::get("BACKUP-STATUS");
			$this->lastMessage = Session::get("BACKUP-MESSAGE");
			unset($_SESSION["s_BACKUP-STATUS"]);
			unset($_SESSION["s_BACKUP-MESSAGE"]);
		}
	}

	protected function response($status, $message)
	{
		// Return JSON object for AJAX requests
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], "xmlhttprequest") === 0) {
			$http = array(
				200 => "200 OK",
				400	=> "400 Bad Request",
				415 => "415 Unsupported Media Type"
			);
			header("HTTP/1.1 " . $http[$status]);
			print(json_encode(array(
				"status" => $status < 400,
				"message" => $message
			)));
			die();
		}

		// Store in Session
		Session::set("BACKUP-STATUS", $status < 400);
		Session::set("BACKUP-MESSAGE", $message);
		return $status < 400;
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

		if (isset($_FILES['backupFile'])) {
			return $this->uploadBackup($_FILES['backupFile']);
		}

		return false;
	}

	public function adminHead()
	{
		global $url;

		if ($url->slug() !== "configure-plugin/pluginBackup") {
			return false;
		}

		$html = $this->includeJS('backup.js');

		return $html;
	}

	public function adminSidebar()
	{
		global $login;
		if ($login->role() === 'admin') {
			$backups = $this->backupList();
			return '<a class="nav-link" href="'.HTML_PATH_ADMIN_ROOT.'configure-plugin/'.$this->className().'">Backups <span class="badge badge-primary badge-pill">'.count($backups).'</span></a>';
		} else {
			return '';
		}
	}

	public function form()
	{
		global $L;

		$backups = $this->backupList();

		$html = '';
		if (empty($backups)) {
			$html .= '<div class="alert alert-primary" role="alert">';
			$html .= $L->get('There are no backups for the moment');
			$html .= '</div>';
		}

		if($this->lastStatus !== null) {
			$html .= '<div class="alert alert-' . ($this->lastStatus? "success": "danger") . '" role="alert">';
			$html .= $this->lastMessage;
			$html .= '</div>';
		}

		$html .= '<div class="row">';
		$html .= '<div class="col text-left">';
		$html .= '<button name="createBackup" value="true" class="btn btn-primary" type="submit"><span class="fa fa-play-circle"></span> '.$L->get('create-backup').'</button>';
		$html .= '</div>';
		$html .= '<div class="col-5 text-right">';
		if ($this->zip) {
			$html .= '<input id="backupFile" type="file" name="backupFile" value="" style="position:absolute;top:-500em;" />';
			$html .= '<label for="backupFile" value="true" class="btn btn-light d-inline-block" type="submit" style="margin-top:0 !important;"><span class="fa fa-upload"></span> '.$L->get('upload-backup').'</label>';
		}
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<hr>';

		foreach ($backups as $backup) {
			$filename = pathinfo($backup,PATHINFO_FILENAME);
			$basename = pathinfo($backup,PATHINFO_BASENAME);

			// Format Title
			list($name, $count) = array_pad(explode(".", $filename, 2), 2, 0);
			if (($temp = Date::format($name, BACKUP_DATE_FORMAT, 'F j, Y, g:i a')) !== false) {
				$name = $temp;
			}

			$html .= '<div>';
			$html .= '<h4 class="font-weight-normal">'.$name.($count > 0? " ($count)": "").'</h4>';
			// Allow download if a zip file
			if ($this->zip) {
				$html .= '<a class="btn btn-outline-secondary btn-sm mr-1 mt-1" href="'.DOMAIN_BASE.'plugin-backup-download?file='.$filename.'.zip"><span class="fa fa-download"></span> '.$L->get('download').'</a>';
			}
			$html .= '<button name="restoreBackup" value="'.$filename.'" class="btn btn-outline-secondary btn-sm mr-1 mt-1" type="submit"><span class="fa fa-rotate-left"></span> '.$L->get('restore-backup').'</button>';
			$html .= '<button name="deleteBackup" value="'.$filename.'" class="btn btn-outline-danger btn-sm mr-1 mt-1" type="submit"><span class="fa fa-trash"></span> '.$L->get('delete-backup').'</button>';
			$html .= '</div>';
			$html .= '<hr>';
		}
		return $html;
	}

	/**
	 * Downloading Backups is not allowed by default server config
	 * This webhook is to allow downloads for admins
	 * Webhook: plugin-backup-download?file={backup-name.zip}
	 */
	public function beforeAll()
	{
		global $L;
		$webhook = 'plugin-backup-download';
		if ($this->webhook($webhook)) {
			if (!empty($_GET['file'])) {
				$login = new Login();
				if ($login->role() === 'admin') {
					$existingBackups = array_map('basename', glob(PATH_WORKSPACES.'backup/*.zip'));
					if (in_array($_GET['file'], $existingBackups)) {
						downloadRestrictedFile(PATH_WORKSPACES.'backup/'.$_GET['file']);
					}
				} else {
					Alert::set($L->g('You do not have sufficient permissions'));
					Redirect::page('dashboard');
				}
			}
			exit(0);
		}
	}

	public function backupList()
	{
		if ($this->zip) {
			$backups = Filesystem::listFiles($this->workspace(), '*', 'zip', true);
		} else {
			$backups = Filesystem::listDirectories($this->workspace(), '*', true);
		}
		return $backups;
	}

	public function createBackup()
	{
		global $L;

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

			// Add validation file
			$zip = new ZipArchive();
			$zip->open($backupDir.'.zip');
			$zip->addFromString('.BLUDIT_BACKUP', md5_file($backupDir.'.zip'));
			$zip->close();
		}

		if (file_exists($backupDir.'.zip')) {
			return $this->response(200, $L->get("The Backup was created successfully."));
		}

		return $this->response(400, $L->get("The Backup file could not be created."));
	}

	public function validateBackup($filename)
	{
		$tmp = PATH_TMP.'backup-'.time().'.zip';
		copy($filename, $tmp);

		// Check Archive
		$zip = new ZipArchive();
		if($zip->open($tmp) !== true) {
			unlink($tmp);
			return false;
		}

		// Check Basic Folders
		if ($zip->addEmptyDir("databases") || $zip->addEmptyDir("pages") || $zip->addEmptyDir("uploads")) {
			$zip->close();
			unlink($tmp);
			return false;
		}

		// Check Checksum
		if (($checksum = $zip->getFromName(".BLUDIT_BACKUP")) === false) {
			$zip->close();
			unlink($tmp);
			return false;
		}
		$zip->deleteName(".BLUDIT_BACKUP");
		$zip->close();
		$check = $checksum === md5_file($tmp);

		// Return
		unlink($tmp);
		return $check;
	}

	public function restoreBackup($filename)
	{
		global $L;

		// Remove current files
		foreach ($this->directoriesToBackup as $dir) {
			Filesystem::deleteRecursive($dir);
		}

		// Recover backuped files
		if ($this->zip) {
			// Zip format
			$tmp = $this->workspace().$filename.'.zip';
			$status = Filesystem::unzip($tmp, PATH_CONTENT);
		} else {
			// Directory format
			$tmp = $this->workspace().$filename;
			$status = Filesystem::copyRecursive($tmp, PATH_CONTENT);
		}

		if ($status) {
			return $this->response(200, sprintf($L->get("The Backup '%s' could be restored successfully."), $filename));
		}
		return $this->response(400, sprintf($L->get("The Backup '%s' could not be restored."), $filename));
	}

	public function deleteBackup($filename)
	{
		global $L;

		if ($this->zip) {
			// Zip format
			$tmp = $this->workspace().$filename.'.zip';
			$status = Filesystem::rmfile($tmp);
		} else {
			// Directory format
			$tmp = $this->workspace().$filename;
			$status = Filesystem::deleteRecursive($tmp);
		}

		if ($status) {
			return $this->response(200, sprintf($L->get("The Backup '%s' could be deleted successfully."), $filename));
		}
		return $this->response(400, sprintf($L->get("The Backup '%s' could not be deleted."), $filename));
	}

	public function uploadBackup($backup)
	{
		global $L;

		// Check File Type
		if ($backup["type"] !== "application/zip" && $backup["type"] !== "application/x-zip-compressed") {
			return $this->response(415, $L->get("The passed file is not a valid ZIP Archive."));
		}

		// Check File Extension
		if (stripos($backup["name"], ".zip") !== (strlen($backup["name"]) - 4)) {
			return $this->response(415, $L->get("The passed file does not end with .zip."));
		}

		// Check ZIP extension
		if(!$this->zip) {
			return $this->response(400, $L->get("The passed file could not be validated."));
		}

		// Validate Backup ZIP
		if (!$this->validateBackup($backup["tmp_name"])) {
			return $this->response(415, $L->get("The passed file is not a valid backup archive."));
		}

		// File Name
		$name = $backup["name"];
		$count = 0;
		while (file_exists($this->workspace() . $name)) {
			$name = substr($backup["name"], 0, -4) . "." . ++$count . ".zip";
		}

		// Move File to Backup Directory
		Filesystem::mv($backup["tmp_name"], $this->workspace() . $name);
		return $this->response(200, $L->get("The backup file could be uploaded successfully."));
	}
}
