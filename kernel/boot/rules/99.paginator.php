<?php defined('BLUDIT') or die('Bludit CMS.');

// Current page number.
$currentPage = $Url->pageNumber();
Paginator::set('currentPage', $currentPage);

// Post per page.
$postPerPage = $Site->postsPerPage();
Paginator::set('postPerPage', $postPerPage);

// Number of pages.
if($Url->whereAmI()=='admin') {	
	$numberOfPosts = $dbPosts->numberPost(true); // published and drafts
}
else {
	$numberOfPosts = $dbPosts->numberPost(false); // published
}

Paginator::set('numberOfPosts', $numberOfPosts);

$numberOfPages = (int) ceil($numberOfPosts / $postPerPage) -1;
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
