<?php defined('BLUDIT') or die('Bludit CMS.');

// Start the session
// If the session is not possible to start the admin area is not available
Session::start($site->urlPath(), $site->isHTTPS());
if (Session::started()===false) {
	exit('Bludit CMS. Session initialization failed.');
}

$login = new Login();

$layout = array(
	'controller'=>null,
	'view'=>null,
	'template'=>'index.php',
	'slug'=>null,
	'plugin'=>false,
	'parameters'=>null,
	'title'=>'Bludit'
);

// Get the Controller
$explodeSlug = $url->explodeSlug();
$layout['controller'] = $layout['view'] = $layout['slug'] = empty($explodeSlug[0])?'dashboard':$explodeSlug[0];
unset($explodeSlug[0]);

// Get the Plugins
include(PATH_RULES.'60.plugins.php');
// Check if the user want to access to an admin controller or view from a plugin
if ($layout['controller'] === 'plugin' && !empty($explodeSlug)) {
	// Lowercase plugins class name to search by case-insensitive
	$pluginsLowerCases = array_change_key_case($pluginsInstalled);
	$pluginName = Text::lowercase(array_shift($explodeSlug));
	if (isset($pluginsLowerCases[$pluginName])) {
		$layout['plugin'] = $pluginsLowerCases[$pluginName];
	}
}

// Get the URL parameters
$layout['parameters'] = implode('/', $explodeSlug);

// --- AJAX ---
if ($layout['slug']==='ajax') {
	if ($login->isLogged()) {
		// Rules: Security check CSRF
		include(PATH_RULES.'99.security.php');

		// Load the ajax file
		if (Sanitize::pathFile(PATH_AJAX, $layout['parameters'].'.php')) {
			include(PATH_AJAX.$layout['parameters'].'.php');
		}
	}
	header('HTTP/1.1 401 User not logged.');
	exit(0);
}
// --- ADMIN AREA ---
else
{
	// Boot rules
	include(PATH_RULES.'69.pages.php');
	include(PATH_RULES.'99.header.php');
	include(PATH_RULES.'99.paginator.php');
	include(PATH_RULES.'99.themes.php');
	include(PATH_RULES.'99.security.php');

	// Page not found.
	// User not logged.
	// Slug is login.
	if ($url->notFound() || !$login->isLogged() || ($url->slug()==='login') ) {
		$layout['controller']	= 'login';
		$layout['view']			= 'login';
		$layout['template']		= 'login.php';

		// Generate the tokenCSRF for the user not logged, when the user log-in the token will be change.
		$security->generateTokenCSRF();
	}

	// Define variables
	$ADMIN_CONTROLLER 	= $layout['controller'];
	$ADMIN_VIEW 		= $layout['view'];

	// Load plugins before the admin area will be load.
	Theme::plugins('beforeAdminLoad');

	// Load init.php if the theme has one.
	if (Sanitize::pathFile(PATH_ADMIN_THEMES, $site->adminTheme().DS.'init.php')) {
		include(PATH_ADMIN_THEMES.$site->adminTheme().DS.'init.php');
	}

	// Load controller.
	if (Sanitize::pathFile(PATH_ADMIN_CONTROLLERS, $layout['controller'].'.php')) {
		include(PATH_ADMIN_CONTROLLERS.$layout['controller'].'.php');
	} elseif ($layout['plugin'] && method_exists($layout['plugin'], 'adminController')) {
		$layout['plugin']->adminController();
	}

	// Load view and theme.
	if (Sanitize::pathFile(PATH_ADMIN_THEMES, $site->adminTheme().DS.$layout['template'])) {
		include(PATH_ADMIN_THEMES.$site->adminTheme().DS.$layout['template']);
	}

	// Load plugins after the admin area is loaded.
	Theme::plugins('afterAdminLoad');
}
