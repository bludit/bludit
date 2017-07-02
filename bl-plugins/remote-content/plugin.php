<?php

class pluginRemoteContent extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'source'=>'localbluditv2.com/bl-content.zip',
			'webhook'=>'remote-content-webhook'
		);
	}

	public function install($position=0)
	{
		parent::install($position);
		$workspace = $this->workspace();
		mkdir($workspace, 0755, true);
	}

	public function uninstall()
	{
		parent::uninstall();
		$workspace = $this->workspace();
		Filesystem::deleteRecursive($workspace);
	}

	public function beforeRulesLoad()
	{
		if( $this->webhook() ) {
			$this->getFile();
			$this->updateContent();
			$this->cleanUp();
			exit();
		}
	}

	private function workspace()
	{
		return PATH_CONTENT.'remotecontent'.DS;
	}

	private function webhook()
	{
		global $Url;

		// Check URI start with the webhook
		$webhook = $this->getValue('webhook');
		if( empty($webhook) ) {
			return false;
		}
		$startString = HTML_PATH_ROOT.$webhook;
		$URI = $Url->uri();
		$length = mb_strlen($startString, CHARSET);
		if( mb_substr($URI, 0, $length)!=$startString ) {
			return false;
		}

		Log::set('Plugin Remote Content'.LOG_SEP.'Webhook request.');

		return true;
	}

	private function cleanUp()
	{
		Log::set('Plugin Remote Content'.LOG_SEP.'Cleaning...');
		$workspace = $this->workspace();
		Filesystem::deleteRecursive($workspace);
		mkdir($workspace, 0755, true);
		return true;
	}

	private function updateContent()
	{
		// Directory where the zip file was uncompress
		$destinationPath = $this->workspace();

		// This helps when uncompress the zip file and the files are saved inside a directory
		$listDirectories = Filesystem::listDirectories($destinationPath);
		if(count($listDirectories)==1) {
			$uncompressDirectory = $listDirectories[0];
		} else {
			$uncompressDirectory = $destinationPath;
		}

		$uncompressDirectory = rtrim($uncompressDirectory, '/');

		// Copy page directory
		if(Filesystem::directoryExists($uncompressDirectory.DS.'pages')) {
			Log::set('Plugin Remote Content'.LOG_SEP.'Copying pages...');
			Filesystem::copyRecursive($uncompressDirectory.DS.'pages', PATH_PAGES);
		}

		// Copy databases directory
		if(Filesystem::directoryExists($uncompressDirectory.DS.'databases')) {
			Log::set('Plugin Remote Content'.LOG_SEP.'Copying databases...');
			Filesystem::copyRecursive($uncompressDirectory.DS.'databases', PATH_DATABASES);
		}

		// Copy uploads directory
		if(Filesystem::directoryExists($uncompressDirectory.DS.'uploads')) {
			Log::set('Plugin Remote Content'.LOG_SEP.'Copying uploads...');
			Filesystem::copyRecursive($uncompressDirectory.DS.'uploads', PATH_UPLOADS);
		}

		return true;
	}

	private function getFile()
	{
		// Download the zip file
		Log::set('Plugin Remote Content'.LOG_SEP.'Downloading the zip file.');
		$url = $this->getValue('source');
		$destinationPath = $this->workspace();
		$destinationFile = $destinationPath.'content.zip';
		TCP::download($url, $destinationFile);

		// Uncompress the zip file
		Log::set('Plugin Remote Content'.LOG_SEP.'Uncompress the zip file.');
		$zip = new ZipArchive;
		if($zip->open($destinationFile)===true) {
			$zip->extractTo($destinationPath);
			$zip->close();
		}

		// Delete the zip file
		unlink($destinationFile);
		return true;
	}
}