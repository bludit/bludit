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
		$languageFilename = PATH_PLUGINS.$Plugin->directoryName().'language'.DS.$Site->locale().'.json';
		if( Sanitize::pathFile($languageFilename) )
		{
			$data = new dbJSON($languageFilename, false);
		}
		else
		{
			$languageFilename = PATH_PLUGINS.$Plugin->directoryName().'language'.DS.'en_US.json';
			$data = new dbJSON($languageFilename, false);
		}
		$data = $data->getDb();
		$Plugin->setData( $data['plugin-data'] );

		// Add words to language dictionary.
		unset($data['plugin-data']);
		$Language->add($data);

		// Push Plugin to array all plugins installed and not installed.
		array_push($plugins['all'], $Plugin);

		// If the plugin installed
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
