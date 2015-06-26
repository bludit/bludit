<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

$plugins = array(
	'onSiteHead'=>array(),
	'onSiteBody'=>array(),
	'onSiteSidebar'=>array(),
	'onAdminHead'=>array(),
	'onAdminBody'=>array(),
	'onAdminSidebar'=>array(),
	'beforePostsLoad'=>array(),
	'afterPostsLoad'=>array(),
	'beforePagesLoad'=>array(),
	'afterPagesLoad'=>array(),
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

	// List plugins directories
	$list = Filesystem::listDirectories(PATH_PLUGINS);

	// Get declared clasess before load plugins clasess, this list doesn't have the plugins clasess.
	$currentDeclaredClasess = get_declared_classes();

	// Load each plugin clasess
	foreach($list as $pluginPath) {
		include($pluginPath.'/plugin.php');
	}

	// Get plugins clasess loaded
	$pluginsDeclaredClasess = array_diff(get_declared_classes(), $currentDeclaredClasess);

	foreach($pluginsDeclaredClasess as $pluginClass)
	{
		$Plugin = new $pluginClass;

		// All plugins installed and not installed.
		array_push($plugins['all'], $Plugin);

		// If the plugin installed, then add the plugin on the arrays.
		if($Plugin->installed())
		{
			foreach($pluginsEvents as $event=>$value)
			{
				/*
				if($Plugin->onSiteHead()!==false)
					array_push($plugins['onSiteHead'], $Plugin);
				*/
				if($Plugin->{$event}()!==false) {
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
