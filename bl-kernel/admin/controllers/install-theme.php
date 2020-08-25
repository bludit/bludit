<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================
$token = "";
$parameters = explode("/", $layout['parameters']);
if(count($parameters)==2) {
    $themeDirectory = $parameters[0];

    // Verify CSRF Token
    $token = Sanitize::html($parameters[1]);
    if ($security->validateTokenCSRF($token)) {
        // Activate theme
        activateTheme($themeDirectory);

        // Redirect
        Redirect::page('themes');
    }
}

Log::set(__FILE__.LOG_SEP.'Error occurred when trying to validate the tokenCSRF.', ALERT_STATUS_FAIL);
Log::set(__FILE__.LOG_SEP.'Token in install theme ['.$token.']', ALERT_STATUS_FAIL);

Session::destroy();
Redirect::page('login');

