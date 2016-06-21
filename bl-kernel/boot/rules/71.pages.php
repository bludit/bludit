<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// Array with all pages.
$pages = array();

$pagesPublished = array();

// Array with all pages, order by parent.
$pagesParents = array(NO_PARENT_CHAR=>array());

$pagesParentsPublished = array();

// ============================================================================
// Main
// ============================================================================

// Search for changes on pages by the user.
if( CLI_MODE ) {
	$dbPages->regenerateCli();
}

// Build specific page.
if( ($Url->whereAmI()==='page') && ($Url->notFound()===false) )
{
	$Page = buildPage( $Url->slug() );

	// The page doesn't exist.
	if($Page===false)
	{
		$Url->setNotFound(true);
		unset($Page);
	}
	// The page is not published yet.
	elseif( !$Page->published() )
	{
		$Url->setNotFound(true);
		unset($Page);
	}
}

// Homepage
if( ($Url->whereAmI()==='home') && ($Url->notFound()===false) )
{
	// The user defined as homepage a particular page.
	if( Text::isNotEmpty( $Site->homepage() ) )
	{
		$Url->setWhereAmI('page');

		$Page = buildPage( $Site->homepage() );

		if($Page===false) {
			$Url->setWhereAmI('home');
		}
	}
}

if($Url->notFound())
{
	$Url->setWhereAmI('page');
	$Page = new Page('error');
}

// Build all pages
$pages = buildAllPages();

