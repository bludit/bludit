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
	global $Language;
	global $dbUsers;
	global $Site;

	if($Security->isBlocked()) {
		Alert::set($Language->g('IP address has been blocked').'<br>'.$Language->g('Try again in a few minutes'));
		return false;
	}

	// Remove illegal characters from email
	$email = Sanitize::email($args['email']);

	if(Valid::email($email))
	{
		// Get username associated to an email.
		$username = $dbUsers->getByEmail($email);
		if($username!=false)
		{
			// Generate the token and the token expiration date.
			$token = $dbUsers->generateTokenEmail($username);

			// ---- EMAIL ----
			$link = $Site->url().'admin/login-email?tokenEmail='.$token.'&username='.$username;
			$subject = $Language->g('BLUDIT Login access code');
			$message = Text::replaceAssoc(
					array(
						'{{WEBSITE_NAME}}'=>$Site->title(),
						'{{LINK}}'=>'<a href="'.$link.'">'.$link.'</a>'
					),
					$Language->g('email-notification-login-access-code')
			);

			$sent = Email::send(array(
						'from'=>$Site->emailFrom(),
						'to'=>$email,
						'subject'=>$subject,
						'message'=>$message
			));

			if($sent) {
				Alert::set($Language->g('check-your-inbox-for-your-login-access-code'));
				return true;
			}
			else {
				Alert::set($Language->g('There was a problem sending the email'));
				return false;
			}
		}
	}

	// Bruteforce protection, add IP to blacklist.
	$Security->addLoginFail();
	Alert::set($Language->g('check-your-inbox-for-your-login-access-code'));

	return false;
}

function checkGet($args)
{
	global $Security;
	global $Language;
	global $Login;

	if($Security->isBlocked()) {
		Alert::set($Language->g('IP address has been blocked').'<br>'.$Language->g('Try again in a few minutes'));
		return false;
	}

	// Verify User sanitize the input
	if( $Login->verifyUserByToken($args['username'], $args['tokenEmail']) )
	{
		// Renew the tokenCRFS. This token will be the same inside the session for multiple forms.
		$Security->generateTokenCSRF();

		Redirect::page('admin', 'dashboard');
		return true;
	}

	// Bruteforce protection, add IP to blacklist.
	$Security->addLoginFail();
	return false;
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// GET Method
// ============================================================================

if( !empty($_GET['tokenEmail']) && !empty($_GET['username']) )
{
	checkGet($_GET);
}


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
