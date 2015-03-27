<?php defined('BLUDIT') or die('Bludit CMS.');

$pages = array();

function build_page($slug)
{
	global $dbPages;
	global $dbUsers;
	global $Parsedown;

	if( !$dbPages->validPage($slug) )
		return false;

	$Page = new Page($slug);
	if( !$Page->valid() )
		return false;

	// Get post's database
	$db = $dbPages->getDb($slug);
	foreach($db as $key=>$value)
	{
		if($key=='unixstamp')
		{
			// Not overwrite
			$Page->setField('unixstamp', 	$value, false);
			$Page->setField('date', 		Date::format($value, '%d %B'), false);
			$Page->setField('timeago',		Date::timeago($value), false);
		}
		else
		{
			// Not overwrite
			$Page->setField($key, $value, false);
		}
	}

	// Parse the content
	$content = $Parsedown->text( $Page->content() );
	$Page->setField('content', $content, true);

	if( $dbUsers->validUsername( $Page->username() ) )
	{
		$user = $dbUsers->get( $Page->username() );

		$Page->setField('author',	$user['first_name'].', '.$user['last_name']);
	}

	return $Page;
}

function build_all_pages()
{
	global $pages;
	global $dbPages;

	$list = $dbPages->getAll();

	unset($list['error']);

	foreach($list as $slug=>$db)
	{
		$Page = build_page($slug);

		if($Page!==false)
		{
			if( $Page->published() )
				array_push($pages, $Page);
		}
	}
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

// Build all pages
build_all_pages();
