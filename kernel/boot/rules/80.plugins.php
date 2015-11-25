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
	'postBegin'=>array(),
	'postEnd'=>array(),

	'adminHead'=>array(),
	'adminBodyBegin'=>array(),
	'adminBodyEnd'=>array(),
	'adminSidebar'=>array(),
	'beforeAdminLoad'=>array(),
	'afterAdminLoad'=>array(),

	'loginHead'=>array(),
	'loginBodyBegin'=>array(),
	'loginBodyEnd'=>array(),

	'all'=>array()
);

$pluginsEvents = $plugins;
unset($pluginsEvents['all']);

// ============================================================================
// Functions
// ============================================================================

function build_plugins()
{
	global $plugins;
	global $pluginsEvents;
	global $Language;
	global $Site;

	// List plugins directories
	$list = Filesystem::listDirectories(PATH_PLUGINS);

	// Get declared clasess before load plugins clasess, this list doesn't have the plugins clasess.
	$currentDeclaredClasess = get_declared_classes();

	// Load each plugin clasess
	foreach($list as $pluginPath) {
		include($pluginPath.DS.'plugin.php');
	}

	// Get plugins clasess loaded
	$pluginsDeclaredClasess = array_diff(get_declared_classes(), $currentDeclaredClasess);

	foreach($pluginsDeclaredClasess as $pluginClass)
	{
		$Plugin = new $pluginClass;

		// Default language and meta data for the plugin
		$tmpMetaData = array();
		$languageFilename = PATH_PLUGINS.$Plugin->directoryName().DS.'languages'.DS.'en_US.json';
		$database = new dbJSON($languageFilename, false);
		$tmpMetaData = $database->db['plugin-data'];

		// Check if the plugin is translated.
		$languageFilename = PATH_PLUGINS.$Plugin->directoryName().DS.'languages'.DS.$Site->locale().'.json';
		if( Sanitize::pathFile($languageFilename) )
		{
			$database = new dbJSON($languageFilename, false);
			$tmpMetaData = array_merge($tmpMetaData, $database->db['plugin-data']);
		}

		// Set plugin meta data
		$Plugin->setData($tmpMetaData);

		// Add words to language dictionary.
		unset($database->db['plugin-data']);
		$Language->add($database->db);

		// Push Plugin to array all plugins installed and not installed.
		$plugins['all'][$pluginClass] = $Plugin;

		// If the plugin is installed, order by hooks.
		if($Plugin->installed())
		{
			foreach($pluginsEvents as $event=>$value)
			{
				if(method_exists($Plugin, $event)) {
					array_push($plugins[$event], $Plugin);
				}
			}
		}
	}
}

// ============================================================================
// Main
// ============================================================================

build_plugins();
