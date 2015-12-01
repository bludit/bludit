<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

function checkPost($args)
{
	global $Security;
	global $Login;
	global $Language;

	if($Security->isBlocked()) {
		Alert::set($Language->g('IP address has been blocked').'<br>'.$Language->g('Try again in a few minutes'));
		return false;
	}

	// Verify User sanitize the input
	if( $Login->verifyUser($_POST['username'], $_POST['password']) )
	{
		// Renew the token. This token will be the same inside the session for multiple forms.
		$Security->generateTokenCSRF();

		Redirect::page('admin', 'dashboard');
		return true;
	}

	// Bruteforce protection, add IP to blacklist.
	$Security->addLoginFail();
	Alert::set($Language->g('Username or password incorrect'));

	return false;
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	checkPost($_POST);
}

// ============================================================================
// Main after POST
// ============================================================================
