<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

function checkLogin($args)
{
	global $security;
	global $login;
	global $L;

	if ($security->isBlocked()) {
		Alert::set($L->g('IP address has been blocked').'<br>'.$L->g('Try again in a few minutes'), ALERT_STATUS_FAIL);
		return false;
	}

	if ($login->verifyUser($_POST['username'], $_POST['password'])) {
		if (isset($_POST['remember'])) {
			$login->setRememberMe($_POST['username']);
		}
		// Renew the token. This token will be the same inside the session for multiple forms.
		$security->generateTokenCSRF();

		if (isset($_GET['enableAPI'])) {
			Redirect::page('api');
		}
		Redirect::page('dashboard');
		return true;
	}

	// Bruteforce protection, add IP to the blacklist
	$security->addToBlacklist();

	// Create alert
	Alert::set($L->g('Username or password incorrect'), ALERT_STATUS_FAIL);
	return false;
}

function checkRememberMe()
{
	global $security;
	global $login;

	if ($security->isBlocked()) {
		return false;
	}

	if ($login->verifyUserByRemember()) {
		$security->generateTokenCSRF();
		Redirect::page('dashboard');
		return true;
	}

	return false;
}

// ============================================================================
// Main before POST
// ============================================================================

if ($_SERVER['REQUEST_METHOD']!=='POST') {
	checkRememberMe();
}

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD']=='POST') {
	checkLogin($_POST);
}

// ============================================================================
// Main after POST
// ============================================================================
