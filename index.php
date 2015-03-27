<?php

// DEBUG:
$loadTime = microtime(true);

// SECURITY CONSTANT
define('BLUDIT', true);

// PHP PATHS
define('PATH_ROOT',					__DIR__.'/');
define('PATH_LANGUAGES',			PATH_ROOT.'languages/');
define('PATH_THEMES',				PATH_ROOT.'themes/');
define('PATH_PLUGINS',				PATH_ROOT.'plugins/');

define('PATH_KERNEL',				PATH_ROOT.'kernel/');
define('PATH_ABSTRACT',				PATH_ROOT.'kernel/abstract/');
define('PATH_BOOT',					PATH_ROOT.'kernel/boot/');
define('PATH_RULES',				PATH_ROOT.'kernel/boot/rules/');
define('PATH_HELPERS',				PATH_ROOT.'kernel/helpers/');

define('PATH_CONTENT',				PATH_ROOT.'content/');
define('PATH_POSTS',				PATH_CONTENT.'posts/');
define('PATH_PAGES',				PATH_CONTENT.'pages/');
define('PATH_DATABASES',			PATH_CONTENT.'databases/');
define('PATH_PLUGINS_DATABASES',	PATH_CONTENT.'databases/plugins/');

define('PATH_ADMIN_THEMES',			PATH_ROOT.'admin/themes/');
define('PATH_ADMIN_CONTROLLERS',	PATH_ROOT.'admin/controllers/');
define('PATH_ADMIN_VIEWS',			PATH_ROOT.'admin/views/');

// BOOT
require(PATH_BOOT.'site.php');

// Admin area
if($Url->whereAmI()==='admin')
{
	$layout = array(
		'controller'=>null,
		'view'=>null,
		'template'=>'index.php'
	);

	$layout['controller'] = $layout['view'] = $Url->slug();

	if($Url->notFound() || !$Login->isLogged() || ($Url->slug()==='login') )
	{
		$layout['controller'] = 'login';
		$layout['view'] = 'login';
		$layout['template'] = 'login.php';
	}

	// Admin theme init.php
	if( Sanitize::pathFile(PATH_ADMIN_THEMES, $Site->adminTheme().'/init.php') )
		include(PATH_ADMIN_THEMES.$Site->adminTheme().'/init.php');

	// Load controller
	if( Sanitize::pathFile(PATH_ADMIN_CONTROLLERS, $layout['controller'].'.php') )
		include(PATH_ADMIN_CONTROLLERS.$layout['controller'].'.php');	

	// Load view and theme
	if( Sanitize::pathFile(PATH_ADMIN_THEMES, $Site->adminTheme().'/'.$layout['template']) )
		include(PATH_ADMIN_THEMES.$Site->adminTheme().'/'.$layout['template']);

}
// Site
else
{
	if($Url->notFound())
	{
		$Url->setWhereAmI('page');
		$Page = new Page('error');
	}

	// Theme init.php
	if( Sanitize::pathFile(PATH_THEMES, $Site->theme().'/init.php') )
		include(PATH_THEMES.$Site->theme().'/init.php');

	// Theme HTML
	if( Sanitize::pathFile(PATH_THEMES, $Site->theme().'/index.php') )
		include(PATH_THEMES.$Site->theme().'/index.php');
}

// DEBUG:
echo "Load time: ".(microtime(true) - $loadTime);
