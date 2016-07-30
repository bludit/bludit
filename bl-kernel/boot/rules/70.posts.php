<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// Array with all posts specified by a filter.
// Filter by page number, by tag, etc.
$posts = array();

// ============================================================================
// Main
// ============================================================================

// Search for changes on posts by the user.
if( CLI_MODE && false) {
	if($dbPosts->cliMode()) {
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
