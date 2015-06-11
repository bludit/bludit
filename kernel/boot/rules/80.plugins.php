<?php defined('BLUDIT') or die('Bludit CMS.');

$plugins = array(
	'onSiteHead'=>array(),
	'onSiteBody'=>array(),
	'onSidebar'=>array(),
	'onAdminHead'=>array(),
	'onAdminBody'=>array(),
	'all'=>array()
);

function build_plugins()
{
	global $plugins;

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
			if($Plugin->onSiteHead()!==false)
				array_push($plugins['onSiteHead'], $Plugin);

			if($Plugin->onSiteBody()!==false)
				array_push($plugins['onSiteBody'], $Plugin);

			if($Plugin->onSidebar()!==false)
				array_push($plugins['onSidebar'], $Plugin);

			if($Plugin->onAdminHead()!==false)
				array_push($plugins['onAdminHead'], $Plugin);

			if($Plugin->onAdminBody()!==false)
				array_push($plugins['onAdminBody'], $Plugin);
		}
	}
}

build_plugins();
