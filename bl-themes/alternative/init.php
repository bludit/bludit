<?php defined('BLUDIT') or die('Bludit CMS.');

// Check if the Alternative plugin is active
if ($themePlugin === false || $themePlugin === null) {
	// Display a user-friendly error message
	$errorMessage = 'To ensure proper functionality, the theme requires the Alternative plugin. ';
	$errorMessage .= 'Please activate the plugin through the admin panel.';

	// If in admin area, show link to plugins
	if (defined('ADMIN_CONTROLLER')) {
		$errorMessage .= ' <a href="' . DOMAIN_ADMIN . 'plugins">Go to Plugins</a>';
	}

	exit('<div style="font-family: sans-serif; padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; margin: 20px; color: #856404;">' . $errorMessage . '</div>');
}
