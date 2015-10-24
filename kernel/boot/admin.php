<?php defined('BLUDIT') or die('Bludit CMS.');

$layout = array(
	'controller'=>null,
	'view'=>null,
	'template'=>'index.php',
	'slug'=>null,
	'parameters'=>null,
	'title'=>'Bludit'
);

// Get the view, controller, and the parameters from the URL.
$explodeSlug = $Url->explodeSlug();
$layout['controller'] = $layout['view'] = $layout['slug'] = $explodeSlug[0];
unset($explodeSlug[0]);
$layout['parameters'] = implode('/', $explodeSlug);

// Disable Magic Quotes
// Thanks, http://stackoverflow.com/questions/517008/how-to-turn-off-magic-quotes-on-shared-hosting
if ( in_array( strtolower( ini_get( 'magic_quotes_gpc' ) ), array( '1', 'on' ) ) )
{
    $_POST	= array_map('stripslashes', $_POST);
    $_GET	= array_map('stripslashes', $_GET);
    $_COOKIE	= array_map('stripslashes', $_COOKIE);
}

// AJAX
if( $layout['slug']==='ajax' )
{
	if($Login->isLogged())
	{
		// Load AJAX file
		if( Sanitize::pathFile(PATH_AJAX, $layout['parameters'].'.php') ) {
			include(PATH_AJAX.$layout['parameters'].'.php');
		}
	}
}
// ADMIN AREA
else
{
	// Boot rules
	include(PATH_RULES.'70.posts.php');
	include(PATH_RULES.'70.pages.php');
	include(PATH_RULES.'80.plugins.php');
	include(PATH_RULES.'99.header.php');
	include(PATH_RULES.'99.paginator.php');
	include(PATH_RULES.'99.themes.php');
	include(PATH_RULES.'99.security.php');

	if($Url->notFound() || !$Login->isLogged() || ($Url->slug()==='login') || ($Url->slug()==='login-email') )
	{
		$layout['controller']	= 'login';
		$layout['view']		= 'login';
		$layout['template']	= 'login.php';

		if($Url->slug()==='login-email')
		{
			$layout['controller']	= 'login-email';
			$layout['view']		= 'login-email';
		}

		// Generate the token for the user not logged, when the user is loggued the token will be change.
		$Security->generateToken();
	}

	// Plugins before admin area loaded
	Theme::plugins('beforeAdminLoad');

	// Admin theme init.php
	if( Sanitize::pathFile(PATH_ADMIN_THEMES, $Site->adminTheme().DS.'init.php') ) {
		include(PATH_ADMIN_THEMES.$Site->adminTheme().DS.'init.php');
	}

	// Load controller
	if( Sanitize::pathFile(PATH_ADMIN_CONTROLLERS, $layout['controller'].'.php') ) {
		include(PATH_ADMIN_CONTROLLERS.$layout['controller'].'.php');
	}

	// Load view and theme
	if( Sanitize::pathFile(PATH_ADMIN_THEMES, $Site->adminTheme().DS.$layout['template']) ) {
		include(PATH_ADMIN_THEMES.$Site->adminTheme().DS.$layout['template']);
	}

	// Plugins after admin area loaded
	Theme::plugins('afterAdminLoad');
}