<?php defined('BLUDIT') or die('Bludit CMS.');

// Bludit version
define('BLUDIT_VERSION',	'githubVersion');
define('BLUDIT_CODENAME',	'');
define('BLUDIT_RELEASE_DATE',	'');

// Debug mode
define('DEBUG_MODE', TRUE);
error_reporting(0); // Turn off all error reporting
if(DEBUG_MODE)
{
	// Turn on all error reporting
	ini_set("display_errors", 1);
	ini_set('display_startup_errors',1);
	ini_set("track_errors", 1);
	ini_set("html_errors", 1);
	error_reporting(E_ALL | E_STRICT | E_NOTICE);
}

// PHP paths
// PATH_ROOT and PATH_BOOT are defined in index.php
define('PATH_LANGUAGES',		PATH_ROOT.'languages'.DS);
define('PATH_THEMES',			PATH_ROOT.'themes'.DS);
define('PATH_PLUGINS',			PATH_ROOT.'plugins'.DS);
define('PATH_KERNEL',			PATH_ROOT.'kernel'.DS);
define('PATH_ABSTRACT',			PATH_KERNEL.'abstract'.DS);
define('PATH_RULES',			PATH_KERNEL.'boot'.DS.'rules'.DS);
define('PATH_HELPERS',			PATH_KERNEL.'helpers'.DS);
define('PATH_AJAX',			PATH_KERNEL.'ajax'.DS);
define('PATH_JS',			PATH_KERNEL.'js'.DS);
define('PATH_CONTENT',			PATH_ROOT.'content'.DS);
define('PATH_POSTS',			PATH_CONTENT.'posts'.DS);
define('PATH_PAGES',			PATH_CONTENT.'pages'.DS);
define('PATH_DATABASES',		PATH_CONTENT.'databases'.DS);
define('PATH_PLUGINS_DATABASES',	PATH_CONTENT.'databases'.DS.'plugins'.DS);
define('PATH_UPLOADS',			PATH_CONTENT.'uploads'.DS);
define('PATH_ADMIN',			PATH_ROOT.'admin'.DS);
define('PATH_ADMIN_THEMES',		PATH_ADMIN.'themes'.DS);
define('PATH_ADMIN_CONTROLLERS',	PATH_ADMIN.'controllers'.DS);
define('PATH_ADMIN_VIEWS',		PATH_ADMIN.'views'.DS);

// Log separator
define('LOG_SEP', ' | ');

// JSON pretty print
if(!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 128);
}

// Salt length
define('SALT_LENGTH', 8);

// Page brake string
define('PAGE_BREAK', '<!-- pagebreak -->');

// No parent character
define('NO_PARENT_CHAR', '—');

// Post per page on Manage->Posts
define('POSTS_PER_PAGE_ADMIN', 10);

// Check if JSON encode and decode are enabled.
define('JSON', function_exists('json_encode'));

// TRUE if new posts hand-made set published, or FALSE for draft.
define('CLI_STATUS', 'published');

// Database format date
define('DB_DATE_FORMAT', 'Y-m-d H:i');

// Charset, default UTF-8.
define('CHARSET', 'UTF-8');

// Multibyte string extension loaded.
define('MB_STRING', extension_loaded('mbstring'));

if(MB_STRING)
{
	// Set internal character encoding.
	mb_internal_encoding(CHARSET);

	// Set HTTP output character encoding.
	mb_http_output(CHARSET);
}

// Inclde Abstract Classes
include(PATH_ABSTRACT.'dbjson.class.php');
include(PATH_ABSTRACT.'filecontent.class.php');
include(PATH_ABSTRACT.'plugin.class.php');

// Inclde Classes
include(PATH_KERNEL.'dbposts.class.php');
include(PATH_KERNEL.'dbpages.class.php');
include(PATH_KERNEL.'dbusers.class.php');
include(PATH_KERNEL.'dbtags.class.php');
include(PATH_KERNEL.'dblanguage.class.php');
include(PATH_KERNEL.'dbsite.class.php');
include(PATH_KERNEL.'post.class.php');
include(PATH_KERNEL.'page.class.php');
include(PATH_KERNEL.'url.class.php');
include(PATH_KERNEL.'login.class.php');
include(PATH_KERNEL.'parsedown.class.php');
include(PATH_KERNEL.'security.class.php');

// Include Helpers Classes
include(PATH_HELPERS.'text.class.php');
include(PATH_HELPERS.'log.class.php');
include(PATH_HELPERS.'date.class.php');
include(PATH_HELPERS.'theme.class.php');
include(PATH_HELPERS.'session.class.php');
include(PATH_HELPERS.'redirect.class.php');
include(PATH_HELPERS.'sanitize.class.php');
include(PATH_HELPERS.'valid.class.php');
include(PATH_HELPERS.'filesystem.class.php');
include(PATH_HELPERS.'alert.class.php');
include(PATH_HELPERS.'paginator.class.php');

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
$dbTags 	= new dbTags();
$Site 		= new dbSite();
$Url 		= new Url();
$Parsedown 	= new Parsedown();
$Security	= new Security();

// HTML PATHs
$base = (dirname(getenv('SCRIPT_NAME'))==DS)?'/':dirname(getenv('SCRIPT_NAME')).'/';
define('HTML_PATH_ROOT', $base);

// Paths for themes
define('HTML_PATH_THEMES',		HTML_PATH_ROOT.'themes/');
define('HTML_PATH_THEME',		HTML_PATH_ROOT.'themes/'.$Site->theme().'/');
define('HTML_PATH_THEME_CSS',		HTML_PATH_THEME.'css/');
define('HTML_PATH_THEME_JS',		HTML_PATH_THEME.'js/');
define('HTML_PATH_THEME_IMG',		HTML_PATH_THEME.'img/');

define('HTML_PATH_ADMIN_THEME',		HTML_PATH_ROOT.'admin/themes/'.$Site->adminTheme().'/');
define('HTML_PATH_ADMIN_ROOT',		HTML_PATH_ROOT.'admin/');
define('HTML_PATH_UPLOADS',		HTML_PATH_ROOT.'content/uploads/');
define('HTML_PATH_PLUGINS',		HTML_PATH_ROOT.'plugins/');

// PHP paths with dependency
define('PATH_THEME',			PATH_ROOT.'themes/'.$Site->theme().'/');
define('PATH_THEME_PHP',		PATH_THEME.'php'.DS)

// Objects with dependency
$Language 	= new dbLanguage( $Site->locale() );
$Login 		= new Login( $dbUsers );

$Url->checkFilters( $Site->uriFilters() );

// Objects shortcuts
$L = $Language;
