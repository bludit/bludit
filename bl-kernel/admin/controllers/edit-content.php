<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if (checkRole(array('author'), false)) {
	try {
		$pageKey = isset($_POST['key']) ? $_POST['key'] : $layout['parameters'];
		$page = new Page($pageKey);
	} catch (Exception $e) {
		Alert::set($L->g('You do not have sufficient permissions'));
		Redirect::page('dashboard');
	}

	if ($page->username()!==$login->username()) {
		// Add to syslog
		$syslog->add(array(
			'dictionaryKey'=>'access-denied',
			'notes'=>$login->username()
		));

		Alert::set($L->g('You do not have sufficient permissions'));
		Redirect::page('dashboard');
	}
}

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
	if ($_POST['type']==='delete') {
		if (deletePage($_POST['key'])) {
			Alert::set( $L->g('The changes have been saved') );
		}
	} else {
		$key = editPage($_POST);
		if ($key!==false) {
			Alert::set( $L->g('The changes have been saved') );
			Redirect::page('edit-content/'.$key);
		}
	}

	Redirect::page('content');
}

// ============================================================================
// Main after POST
// ============================================================================
try {
	$pageKey = $layout['parameters'];
	$page = new Page($pageKey);
} catch (Exception $e) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the page: '.$pageKey, LOG_TYPE_ERROR);
	Redirect::page('content');
}

// Images prefix directory
define('PAGE_IMAGES_KEY', $page->uuid());

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
$layout['title'] .= ' - '.$L->g('Edit content').' - '.$page->title();