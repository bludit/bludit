<?php defined('BLUDIT') or die('Bludit CMS.');

// PHP PATHS
define('PATH_LANGUAGES',			PATH_ROOT.'languages/');
define('PATH_THEMES',				PATH_ROOT.'themes/');
define('PATH_PLUGINS',				PATH_ROOT.'plugins/');

define('PATH_KERNEL',				PATH_ROOT.'kernel/');
define('PATH_ABSTRACT',				PATH_ROOT.'kernel/abstract/');
define('PATH_RULES',				PATH_ROOT.'kernel/boot/rules/');
define('PATH_HELPERS',				PATH_ROOT.'kernel/helpers/');
define('PATH_AJAX',					PATH_ROOT.'kernel/ajax/');
define('PATH_JS',					PATH_ROOT.'kernel/js/');

define('PATH_CONTENT',				PATH_ROOT.'content/');
define('PATH_POSTS',				PATH_CONTENT.'posts/');
define('PATH_PAGES',				PATH_CONTENT.'pages/');
define('PATH_DATABASES',			PATH_CONTENT.'databases/');
define('PATH_PLUGINS_DATABASES',	PATH_CONTENT.'databases/plugins/');

define('PATH_ADMIN_THEMES',			PATH_ROOT.'admin/themes/');
define('PATH_ADMIN_CONTROLLERS',	PATH_ROOT.'admin/controllers/');
define('PATH_ADMIN_VIEWS',			PATH_ROOT.'admin/views/');

// Log
// Log separator
define('LOG_SEP', ' | ');

//
define('NO_PARENT_CHAR', '—');

// Multibyte string / UTF-8
define('MB_STRING', extension_loaded('mbstring'));

// Check if JSON encode and decode are enabled.
define('JSON', function_exists('json_encode'));

// TRUE if new posts hand-made set published, or FALSE for draft.
define('HANDMADE_PUBLISHED', true);

if(MB_STRING)
{
	// Tell PHP that we're using UTF-8 strings until the end of the script.
	mb_internal_encoding('UTF-8');

	// Tell PHP that we'll be outputting UTF-8 to the browser.
	mb_http_output('UTF-8');
}

// Abstract Classes
include(PATH_ABSTRACT.'dbjson.class.php');
include(PATH_ABSTRACT.'filecontent.class.php');
include(PATH_ABSTRACT.'plugin.class.php');

include(PATH_KERNEL.'dbposts.class.php');
include(PATH_KERNEL.'dbpages.class.php');
include(PATH_KERNEL.'dbusers.class.php');
include(PATH_KERNEL.'dblanguage.class.php');
include(PATH_KERNEL.'dbsite.class.php');

include(PATH_KERNEL.'post.class.php');
include(PATH_KERNEL.'page.class.php');

include(PATH_KERNEL.'url.class.php');
include(PATH_KERNEL.'login.class.php');
include(PATH_KERNEL.'parsedown.class.php');

// Helpers Classes
include(PATH_HELPERS.'text.class.php');
include(PATH_HELPERS.'log.class.php');
include(PATH_HELPERS.'date.class.php');
include(PATH_HELPERS.'theme.class.php');
include(PATH_HELPERS.'session.class.php');
include(PATH_HELPERS.'redirect.class.php');
include(PATH_HELPERS.'sanitize.class.php');
include(PATH_HELPERS.'filesystem.class.php');
include(PATH_HELPERS.'alert.class.php');

// Session
Session::start();
if(Session::started()===false) {
	Log::set('init.php'.LOG_SEP.'Error occurred when trying to start the session.');
	exit('Bludit CMS. Failed to start session.');
}

// Objects
$dbPosts 	= new dbPosts();
$dbPages 	= new dbPages();
$dbUsers 	= new dbUsers();
$Site 		= new dbSite();
$Url 		= new Url();
$Parsedown 	= new Parsedown();

// HTML PATHs
$tmp = dirname(getenv('SCRIPT_NAME'));
if($tmp!='/') {
	define('HTML_PATH_ROOT', $tmp.'/');
}
else {
	define('HTML_PATH_ROOT', $tmp);
}

define('HTML_PATH_THEMES',		HTML_PATH_ROOT.'themes/');
define('HTML_PATH_THEME',		HTML_PATH_ROOT.'themes/'.$Site->theme().'/');
define('HTML_PATH_ADMIN_THEME',	HTML_PATH_ROOT.'admin/themes/'.$Site->adminTheme().'/');
define('HTML_PATH_ADMIN_ROOT',	HTML_PATH_ROOT.'admin/');

// Objects with dependency
$Language 	= new dbLanguage( $Site->locale() );
$Login 		= new Login( $dbUsers );

$Url->checkFilters( $Site->uriFilters() );

// Objects shortcuts
$L = $Language;