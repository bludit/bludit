<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Authorization
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main
// ============================================================================

$username = $layout['parameters'];

// Prevent non-administrators to change other users
if ($login->role()!=='admin') {
	$username = $login->username();
}

try {
	$user = new User($username);
} catch (Exception $e) {
	Redirect::page('users');
}

// HTML <title>
$layout['title'] = $L->g('Edit user') . ' [ ' . $username . ' ] ' . ' - ' . $layout['title'];