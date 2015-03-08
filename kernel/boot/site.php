<?php defined('BLUDIT') or die('Bludit CMS.');

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
include(PATH_ABSTRACT.'db_serialize.class.php');
include(PATH_ABSTRACT.'db_content.class.php');
include(PATH_ABSTRACT.'plugin.class.php');

include(PATH_KERNEL.'db_posts.class.php');
include(PATH_KERNEL.'db_pages.class.php');
include(PATH_KERNEL.'db_users.class.php');
include(PATH_KERNEL.'post.class.php');
include(PATH_KERNEL.'page.class.php');
include(PATH_KERNEL.'site.class.php');
include(PATH_KERNEL.'url.class.php');
include(PATH_KERNEL.'language.class.php');
include(PATH_KERNEL.'parsedown.class.php');

// Helpers Classes
include(PATH_HELPERS.'text.class.php');
//include(PATH_HELPERS.'url.class.php');
include(PATH_HELPERS.'date.class.php');
include(PATH_HELPERS.'theme.class.php');
include(PATH_HELPERS.'filesystem.class.php');

// Objects
$dbPosts = new dbPosts();
$dbPages = new dbPages();
$dbUsers = new dbUsers();
$Site = new Site();
$Url = new Url();

$Parsedown = new Parsedown();

$Language = new Language( $Site->locale() );

$Url->init( $Site->urlFilters() );

// Objects shortcuts
$L = $Language;

// HTML PATHs
$tmp = dirname(getenv('SCRIPT_NAME'));
if($tmp!='/')
	define('HTML_PATH_ROOT', $tmp.'/');
else
	define('HTML_PATH_ROOT', $tmp);

define('HTML_PATH_THEMES', HTML_PATH_ROOT.'themes/');
define('HTML_PATH_THEME', HTML_PATH_ROOT.'themes/'.$Site->theme().'/');

// Boot rules
include(PATH_RULES.'70.build_posts.php');
include(PATH_RULES.'70.build_pages.php');
include(PATH_RULES.'80.plugins.php');

// Page not found 404
if($Url->notFound())
{
	header('HTTP/1.0 404 Not Found');
	$Page = new Page('error');
}

?>
