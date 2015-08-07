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

		// Set Plugin data
		$languageFilename = PATH_PLUGINS.$Plugin->directoryName().DS.'language'.DS.$Site->locale().'.json';
		if( Sanitize::pathFile($languageFilename) )
		{
			$database = new dbJSON($languageFilename, false);
		}
		else
		{
			$languageFilename = PATH_PLUGINS.$Plugin->directoryName().DS.'language'.DS.'en_US.json';
			$database = new dbJSON($languageFilename, false);
		}

		$databaseArray = $database->db;
		$Plugin->setData( $databaseArray['plugin-data'] );

		// Add words to language dictionary.
		unset($databaseArray['plugin-data']);
		$Language->add($databaseArray);

		// Push Plugin to array all plugins installed and not installed.
		array_push($plugins['all'], $Plugin);

		// If the plugin installed
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
