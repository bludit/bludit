<?php defined('BLUDIT') or die('Bludit CMS.');

// Boot rules
include(PATH_RULES.'70.build_posts.php');
include(PATH_RULES.'70.build_pages.php');
include(PATH_RULES.'80.plugins.php');
include(PATH_RULES.'99.header.php');
include(PATH_RULES.'99.paginator.php');
include(PATH_RULES.'99.themes.php');

// Plugins before site loaded
Theme::plugins('beforeSiteLoad');

// Theme init.php
if( Sanitize::pathFile(PATH_THEMES, $Site->theme().DS.'init.php') ) {
	include(PATH_THEMES.$Site->theme().DS.'init.php');
}

// Theme HTML
if( Sanitize::pathFile(PATH_THEMES, $Site->theme().DS.'index.php') ) {
	include(PATH_THEMES.$Site->theme().DS.'index.php');
}

// Plugins after site loaded
Theme::plugins('afterSiteLoad');