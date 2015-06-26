<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

$posts = array();

// ============================================================================
// Functions
// ============================================================================

function buildPost($key)
{
	global $dbPosts;
	global $dbUsers;
	global $Parsedown;

	// Post object.
	$Post = new Post($key);
	if( !$Post->isValid() ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the post from file with key'.$key);
		return false;
	}

	// Page database.
	$db = $dbPosts->getDb($key);
	if( !$db ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the post from database with key'.$key);
		return false;
	}

	// Foreach field from database.
	foreach($db as $field=>$value)
	{
		if($field=='unixTimeCreated')
		{
			// Format dates, not overwrite from file fields.
			$Post->setField('unixTimeCreated', 	$value, false);
			$Post->setField('date', 			Date::format($value, '%d %B'), false);
			$Post->setField('timeago',			Date::timeago($value), false);
		}
		else
		{
			// Other fields, not overwrite from file fields.
			$Post->setField($field, $value, false);
		}
	}

	// Content in raw format
	$Post->setField('contentRaw', $Post->content(), true);

	// Parse the content
	$content = $Parsedown->text( $Post->content() );
	$Post->setField('content', $content, true);

	// User / Author
	if( $dbUsers->userExists( $Post->username() ) )
	{
		$user = $dbUsers->get( $Post->username() );

		$Post->setField('author', $user['firstName'].', '.$user['lastName'], false);
	}

	return $Post;
}

function build_posts_per_page($pageNumber=0, $amount=5, $draftPosts=false)
{
	global $dbPosts;
	global $posts;
	global $Url;

	$list = $dbPosts->getPage($pageNumber, $amount, $draftPosts);

	// There are not post for the pageNumber then NotFound page
	if(empty($list) && $pageNumber>0) {
		$Url->setNotFound(true);
	}

	foreach($list as $slug=>$db)
	{
		$Post = buildPost($slug);

		if($Post!==false) {
			array_push($posts, $Post);
		}
	}
}

// ============================================================================
// Main
// ============================================================================

// Plugins before load posts
Theme::plugins('beforePostsLoad');

// Filter by post, then build it
if( ($Url->whereAmI()==='post') && ($Url->notFound()===false) )
{
	$Post = buildPost( $Url->slug() );

	if($Post===false)
	{
		$Url->setNotFound(true);
		unset($Post);
	}
	elseif( !$Post->published() )
	{
		$Url->setNotFound(true);
		unset($Post);
	}
	else
	{
		$posts[0] = $Post;
	}

}
// Build post per page
else
{
	if($Url->whereAmI()==='admin') {
		// Build post for admin area with drafts
		build_posts_per_page($Url->pageNumber(), $Site->postsPerPage(), true);
	}
	else
	{
		// Build post for the site, without the drafts posts
		build_posts_per_page($Url->pageNumber(), $Site->postsPerPage(), false);
	}
}

// Plugins after load posts
Theme::plugins('afterPostsLoad');