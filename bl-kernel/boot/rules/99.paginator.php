<?php defined('BLUDIT') or die('Bludit CMS.');

// Current page number
$currentPage = $url->pageNumber();
Paginator::set('currentPage', $currentPage);

if ($url->whereAmI()=='admin') {
	$itemsPerPage = ITEMS_PER_PAGE_ADMIN;
	$numberOfItems = $pages->count(true);
} elseif ($url->whereAmI()=='tag') {
	$itemsPerPage = $site->itemsPerPage();
	$tagKey = $url->slug();
	$numberOfItems = $tags->numberOfPages($tagKey);
} elseif ($url->whereAmI()=='category') {
	$itemsPerPage = $site->itemsPerPage();
	$categoryKey = $url->slug();
	$numberOfItems = $categories->numberOfPages($categoryKey);
} else {
	$itemsPerPage = $site->itemsPerPage();
	$numberOfItems = $pages->count(true);
}

// Execute hook from plugins
Theme::plugins('paginator');

// Items per page
Paginator::set('itemsPerPage', $itemsPerPage);

// Amount of items
Paginator::set('numberOfItems', $numberOfItems);

// Amount of pages
$numberOfPages = (int) max(ceil($numberOfItems / $itemsPerPage), 1);
Paginator::set('numberOfPages', $numberOfPages);

// TRUE if exists a next page to show
$showNext = $numberOfPages > $currentPage;
Paginator::set('showNext', $showNext);

// TRUE if exists a previous page to show
$showPrev = $currentPage > Paginator::firstPage();
Paginator::set('showPrev', $showPrev);

// TRUE if exists a next and previous page to show
$showNextPrev = $showNext && $showPrev;
Paginator::set('showNextPrev', $showNextPrev);

// Integer with the next page
$nextPage = max(0, $currentPage+1);
Paginator::set('nextPage', $nextPage);

// Integer with the previous page
$prevPage = min($numberOfPages, $currentPage-1);
Paginator::set('prevPage', $prevPage);
