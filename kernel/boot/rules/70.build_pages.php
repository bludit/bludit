<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

$pages = array();
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

	// Page object.
	$Page = new Page($key);
	if( !$Page->isValid() ) {
		return false;
	}

	// Page database.
	$db = $dbPages->getDb($key);
	if( !$db ) {
		return false;
	}

	// Foreach field from database.
	foreach($db as $field=>$value)
	{
		if($field=='unixTimeCreated')
		{
			// Format dates, not overwrite from file fields.
			$Page->setField('unixTimeCreated', 	$value, false);
			$Page->setField('date', 			Date::format($value, '%d %B'), false);
			$Page->setField('timeago',			Date::timeago($value), false);
		}
		else
		{
			// Other fields, not overwrite from file fields.
			$Page->setField($field, $value, false);
		}
	}

	// Content in raw format
	$Page->setField('contentRaw', $Page->content(), true);

	// Parse markdown content.
	$content = $Parsedown->text( $Page->content() );
	$Page->setField('content', $content, true);

	// Parse username for the page.
	if( $dbUsers->userExists( $Page->username() ) )
	{
		$user = $dbUsers->get( $Page->username() );

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
			// Generate all posible parents.
			if( $Page->parentKey()===false )
			{
				$dbPages->addParentKey($Page->key());

				$pagesParents[NO_PARENT_CHAR][$Page->key()] = $Page;
			}
			else
			{
				$pagesParents[$Page->parentKey()][$Page->key()] = $Page;
			}

			// $pages array
			$pages[$Page->key()] = $Page;
		}
	}

	// ======== Order pages ========

	// DEBUG: No me gusta esta forma de ordenar

	// Order children
	$tmp = array();
	foreach($pagesParents as $parentKey=>$childrenPages)
	{
		$tmp[$parentKey] = $childrenPages;
		uasort($tmp[$parentKey], 'orderChildren');
	}

	if(isset($tmp[NO_PARENT_CHAR]))
	{
		$tmpNoParents = $tmp[NO_PARENT_CHAR];
		unset($tmp[NO_PARENT_CHAR]);
	}

	$pagesParents = $tmp;

	// Order parents.
	foreach($pagesParents as $parentKey=>$childrenPages)
	{
		$tmp = orderParent($tmp, array($parentKey=>$childrenPages), $pages[$parentKey]->position());
	}

	$pagesParents = array(NO_PARENT_CHAR=>$tmpNoParents) + $tmp;
}

// ============================================================================
// Main
// ============================================================================

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
	if( ($Site->homepage()!=='home') && ($Url->whereAmI()==='home') )
	{
		$Url->setWhereAmI('page');

		$Page = build_page( $Site->homepage() );

		if($Page===false)
		{
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
