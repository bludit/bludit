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
	'afterFormSave'=>array(),

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

		// Check if the directory has the plugin.php
		if(file_exists($pluginPath.DS.'plugin.php')) {
			include($pluginPath.DS.'plugin.php');
		}
	}

	// Get plugins clasess loaded
	$pluginsDeclaredClasess = array_diff(get_declared_classes(), $currentDeclaredClasess);

	foreach($pluginsDeclaredClasess as $pluginClass)
	{
		// Ignore non-plugin classes
		if ( !is_subclass_of( $pluginClass, 'Plugin' ) )
		{
			continue;
		}
		
		$Plugin = new $pluginClass();		

		// Push Plugin to array all plugins installed and not installed.
		$plugins['all'][$pluginClass] = $Plugin;

		// If the plugin is installed, order by hooks.
		if($Plugin->installed()) {

			foreach($pluginsEvents as $event=>$value) {

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
