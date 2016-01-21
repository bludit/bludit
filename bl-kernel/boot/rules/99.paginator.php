<?php defined('BLUDIT') or die('Bludit CMS.');

// Current page number.
$currentPage = $Url->pageNumber();
Paginator::set('currentPage', $currentPage);

// Number of pages.
if($Url->whereAmI()=='admin') {
	$postPerPage = POSTS_PER_PAGE_ADMIN;
	$numberOfPosts = $dbPosts->numberPost(true); // published and drafts
}
elseif($Url->whereAmI()=='tag') {
	$postPerPage = $Site->postsPerPage();
	$tagKey = $Url->slug();
	$numberOfPosts = $dbTags->countPostsByTag($tagKey);
}
else {
	$postPerPage = $Site->postsPerPage();
	$numberOfPosts = $dbPosts->numberPost(false); // published
}

// Post per page.
Paginator::set('postPerPage', $postPerPage);

// Number of posts
Paginator::set('numberOfPosts', $numberOfPosts);

$numberOfPages = (int) max(ceil($numberOfPosts / $postPerPage) -1, 0);
Paginator::set('numberOfPages', $numberOfPages);

$showOlder = $numberOfPages > $currentPage;
Paginator::set('showOlder', $showOlder);

$showNewer = $currentPage > 0;
Paginator::set('showNewer', $showNewer);

$show = $showNewer && $showOlder;
Paginator::set('show', true);

$nextPage = max(0, $currentPage+1);
Paginator::set('nextPage', $nextPage);

$prevPage = min($numberOfPages, $currentPage-1);
Paginator::set('prevPage', $prevPage);
