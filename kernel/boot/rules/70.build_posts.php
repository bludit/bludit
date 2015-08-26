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
	global $Site;

	// Post object, this get the content from the file.
	$Post = new Post($key);
	if( !$Post->isValid() ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the post from file with key: '.$key);
		return false;
	}

	// Page database, this get the contente from the database json.
	$db = $dbPosts->getDb($key);
	if( !$db ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the post from database with key: '.$key);
		return false;
	}

	// Foreach field from database.
	foreach($db as $field=>$value)
	{
		// Not overwrite the value from file.
		$Post->setField($field, $value, false);
	}

	// Content in raw format
	$contentRaw = $Post->content();
	$Post->setField('contentRaw', $contentRaw, true);

	// Parse the content
	$content = Text::pre2htmlentities($contentRaw); // Parse pre code with htmlentities
	$content = $Parsedown->text($content); // Parse Markdown.
	$content = Text::imgRel2Abs($content, HTML_PATH_UPLOADS); // Parse img src relative to absolute.
	$Post->setField('content', $content, true);

	// Pagebrake
	$explode = explode(PAGE_BREAK, $content);
	$Post->setField('breakContent', $explode[0], true);
	$Post->setField('readMore', !empty($explode[1]), true);

	// Parse username for the post.
	if( $dbUsers->userExists( $Post->username() ) )
	{
		$user = $dbUsers->getDb( $Post->username() );

		$Post->setField('authorFirstName', $user['firstName'], false);

		$Post->setField('authorLastName', $user['lastName'], false);
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
		build_posts_per_page($Url->pageNumber(), POSTS_PER_PAGE_ADMIN, true);
	}
	else
	{
		// Build post for the site, without the drafts posts
		build_posts_per_page($Url->pageNumber(), $Site->postsPerPage(), false);
	}
}
