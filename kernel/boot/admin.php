<?php defined('BLUDIT') or die('Bludit CMS.');

$layout = array(
	'controller'=>null,
	'view'=>null,
	'template'=>'index.php',
	'slug'=>null,
	'parameters'=>null
);

// Get the view, controller, and the parameters from the URL.
$explodeSlug = $Url->explodeSlug();
$layout['controller'] = $layout['view'] = $layout['slug'] = $explodeSlug[0];
unset($explodeSlug[0]);
$layout['parameters'] = implode('/', $explodeSlug);

// AJAX
if( $Login->isLogged() && ($layout['slug']==='ajax') )
{
	// Boot rules
	// Ajax doesn't needs load rules

	// Load AJAX file
	if( Sanitize::pathFile(PATH_AJAX, $layout['parameters'].'.php') )
		include(PATH_AJAX.$layout['parameters'].'.php');
}
// ADMIN AREA
else
{
	// Boot rules
	include(PATH_RULES.'70.build_posts.php');
	include(PATH_RULES.'70.build_pages.php');
	include(PATH_RULES.'80.plugins.php');
	include(PATH_RULES.'99.header.php');

	if($Url->notFound() || !$Login->isLogged() || ($Url->slug()==='login') )
	{
		$layout['controller']	= 'login';
		$layout['view']			= 'login';
		$layout['template']		= 'login.php';
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