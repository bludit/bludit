<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

$plugins = array(
	'siteHead'=>array(),
	'siteBodyBegin'=>array(),
	'siteBodyEnd'=>array(),
	'siteSidebar'=>array(),
	'beforeSiteLoad'=>array(),
	'afterSiteLoad'=>array(),

	'pageBegin'=>array(),
	'pageEnd'=>array(),

	'beforeAdminLoad'=>array(),
	'afterAdminLoad'=>array(),
	'adminHead'=>array(),
	'adminBodyBegin'=>array(),
	'adminBodyEnd'=>array(),
	'adminSidebar'=>array(),
	'adminContentSidebar'=>array(),
	'dashboard'=>array(),
	'editorToolbar'=>array(),

	'beforeAll'=>array(),
	'afterAll'=>array(),

	'paginator'=>array(),

	'afterPageCreate'=>array(),
	'afterPageModify'=>array(),
	'afterPageDelete'=>array(),

	'loginHead'=>array(),
	'loginBodyBegin'=>array(),
	'loginBodyEnd'=>array(),

	'all'=>array()
);

$pluginsEvents = $plugins;
unset($pluginsEvents['all']);

$pluginsInstalled = array();

// ============================================================================
// Functions
// ============================================================================

function buildPlugins()
{
	global $plugins;
	global $pluginsEvents;
	global $pluginsInstalled;
	global $L;
	global $site;

	// Get declared clasess BEFORE load plugins clasess
	$currentDeclaredClasess = get_declared_classes();

	// List plugins directories
	$list = Filesystem::listDirectories(PATH_PLUGINS);
	// Load each plugin clasess
	foreach ($list as $pluginPath) {
		// Check if the directory has the plugin.php
		if (file_exists($pluginPath.DS.'plugin.php')) {
			// A plugin with a syntax error throws a ParseError, catch it to keep
			// the site working, otherwise a single broken plugin takes down the
			// whole site including the admin area
			try {
				include_once($pluginPath.DS.'plugin.php');
			} catch (Throwable $e) {
				Log::set('Plugin ' . basename($pluginPath) . LOG_SEP . 'The file plugin.php can not be loaded, ' . $e->getMessage(), LOG_TYPE_ERROR);
			}
		}
	}

	// Get plugins clasess loaded
	$pluginsDeclaredClasess = array_diff(get_declared_classes(), $currentDeclaredClasess);

	foreach ($pluginsDeclaredClasess as $pluginClass) {
		$Plugin = new $pluginClass;

		// Check if the plugin is translated
		$languageFilename = PATH_PLUGINS.$Plugin->directoryName().DS.'languages'.DS.$site->language().'.json';
		if (!Sanitize::pathFile($languageFilename)) {
			$languageFilename = PATH_PLUGINS.$Plugin->directoryName().DS.'languages'.DS.DEFAULT_LANGUAGE_FILE;
		}

		// The language file is read defensively, a plugin with a missing or invalid
		// language file is discarded instead of breaking the whole site
		$database = false;
		if (file_exists($languageFilename)) {
			$database = json_decode(file_get_contents($languageFilename), true);
		}

		if (!isset($database['plugin-data']['name']) || !isset($database['plugin-data']['description'])) {
			Log::set('Plugin ' . $Plugin->directoryName() . LOG_SEP . 'The language file is missing or invalid, the plugin is not loaded.', LOG_TYPE_ERROR);
			continue;
		}

		// A plugin without metadata.json can not be loaded, the metadata is
		// read by the Plugin class and it's empty when the file is missing or invalid
		if (empty($Plugin->metadata)) {
			Log::set('Plugin ' . $Plugin->directoryName() . LOG_SEP . 'The file metadata.json is missing or invalid, the plugin is not loaded.', LOG_TYPE_ERROR);
			continue;
		}

		// Set name and description from the language file
		$Plugin->setMetadata('name',$database['plugin-data']['name']);
		$Plugin->setMetadata('description',$database['plugin-data']['description']);

		// Remove name and description from the language file loaded and add new words if there are
		// This function overwrite the key=>value
		unset($database['plugin-data']);
		if (!empty($database)) {
			$L->add($database);
		}

		// $plugins['all'] Array with all plugins, installed and not installed
		$plugins['all'][$pluginClass] = $Plugin;

		// If the plugin is installed insert on the hooks
		if ($Plugin->installed()) {
			// Include custom hooks
			if (!empty($Plugin->customHooks)) {
				foreach ($Plugin->customHooks as $customHook) {
					if (!isset($plugins[$customHook])) {
						$plugins[$customHook] = array();
						$pluginsEvents[$customHook] = array();
					}
				}
			}

			$pluginsInstalled[$pluginClass] = $Plugin;
			foreach ($pluginsEvents as $event=>$value) {
				if (method_exists($Plugin, $event)) {
					array_push($plugins[$event], $Plugin);
				}
			}
		}

		// Sort the plugins by the position for the site sidebar
		uasort($plugins['siteSidebar'], function ($a, $b) {
				return $a->position() <=> $b->position();
			}
		);
	}
}

// ============================================================================
// Main
// ============================================================================

buildPlugins();
