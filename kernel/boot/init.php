<?php defined('BLUDIT') or die('Bludit CMS.');

// Bludit version
define('BLUDIT_VERSION',	'githubVersion');
define('BLUDIT_CODENAME',	'');
define('BLUDIT_RELEASE_DATE',	'');
define('BLUDIT_BUILD',		'20151119');

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
define('PATH_UPLOADS_PROFILES',		PATH_UPLOADS.'profiles'.DS);
define('PATH_ADMIN',			PATH_KERNEL.'admin'.DS);
define('PATH_ADMIN_THEMES',		PATH_ADMIN.'themes'.DS);
define('PATH_ADMIN_CONTROLLERS',	PATH_ADMIN.'controllers'.DS);
define('PATH_ADMIN_VIEWS',		PATH_ADMIN.'views'.DS);

// Log separator
define('LOG_SEP', ' | ');

// JSON pretty print
if(!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 128);
}

// Alert status ok
define('ALERT_STATUS_OK', 0);

// Alert status fail
define('ALERT_STATUS_FAIL', 1);

// Salt length
define('SALT_LENGTH', 8);

// Page brake string
define('PAGE_BREAK', '<!-- pagebreak -->');

// No parent character, md5('No parent')
define('NO_PARENT_CHAR', '3849abb4cb7abd24c2d8dac17b216f17');

// Post per page on Manage->Posts
define('POSTS_PER_PAGE_ADMIN', 10);

// Check if JSON encode and decode are enabled.
define('JSON', function_exists('json_encode'));

// TRUE if new posts hand-made set published, or FALSE for draft.
define('CLI_STATUS', 'published');

// Database format date
define('DB_DATE_FORMAT', 'Y-m-d H:i');

// Date format for Dashboard schedule posts
define('SCHEDULED_DATE_FORMAT', 'd M - h:i a');

// Token time to live for login via email. The offset is defined by http://php.net/manual/en/datetime.modify.php
define('TOKEN_EMAIL_TTL', '+15 minutes');

// Charset, default UTF-8.
define('CHARSET', 'UTF-8');

// Directory permissions
define('DIR_PERMISSIONS', '0755');

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
include(PATH_KERNEL.'parsedownextra.class.php');
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
include(PATH_HELPERS.'email.class.php');
include(PATH_HELPERS.'filesystem.class.php');
include(PATH_HELPERS.'alert.class.php');
include(PATH_HELPERS.'paginator.class.php');
include(PATH_HELPERS.'image.class.php');

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
$Parsedown 	= new ParsedownExtra();
$Security	= new Security();

// HTML PATHs
$base = empty( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
$base = dirname($base);

if($base!=DS) {
	$base = $base.'/';
}
else {
	// Workaround for Windows Web Servers
	$base = '/';
}

define('HTML_PATH_ROOT', $base);

// Paths for themes
define('HTML_PATH_THEMES',		HTML_PATH_ROOT.'themes/');
define('HTML_PATH_THEME',		HTML_PATH_ROOT.'themes/'.$Site->theme().'/');
define('HTML_PATH_THEME_CSS',		HTML_PATH_THEME.'css/');
define('HTML_PATH_THEME_JS',		HTML_PATH_THEME.'js/');
define('HTML_PATH_THEME_IMG',		HTML_PATH_THEME.'img/');

define('HTML_PATH_ADMIN_ROOT',		HTML_PATH_ROOT.'admin/');
define('HTML_PATH_ADMIN_THEME',		HTML_PATH_ROOT.'kernel/admin/themes/'.$Site->adminTheme().'/');
define('HTML_PATH_ADMIN_THEME_JS',	HTML_PATH_ADMIN_THEME.'js/');
define('HTML_PATH_ADMIN_THEME_CSS',	HTML_PATH_ADMIN_THEME.'css/');
define('HTML_PATH_ADMIN_THEME_IMG',	HTML_PATH_ADMIN_THEME.'img/');

define('HTML_PATH_UPLOADS',		HTML_PATH_ROOT.'content/uploads/');
define('HTML_PATH_UPLOADS_PROFILES',	HTML_PATH_UPLOADS.'profiles/');
define('HTML_PATH_PLUGINS',		HTML_PATH_ROOT.'plugins/');

define('JQUERY',			HTML_PATH_ADMIN_THEME_JS.'jquery.min.js');

// PHP paths with dependency
define('PATH_THEME',			PATH_ROOT.'themes'.DS.$Site->theme().DS);
define('PATH_THEME_PHP',		PATH_THEME.'php'.DS);
define('PATH_THEME_CSS',		PATH_THEME.'css'.DS);
define('PATH_THEME_JS',			PATH_THEME.'js'.DS);
define('PATH_THEME_IMG',		PATH_THEME.'img'.DS);
define('PATH_THEME_LANG',		PATH_THEME.'languages'.DS);

// --- Objects with dependency ---
$Language 	= new dbLanguage( $Site->locale() );
$Login 		= new Login( $dbUsers );
$Url->checkFilters( $Site->uriFilters() );

// --- Objects shortcuts ---
$L = $Language;
