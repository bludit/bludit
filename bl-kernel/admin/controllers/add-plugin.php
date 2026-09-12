<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = isset($_POST['action']) ? $_POST['action'] : '';

	// --- Install a plugin from the plugins directory ---
	if ($action === 'install') {
		$pluginId = isset($_POST['pluginId']) ? Sanitize::html($_POST['pluginId']) : '';
		if (PluginInstaller::installFromDirectory($pluginId) === false) {
			Alert::set(PluginInstaller::$error, ALERT_STATUS_FAIL);
			Redirect::page('add-plugin');
		}

		Alert::set($L->g('The plugin has been installed'));
		Redirect::page('plugins');
	}

	// --- Install a plugin from a zip file uploaded by the user ---
	elseif ($action === 'upload') {
		if (!isset($_FILES['pluginZip']) || ($_FILES['pluginZip']['error'] !== UPLOAD_ERR_OK)) {
			Alert::set($L->g('Unable to upload the file, check the size of the file'), ALERT_STATUS_FAIL);
			Redirect::page('add-plugin');
		}

		$fileExtension = Text::lowercase(Filesystem::extension($_FILES['pluginZip']['name']));
		if ($fileExtension !== 'zip') {
			Alert::set($L->g('The file needs to be a zip file'), ALERT_STATUS_FAIL);
			Redirect::page('add-plugin');
		}

		// When the zip file has no directory inside, the name of the zip file is
		// used as the name of the plugin directory
		$fallbackId = Text::lowercase(pathinfo($_FILES['pluginZip']['name'], PATHINFO_FILENAME));
		$pluginId = PluginInstaller::installFromZip($_FILES['pluginZip']['tmp_name'], false, $fallbackId);
		if ($pluginId === false) {
			Alert::set(PluginInstaller::$error, ALERT_STATUS_FAIL);
			Redirect::page('add-plugin');
		}

		Alert::set($L->g('The plugin has been installed'));
		Redirect::page('plugins');
	}

	// --- Download the plugins directory again ---
	elseif ($action === 'refresh') {
		PluginsDirectory::getIndex(true);
		Redirect::page('add-plugin');
	}

	Redirect::page('add-plugin');
}

// ============================================================================
// Main after POST
// ============================================================================

// List of plugins from the directory compatible with this version of Bludit
// FALSE when the directory is not available, the view shows a message and the
// user is still able to install a plugin from a zip file
$pluginsDirectory = PluginsDirectory::getCompatible();

// Title of the page
$layout['title'] .= ' - ' . $L->g('Add a plugin');
