<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if (!checkRole(array('admin','editor'), false)) {
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
		// If the checkbox is not selected the form doesn't send the field
		$_POST['noindex'] = isset($_POST['noindex'])?true:false;
		$_POST['nofollow'] = isset($_POST['nofollow'])?true:false;
		$_POST['noarchive'] = isset($_POST['noarchive'])?true:false;

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
	$uuid = $page->uuid();
} catch (Exception $e) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the page: '.$pageKey, LOG_TYPE_ERROR);
	Redirect::page('content');
}

// Title of the page
$layout['title'] .= ' - '.$L->g('Edit content').' - '.$page->title();