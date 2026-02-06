<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin', 'editor', 'author'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	createPage($_POST);
	Redirect::page('content');
}

// ============================================================================
// Main after POST
// ============================================================================

// UUID of the page is need it for autosave and media manager
$uuid = $pages->generateUUID();

// Images prefix directory
define('PAGE_IMAGES_KEY', $uuid);

// Images and thubmnails directories
if (IMAGE_RESTRICT) {
	define('PAGE_IMAGES_DIRECTORY', (IMAGE_RELATIVE_TO_ABSOLUTE? '' : HTML_PATH_UPLOADS_PAGES.PAGE_IMAGES_KEY.'/'));
	define('PAGE_IMAGES_URL', (IMAGE_RELATIVE_TO_ABSOLUTE? '' : DOMAIN_UPLOADS_PAGES.PAGE_IMAGES_KEY.'/'));
	define('PAGE_THUMBNAILS_DIRECTORY', PATH_UPLOADS_PAGES.PAGE_IMAGES_KEY.DS.'thumbnails'.DS);
	define('PAGE_THUMBNAILS_HTML', HTML_PATH_UPLOADS_PAGES.PAGE_IMAGES_KEY.'/thumbnails/');
	define('PAGE_THUMBNAILS_URL', DOMAIN_UPLOADS_PAGES.PAGE_IMAGES_KEY.'/thumbnails/');
} else {
	define('PAGE_IMAGES_DIRECTORY', (IMAGE_RELATIVE_TO_ABSOLUTE? '' : HTML_PATH_UPLOADS));
	define('PAGE_IMAGES_URL', (IMAGE_RELATIVE_TO_ABSOLUTE? '' : DOMAIN_UPLOADS));
	define('PAGE_THUMBNAILS_DIRECTORY', PATH_UPLOADS_THUMBNAILS);
	define('PAGE_THUMBNAILS_HTML', HTML_PATH_UPLOADS_THUMBNAILS);
	define('PAGE_THUMBNAILS_URL', DOMAIN_UPLOADS_THUMBNAILS);
}

// Title of the page
$layout['title'] = $L->g('New content').' - '.$layout['title'];