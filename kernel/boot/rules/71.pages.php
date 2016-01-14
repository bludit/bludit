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

function sortPages($a, $b)
{
	if ($a->position() == $b->position()) {
	    return 0;
	}

	return ($a->position() < $b->position()) ? -1 : 1;
}

function buildPage($key)
{
	global $dbPages;
	global $dbUsers;
	global $Parsedown;
	global $Site;

	// Page object, content from FILE.
	$Page = new Page($key);
	if( !$Page->isValid() ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from file with key: '.$key);
		return false;
	}

	// Page database, content from DATABASE JSON.
	$db = $dbPages->getPageDB($key);
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

	// Pagebrake
	$explode = explode(PAGE_BREAK, $content);
	$Page->setField('breakContent', $explode[0], true);
	$Page->setField('readMore', !empty($explode[1]), true);

	// Date format
	$pageDate = $Page->date();
	$Page->setField('dateRaw', $pageDate, true);

	$pageDateFormated = $Page->dateRaw( $Site->dateFormat() );
	$Page->setField('date', $pageDateFormated, true);

	// User object
	$username = $Page->username();
	$Page->setField('user', $dbUsers->getUser($username));

	return $Page;
}

function buildAllPages()
{
	global $pagesParents;
	global $dbPages;

	$list = $dbPages->getDB();

	// Clean pages array.
	$pages = array();

	unset($list['error']);

	foreach($list as $key=>$db)
	{
		$Page = buildPage($key);

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

	// --- SORT PAGES ---

	// Sort parents.
	$parents = $pagesParents[NO_PARENT_CHAR];
	uasort($parents, 'sortPages');

	// Sort children.
	unset($pagesParents[NO_PARENT_CHAR]);
	$children = $pagesParents;
	$tmpPageWithParent = array();
	foreach($children as $parentKey=>$childrenPages)
	{
		// If the child doesn't have a valid parent, then doesn't included them.
		if(isset($pages[$parentKey]))
		{
			$tmpPageWithParent[$parentKey] = $childrenPages;
			uasort($tmpPageWithParent[$parentKey], 'sortPages');
		}
	}

	$pagesParents = array(NO_PARENT_CHAR=>$parents) + $tmpPageWithParent;

	return $pages;
}

// ============================================================================
// Main
// ============================================================================

// Search for changes on pages by the user.
if( $Site->cliMode() ) {
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
