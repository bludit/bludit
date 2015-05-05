<?php defined('BLUDIT') or die('Bludit CMS.');

// Boot rules
include(PATH_RULES.'70.build_posts.php');
include(PATH_RULES.'70.build_pages.php');
include(PATH_RULES.'80.plugins.php');
include(PATH_RULES.'99.header.php');

if($Url->notFound())
{
	$Url->setWhereAmI('page');
	$Page = new Page('error');
}

// Theme init.php
if( Sanitize::pathFile(PATH_THEMES, $Site->theme().'/init.php') )
	include(PATH_THEMES.$Site->theme().'/init.php');

// Theme HTML
if( Sanitize::pathFile(PATH_THEMES, $Site->theme().'/index.php') )
	include(PATH_THEMES.$Site->theme().'/index.php');