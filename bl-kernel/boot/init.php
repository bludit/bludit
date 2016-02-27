<?php defined('BLUDIT') or die('Bludit CMS.');

// Bludit version
define('BLUDIT_VERSION',	'1.1.2');
define('BLUDIT_CODENAME',	'The Dig');
define('BLUDIT_RELEASE_DATE',	'2016-02-26');
define('BLUDIT_BUILD',		'20160226');

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
define('PATH_LANGUAGES',		PATH_ROOT.'bl-languages'.DS);
define('PATH_THEMES',			PATH_ROOT.'bl-themes'.DS);
define('PATH_PLUGINS',			PATH_ROOT.'bl-plugins'.DS);
define('PATH_KERNEL',			PATH_ROOT.'bl-kernel'.DS);
define('PATH_CONTENT',			PATH_ROOT.'bl-content'.DS);

define('PATH_ABSTRACT',			PATH_KERNEL.'abstract'.DS);
define('PATH_RULES',			PATH_KERNEL.'boot'.DS.'rules'.DS);
define('PATH_HELPERS',			PATH_KERNEL.'helpers'.DS);
define('PATH_AJAX',			PATH_KERNEL.'ajax'.DS);
define('PATH_JS',			PATH_KERNEL.'js'.DS);

define('PATH_POSTS',			PATH_CONTENT.'posts'.DS);
define('PATH_PAGES',			PATH_CONTENT.'pages'.DS);
define('PATH_DATABASES',		PATH_CONTENT.'databases'.DS);
define('PATH_PLUGINS_DATABASES',	PATH_CONTENT.'databases'.DS.'plugins'.DS);
define('PATH_TMP',			PATH_CONTENT.'tmp'.DS);
define('PATH_UPLOADS',			PATH_CONTENT.'uploads'.DS);

define('PATH_UPLOADS_PROFILES',		PATH_UPLOADS.'profiles'.DS);
define('PATH_UPLOADS_THUMBNAILS',	PATH_UPLOADS.'thumbnails'.DS);

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

// Protecting against Symlink attacks.
define('CHECK_SYMBOLIC_LINKS', TRUE);

// Alert status ok
define('ALERT_STATUS_OK', 0);

// Alert status fail
define('ALERT_STATUS_FAIL', 1);

// Salt length
define('THUMBNAILS_WIDTH', 400);
define('THUMBNAILS_HEIGHT', 400);
define('THUMBNAILS_AMOUNT', 6);

// Salt length
define('SALT_LENGTH', 8);

// Page brake string
define('PAGE_BREAK', '<!-- pagebreak -->');

// No parent character, md5('No parent')
define('NO_PARENT_CHAR', '3849abb4cb7abd24c2d8dac17b216f17');

// Post per page on Manage->Posts
define('POSTS_PER_PAGE_ADMIN', 10);

// Check if JSON encode and decode are enabled.
// define('JSON', function_exists('json_encode'));

// Cli mode status for new posts/pages
define('CLI_STATUS', 'published');

// Cli mode username for new posts/pages
define('CLI_USERNAME', 'admin');

// Database date format
define('DB_DATE_FORMAT', 'Y-m-d H:i:s');

// Sitemap date format
define('SITEMAP_DATE_FORMAT', 'Y-m-d');

// Date format for Dashboard schedule posts
define('SCHEDULED_DATE_FORMAT', 'd M - h:i a');

// Token time to live for login via email. The offset is defined by http://php.net/manual/en/datetime.modify.php
define('TOKEN_EMAIL_TTL', '+15 minutes');

// Charset, default UTF-8.
define('CHARSET', 'UTF-8');

// Directory permissions
define('DIR_PERMISSIONS', 0755);

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
include(PATH_ABSTRACT.'content.class.php');
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
include(PATH_KERNEL.'user.class.php');
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
	exit('Bludit. Failed to start session.');
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

// --- Relative paths ---
// This paths are relative for the user / web browsing.

// Base URL
// The user can define the base URL.
// Left empty if you want to Bludit try to detect the base URL.
$base = '';

if( !empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_NAME']) && empty($base) ) {
	$base = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_NAME']);
	$base = dirname($base);
}
elseif( empty($base) ) {
	$base = empty( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$base = dirname($base);
}

if($base!=DS) {
	$base = trim($base, '/');
	$base = '/'.$base.'/';
}
else {
	// Workaround for Windows Web Servers
	$base = '/';
}

define('HTML_PATH_ROOT', 		$base);
define('HTML_PATH_THEMES',		HTML_PATH_ROOT.'bl-themes/');
define('HTML_PATH_THEME',		HTML_PATH_THEMES.$Site->theme().'/');
define('HTML_PATH_THEME_CSS',		HTML_PATH_THEME.'css/');
define('HTML_PATH_THEME_JS',		HTML_PATH_THEME.'js/');
define('HTML_PATH_THEME_IMG',		HTML_PATH_THEME.'img/');

define('HTML_PATH_ADMIN_ROOT',		HTML_PATH_ROOT.'admin/');
define('HTML_PATH_ADMIN_THEME',		HTML_PATH_ROOT.'bl-kernel/admin/themes/'.$Site->adminTheme().'/');
define('HTML_PATH_ADMIN_THEME_JS',	HTML_PATH_ADMIN_THEME.'js/');
define('HTML_PATH_ADMIN_THEME_CSS',	HTML_PATH_ADMIN_THEME.'css/');
define('HTML_PATH_ADMIN_THEME_IMG',	HTML_PATH_ADMIN_THEME.'img/');

define('HTML_PATH_UPLOADS',		HTML_PATH_ROOT.'bl-content/uploads/');
define('HTML_PATH_UPLOADS_PROFILES',	HTML_PATH_UPLOADS.'profiles/');
define('HTML_PATH_UPLOADS_THUMBNAILS',	HTML_PATH_UPLOADS.'thumbnails/');
define('HTML_PATH_PLUGINS',		HTML_PATH_ROOT.'bl-plugins/');

define('JQUERY',			HTML_PATH_ADMIN_THEME_JS.'jquery.min.js');

// --- PHP paths with dependency ---
// This paths are absolutes for the OS.
define('PATH_THEME',			PATH_ROOT.'bl-themes'.DS.$Site->theme().DS);
define('PATH_THEME_PHP',		PATH_THEME.'php'.DS);
define('PATH_THEME_CSS',		PATH_THEME.'css'.DS);
define('PATH_THEME_JS',			PATH_THEME.'js'.DS);
define('PATH_THEME_IMG',		PATH_THEME.'img'.DS);
define('PATH_THEME_LANG',		PATH_THEME.'languages'.DS);

// --- Absolute paths with domain ---
// This paths are absolutes for the user / web browsing.
define('DOMAIN',			$Site->domain());
define('DOMAIN_BASE',			DOMAIN.HTML_PATH_ROOT);
define('DOMAIN_THEME_CSS',		DOMAIN.HTML_PATH_THEME_CSS);
define('DOMAIN_THEME_JS',		DOMAIN.HTML_PATH_THEME_JS);
define('DOMAIN_THEME_IMG',		DOMAIN.HTML_PATH_THEME_IMG);
define('DOMAIN_UPLOADS',		DOMAIN.HTML_PATH_UPLOADS);
define('DOMAIN_UPLOADS_PROFILES',	DOMAIN.HTML_PATH_UPLOADS_PROFILES);
define('DOMAIN_UPLOADS_THUMBNAILS',	DOMAIN.HTML_PATH_UPLOADS_THUMBNAILS);

// --- Objects with dependency ---
$Language 	= new dbLanguage( $Site->locale() );
$Login 		= new Login( $dbUsers );
$Url->checkFilters( $Site->uriFilters() );

// --- Objects shortcuts ---
$L = $Language;
