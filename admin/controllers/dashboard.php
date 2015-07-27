<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main
// ============================================================================

$_newPosts = $dbPosts->regenerate();
$_newPages = $dbPages->regenerate();

$_draftPosts = array();
foreach($posts as $Post)
{
	if(!$Post->published()) {
		array_push($_draftPosts, $Post);
	}
}

$_draftPages = array();
foreach($pages as $Page)
{
	if(!$Page->published()) {
		array_push($_draftPages, $Page);
	}
}

