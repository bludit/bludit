<?php defined('BLUDIT') or die('Bludit CMS.');

// Load plugins rules
include(PATH_RULES.'60.plugins.php');

// Plugins before all
execPluginsByHook('beforeAll');

// Load rules
include(PATH_RULES.'60.router.php');
include(PATH_RULES.'69.pages.php');
include(PATH_RULES.'99.header.php');
include(PATH_RULES.'99.paginator.php');
include(PATH_RULES.'99.themes.php');

// Plugins before site loaded
execPluginsByHook('beforeSiteLoad');

// Theme init.php
if (Sanitize::pathFile(PATH_THEMES.$site->theme().DS.'init.php')) {
	include(PATH_THEMES.$site->theme().DS.'init.php');
}

// Theme HTML
if (Sanitize::pathFile(PATH_THEMES.$site->theme().DS.'index.php')) {
	include(PATH_THEMES.$site->theme().DS.'index.php');
} else {
	$L->p('Please check your theme configuration in the admin panel. Check for an active theme.');
}

// Plugins after site loaded
execPluginsByHook('afterSiteLoad');

// Plugins after all
execPluginsByHook('afterAll');
