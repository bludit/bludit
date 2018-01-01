<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

function checkLogin($args)
{
	global $Security;
	global $Login;
	global $Language;

	if ($Security->isBlocked()) {
		Alert::set($Language->g('IP address has been blocked').'<br>'.$Language->g('Try again in a few minutes'));
		return false;
	}

	if ($Login->verifyUser($_POST['username'], $_POST['password'])) {
		if (isset($_POST['remember'])) {
			$Login->setRememberMe($_POST['username']);
		}
		// Renew the token. This token will be the same inside the session for multiple forms.
		$Security->generateTokenCSRF();
		Redirect::page('dashboard');
		return true;
	}

	// Bruteforce protection, add IP to the blacklist
	$Security->addToBlacklist();

	// Create alert
	Alert::set($Language->g('Username or password incorrect'));

	return false;
}

function checkRememberMe()
{
	global $Security;
	global $Login;

	if ($Security->isBlocked()) {
		return false;
	}

	if ($Login->verifyUserByRemember()) {
		$Security->generateTokenCSRF();
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
