<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// Array with all pages.
$pages = array();

// Array with all pages, order by parent.
$pagesParents = array(NO_PARENT_CHAR=>array());

// ============================================================================
// Functions
// ============================================================================

function orderChildren($a, $b)
{
	if ($a->position() == $b->position()) {
	    return 0;
	}

	return ($a->position() < $b->position()) ? -1 : 1;
}

function orderParent($array, $values, $offset) {
    return ( array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true) );
}

function build_page($key)
{
	global $dbPages;
	global $dbUsers;
	global $Parsedown;

	// Page object, content from FILE.
	$Page = new Page($key);
	if( !$Page->isValid() ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from file with key: '.$key);
		return false;
	}

	// Page database, content from DATABASE JSON.
	$db = $dbPages->getDb($key);
	if( !$db ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from database with key: '.$key);
		return false;
	}

	// Foreach field from DATABASE.
	foreach($db as $field=>$value) {
		$Page->setField($field, $value);
	}

	// Content in raw format
	$contentRaw = $Page->content();
	$Page->setField('contentRaw', $Page->content(), true);

	// Parse markdown content.
	$content = Text::pre2htmlentities($contentRaw); // Parse pre code with htmlentities
	$content = $Parsedown->text($content); // Parse Markdown.
	$content = Text::imgRel2Abs($content, HTML_PATH_UPLOADS); // Parse img src relative to absolute.
	$Page->setField('content', $content, true);

	// Parse username for the page.
	if( $dbUsers->userExists( $Page->username() ) )
	{
		$user = $dbUsers->getDb( $Page->username() );

		$Page->setField('authorFirstName', $user['firstName'], false);
		$Page->setField('authorLastName', $user['lastName'], false);
	}

	return $Page;
}

function build_all_pages()
{
	global $pages;
	global $pagesParents;
	global $dbPages;

	$list = $dbPages->getAll();

	unset($list['error']);

	foreach($list as $key=>$db)
	{
		$Page = build_page($key);

		if($Page!==false)
		{
			// --- Order pages by parents ---

			// Generate all posible parents.
			if( $Page->parentKey()===false )
			{
				// Add the parent key in the dbPages
				$dbPages->addParentKey($Page->key());

				$pagesParents[NO_PARENT_CHAR][$Page->key()] = $Page;
			}
			else
			{
				$pagesParents[$Page->parentKey()][$Page->key()] = $Page;
			}

			// --- All pages in 1 array ---
			$pages[$Page->key()] = $Page;
		}
	}

	// ======== Sort pages ========

	$tmpNoParents = $pagesParents[NO_PARENT_CHAR];
	unset($pagesParents[NO_PARENT_CHAR]);

	// Sort children
	$tmpPageWithParent = array();
	foreach($pagesParents as $parentKey=>$childrenPages)
	{
		$tmpPageWithParent[$parentKey] = $childrenPages;
		uasort($tmpPageWithParent[$parentKey], 'orderChildren');
	}

	// Sort parents
	$tmp = array();
	foreach($tmpNoParents as $parentKey=>$childrenPages)
	{
		// DEBUG: Workaround, Esto es un bug, cuando se usa el Cli mode
		// DEBUG: Se genera un padre sin index.txt y adentro hay un hijo
		if(isset($pages[$parentKey])) {
			$tmp = orderParent($tmp, array($parentKey=>$childrenPages), $pages[$parentKey]->position());
		}
	}

	$pagesParents = array(NO_PARENT_CHAR=>$tmp) + $tmpPageWithParent;
}

// ============================================================================
// Main
// ============================================================================

// Search for changes on pages by the user.
if( $Site->cliMode() ) {
	$dbPages->regenerateCli();
}

// Filter by page, then build it
if( ($Url->whereAmI()==='page') && ($Url->notFound()===false) )
{
	$Page = build_page( $Url->slug() );

	if($Page===false)
	{
		$Url->setNotFound(true);
		unset($Page);
	}
	elseif( !$Page->published() )
	{
		$Url->setNotFound(true);
		unset($Page);
	}
}

// Default homepage
if($Url->notFound()===false)
{
	if( Text::isNotEmpty($Site->homepage()) && ($Url->whereAmI()==='home') )
	{
		$Url->setWhereAmI('page');

		$Page = build_page( $Site->homepage() );

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
build_all_pages();
