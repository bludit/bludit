<?php defined('BLUDIT') or die('Bludit CMS.');

$plugins = array(
	'onSiteHead'=>array(),
	'onSiteBody'=>array(),
	'onSidebar'=>array()
);

function build_plugins()
{
	global $plugins;

	// List plugins directories
	$list = helperFilesystem::listDirectories(PATH_PLUGINS);

	// Get declared clasess before load plugins clasess
	$currentDeclaredClasess = get_declared_classes();

	// Load each clasess
	foreach($list as $pluginPath)
		include($pluginPath.'/plugin.php');

	// Get plugins clasess loaded
	$pluginsDeclaredClasess = array_diff(get_declared_classes(), $currentDeclaredClasess);

	foreach($pluginsDeclaredClasess as $pluginClass)
	{
		$Plugin = new $pluginClass;

		if($Plugin->onSiteHead()!==false)
			array_push($plugins['onSiteHead'], $Plugin);

		if($Plugin->onSiteBody()!==false)
			array_push($plugins['onSiteBody'], $Plugin);

		if($Plugin->onSidebar()!==false)
			array_push($plugins['onSidebar'], $Plugin);
	}
}

build_plugins();

?>
