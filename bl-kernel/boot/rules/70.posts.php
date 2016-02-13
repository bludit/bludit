<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// Array with all posts specified by a filter.
// Filter by page number, by tag, etc.
$posts = array();

// ============================================================================
// Functions
// ============================================================================

function reIndexTagsPosts()
{
	global $dbPosts;
	global $dbTags;

	// Remove unpublished.
	$dbPosts->removeUnpublished();

	// Regenerate the tags index for posts.
	$dbTags->reindexPosts( $dbPosts->db );

	// Restore the database, before remove the unpublished.
	$dbPosts->restoreDB();

	return true;
}

function buildPost($key)
{
	global $dbPosts;
	global $dbUsers;
	global $Parsedown;
	global $Site;

	// Post object, content from FILE.
	$Post = new Post($key);
	if( !$Post->isValid() ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the post from file with key: '.$key);
		return false;
	}

	// Post database, content from DATABASE JSON.
	$db = $dbPosts->getPostDB($key);
	if( !$db ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the post from database with key: '.$key);
		return false;
	}

	// Foreach field from DATABASE.
	foreach($db as $field=>$value) {
		$Post->setField($field, $value);
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

	// Date format
	$postDate = $Post->date();
	$Post->setField('dateRaw', $postDate, true);

	$postDateFormated = $Post->dateRaw( $Site->dateFormat() );
	$Post->setField('date', $postDateFormated, true);

	// User object
	$username = $Post->username();
	$Post->setField('user', $dbUsers->getUser($username));

	return $Post;
}

function buildPostsForPage($pageNumber=0, $amount=POSTS_PER_PAGE_ADMIN, $removeUnpublished=true, $tagKey=false)
{
	global $dbPosts;
	global $dbTags;
	global $Url;

	$posts = array();

	if($tagKey) {
		// Get the keys list from tags database, this database is optimized for this case.
		$list = $dbTags->getList($pageNumber, $amount, $tagKey);
	}
	else {
		// Get the keys list from posts database.
		$list = $dbPosts->getList($pageNumber, $amount, $removeUnpublished);
	}

	// There are not posts for the page number then set the page notfound
	if(empty($list) && $pageNumber>0) {
		$Url->setNotFound(true);
	}

	// Foreach post key, build the post.
	foreach($list as $postKey=>$values)
	{
		$Post = buildPost($postKey);
		if($Post!==false) {
			array_push($posts, $Post);
		}
	}

	return $posts;
}

// ============================================================================
// Main
// ============================================================================

// Search for changes on posts by the user.
if( $Site->cliMode() ) {
	if($dbPosts->regenerateCli()) {
		reIndexTagsPosts();
	}
}

// Execute the scheduler.
if( $dbPosts->scheduler() ) {
	// Reindex dbTags.
	reIndexTagsPosts();
}

// Build specific post.
if( ($Url->whereAmI()==='post') && ($Url->notFound()===false) )
{
	$Post = buildPost( $Url->slug() );

	// The post doesn't exist.
	if($Post===false)
	{
		$Url->setNotFound(true);
		unset($Post);
	}
	// The post is not published yet.
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
// Build posts by specific tag.
elseif( ($Url->whereAmI()==='tag') && ($Url->notFound()===false) )
{
	$posts = buildPostsForPage($Url->pageNumber(), $Site->postsPerPage(), true, $Url->slug());
}
// Build posts for homepage or admin area.
else
{
	// Posts for admin area.
	if($Url->whereAmI()==='admin') {
		$posts = buildPostsForPage($Url->pageNumber(), POSTS_PER_PAGE_ADMIN, false);
	}
	// Posts for home and blog filter.
	elseif( ( ($Url->whereAmI()==='home') || ($Url->whereAmI()==='blog') ) && ($Url->notFound()===false) ) {
		$posts = buildPostsForPage($Url->pageNumber(), $Site->postsPerPage(), true);
	}
}
