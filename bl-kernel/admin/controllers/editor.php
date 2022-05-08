<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Authorization
// ============================================================================

checkRole(array('admin', 'editor', 'author'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main
// ============================================================================

$pageKey = false;
$pageUUID = false;
$pagePreviewID = false;
if (!empty($layout['parameters'])) {
	try {
		$pageKey = $layout['parameters'];
		$page = new Page($pageKey);
        $pageUUID = $page->uuid();
        $pagePreviewID = $page->previewID();
	} catch (Exception $e) {
		Log::set(__METHOD__.LOG_SEP.'An error occurred while trying to get the page: '.$pageKey, LOG_TYPE_ERROR);
		Redirect::page('content');
	}
}

// HTML <title>
$layout['title'] = $L->g('New content') . ' - ' . $layout['title'];
