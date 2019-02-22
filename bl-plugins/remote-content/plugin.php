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
		global $language;

		if (extension_loaded('zip')===false) {
			$this->formButtons = false;
			return '<div class="alert alert-success">'.$language->get('the-extension-zip-is-not-installed').'</div>';
		}

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$language->get('Webhook').'</label>';
		$html .= '<input id="jswebhook" name="webhook" type="text" value="'.$this->getValue('webhook').'">';
		$html .= '<span class="tip">'.DOMAIN_BASE.$this->getValue('webhook').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$language->get('Source').'</label>';
		$html .= '<input id="jssource" name="source" type="text" value="'.$this->getValue('source').'" placeholder="https://">';
		$html .= '<span class="tip">'.$language->get('Complete URL of the zip file').'</span>';
		$html .= '</div>';

		$html .= '<hr>';
		$html .= '<div>';
		$html .= '<button type="button" id="jstryWebhook" class="btn btn-primary" onclick="tryWebhook()">'.$language->get('Try webhook').'</button>';
$html .= <<<EOF
<script>
	function tryWebhook() {
		var webhook = document.getElementById("jswebhook").value;
		window.open(DOMAIN_BASE+webhook, '_blank');
	}
</script>
EOF;
		$html .= '</div>';

		return $html;
	}

	public function beforeAll()
	{
		// Check Webhook
		$webhook = $this->getValue('webhook');
		if ($this->webhook($webhook)) {
			$this->cleanUp();

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
		mkdir(PATH_PAGES, DIR_PERMISSIONS, true);
		mkdir(PATH_UPLOADS, DIR_PERMISSIONS, true);
		mkdir(PATH_UPLOADS_PROFILES, DIR_PERMISSIONS, true);
		mkdir(PATH_UPLOADS_THUMBNAILS, DIR_PERMISSIONS, true);

		return true;
	}

	private function cleanUp()
	{
		$workspace = $this->workspace();
		Filesystem::deleteRecursive($workspace.DS);
		mkdir($workspace, DIR_PERMISSIONS, true);
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
			$parentList = Filesystem::listDirectories($root.DS.'pages'.DS);
			foreach ($parentList as $parentDirectory) {
				$parentKey = basename($parentDirectory);
				if (Filesystem::fileExists($parentDirectory.DS.'index.md')) {
					$row = $this->parsePage($parentDirectory.DS.'index.md');
					$row['slug'] = $parentKey;
					$pages->add($row);
				}

				$childList = Filesystem::listDirectories($parentDirectory.DS);
				foreach ($childList as $childDirectory) {
					$childKey = basename($childDirectory);
					if (Filesystem::fileExists($childDirectory.DS.'index.md')) {
						$row = $this->parsePage($childDirectory.DS.'index.md');
						$row['slug'] = $childKey;
						$row['parent'] = $parentKey;
						$pages->add($row);
					}
				}
			}

			Theme::plugins('afterPageCreate');
			reindexCategories();
			reindexTags();
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

				//$field = Text::lowercase($explode[0]);
				$field = $explode[0];
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