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

	'beforeRulesLoad'=>array(),

	'afterPostCreate'=>array(),
	'afterPostModify'=>array(),
	'afterPostDelete'=>array(),
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

// ============================================================================
// Functions
// ============================================================================

function buildPlugins()
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

		// Check if the plugin is translated.
		$languageFilename = PATH_PLUGINS.$Plugin->directoryName().DS.'languages'.DS.$Site->locale().'.json';
		if( !Sanitize::pathFile($languageFilename) ) {
			$languageFilename = PATH_PLUGINS.$Plugin->directoryName().DS.'languages'.DS.'en_US.json';
		}

		$database = file_get_contents($languageFilename);
		$database = json_decode($database, true);

		// Set name and description from the language file.
		$Plugin->setMetadata('name',$database['plugin-data']['name']);
		$Plugin->setMetadata('description',$database['plugin-data']['description']);

		// Remove name and description, and add new words if there are.
		unset($database['plugin-data']);
		if(!empty($database)) {
			$Language->add($database);
		}

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

buildPlugins();
