<?php

class pluginRemoteContent extends Plugin {

	public function init()
	{
		// Generate a random string for the webhook
		$randomWebhook = uniqid();

		// Key and value for the database of the plugin
		$this->dbFields = array(
			'webhook'=>$randomWebhook,
			'source'=>''
		);
	}

	public function form()
	{
		global $Language;

		if (extension_loaded('zip')===false) {
			$this->formButtons = false;
			return '<div class="alert alert-success">'.$Language->get('the-extension-zip-is-not-installed').'</div>';
		}

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Webhook').'</label>';
		$html .= '<input id="jswebhook" name="webhook" type="text" value="'.$this->getValue('webhook').'">';
		$html .= '<span class="tip">'.DOMAIN_BASE.$this->getValue('webhook').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Source').'</label>';
		$html .= '<input id="jssource" name="source" type="text" value="'.$this->getValue('source').'" placeholder="https://">';
		$html .= '<span class="tip">'.$Language->get('Complete URL of the zip file').'</span>';
		$html .= '</div>';

		return $html;
	}

	public function beforeAll()
	{
		// Check Webhook
		$webhook = $this->getValue('webhook');
		if ($this->webhook($webhook)) {
			// Download files
			$this->downloadFiles();

			// Delete the current content
			$this->deleteContent();

			// Generate the new content
			$this->generateContent();

			// End request
			$this->response(array('status'=>'0'));
		}
	}

	private function downloadFiles()
	{
		// Download the zip file
		Log::set('Plugin Remote Content'.LOG_SEP.'Downloading the zip file.');
		$source = $this->getValue('source');
		$destinationPath = $this->workspace();
		$destinationFile = $destinationPath.'content.zip';
		TCP::download($source, $destinationFile);

		// Uncompress the zip file
		Log::set('Plugin Remote Content'.LOG_SEP.'Uncompress the zip file.');
		$zip = new ZipArchive;
		if ($zip->open($destinationFile)===true) {
			$zip->extractTo($destinationPath);
			$zip->close();
		}

		// Delete the zip file
		unlink($destinationFile);
		return true;
	}

	// Delete the page and uploads directories from bl-content
	private function deleteContent()
	{
		// Clean the page database
		global $pages;
		$pages->db = array();

		Filesystem::deleteRecursive(PATH_PAGES);
		Filesystem::deleteRecursive(PATH_UPLOADS);
		mkdir(PATH_PAGES, 0755, true);
		mkdir(PATH_UPLOADS, 0755, true);

		return true;
	}

	private function generateContent()
	{
		global $pages;

		$root = Filesystem::listDirectories($this->workspace());
		$root = $root[0]; // first directory created by the unzip

		// For each page inside the pages directory
		// Parse the page and add to the database
		if (Filesystem::directoryExists($root.DS.'pages')) {
			$pageList = Filesystem::listDirectories($root.DS.'pages'.DS);
			foreach ($pageList as $directory) {
				if (Filesystem::fileExists($directory.DS.'index.md')) {
					// Parse the page from the file
					$row = $this->parsePage($directory.DS.'index.md');

					// Add the page to the database
					$pages->add($row);

					// Call the plugins after page created
					Theme::plugins('afterPageCreate');

					// Reindex databases
					reindexCategories();
					reindextags();
				}
			}
		}

		return true;
	}

	private function response($data=array())
	{
		$json = json_encode($data);
		header('Content-Type: application/json');
		exit($json);
	}

	private function parsePage($filename)
	{
		$lines = file($filename);
		$row = array();

		// Title
		$title = ltrim($lines[0], '#'); // Remove the first #
		$title = trim($title);
		unset($lines[0]);
		$row['title'] = $title;

		foreach ($lines as $key=>$line) {
			if (Text::startsWith($line, '<!--')) {
				$line = preg_replace('/<!\-\-/', '', $line);
				$line = preg_replace('/\-\->/', '', $line);
				$line = trim($line);

				$explode = $explode = explode(':', $line, 2);

				$field = Text::lowercase($explode[0]);
				$field = trim($field);
				unset($explode[0]);
				$row[$field] = trim($explode[1]);

				unset($lines[$key]);
			} else {
				break;
			}
		}

		$row['content'] = implode($lines);
		$row['username'] = 'admin';

		return $row;
	}

}