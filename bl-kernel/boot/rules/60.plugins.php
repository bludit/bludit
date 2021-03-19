<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Global Variables
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

	'beforeAll'=>array(),
	'afterAll'=>array(),

	'paginator'=>array(),

	'beforePageModify'=>array(),
	'beforePageDelete'=>array(),

	'afterPageCreate'=>array(),
	'afterPageModify'=>array(),
	'afterPageDelete'=>array(),

	'loginHead'=>array(),
	'loginBodyBegin'=>array(),
	'loginBodyEnd'=>array(),

	'all'=>array() // $plugins['all'] keep installed and not installed plugins
);

// This array has only the installed plugins
// The array key is the "plugin class name" and the value is the object
// pluginsInstalled[pluginClass] = $Plugin
$pluginsInstalled = array();

// ============================================================================
// Functions
// ============================================================================

function buildPlugins()
{
	global $plugins;
	global $pluginsInstalled;
	global $L;
	global $site;

	// This array is only to get the hooks names
	$pluginsHooks = $plugins;
	unset($pluginsHooks['all']); // remove "all" because is not a valid hook

	// Get declared clasess BEFORE load plugins clasess
	$currentDeclaredClasess = get_declared_classes();

	// Load plugins clasess
	$list = Filesystem::listDirectories(PATH_PLUGINS);
	foreach ($list as $pluginPath) {
		if (file_exists($pluginPath.DS.'plugin.php')) {
			include_once($pluginPath.DS.'plugin.php');
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

		$database = file_get_contents($languageFilename);
		$database = json_decode($database, true);

		// Set name and description from the language file
		$Plugin->setMetadata('name',$database['plugin-data']['name']);
		$Plugin->setMetadata('description',$database['plugin-data']['description']);

		// Remove name and description from the language and includes new words to the global language dictionary
		unset($database['plugin-data']);
		if (!empty($database)) {
			$L->add($database);
		}

		// $plugins['all'] Array with all plugins, installed and not installed
		$plugins['all'][$pluginClass] = $Plugin;

		if ($Plugin->installed()) {
			// Include the plugin installed in the global array
			$pluginsInstalled[$pluginClass] = $Plugin;

			// Define new hooks from custom hooks
			if (!empty($Plugin->customHooks)) {
				foreach ($Plugin->customHooks as $hook) {
					if (!isset($plugins[$hook])) {
						$plugins[$hook] = array();
						$pluginsHooks[$hook] = array();
					}
				}
			}

			// Insert the plugin into the hooks
			foreach ($pluginsHooks as $hook=>$value) {
				if (method_exists($Plugin, $hook)) {
					array_push($plugins[$hook], $Plugin);
				}
			}
		}

		// Sort the plugins by the position for the site sidebar
		uasort($plugins['siteSidebar'], function ($a, $b) {
				return $a->position()>$b->position();
			}
		);

		// Sort the plugins by the position for the dashboard
		uasort($plugins['dashboard'], function ($a, $b) {
				return $a->position()>$b->position();
			}
		);
	}
}

// ============================================================================
// Main
// ============================================================================

buildPlugins();
