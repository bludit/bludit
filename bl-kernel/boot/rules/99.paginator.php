<?php defined('BLUDIT') or die('Bludit CMS.');

// Current page number
$currentPage = $url->pageNumber();
Paginator::set('currentPage', $currentPage);

if($url->whereAmI()=='admin') {
	$itemsPerPage = ITEMS_PER_PAGE_ADMIN;
	$amountOfItems = $dbPages->count(true);
}
elseif($url->whereAmI()=='tag') {
	$itemsPerPage = $site->itemsPerPage();
	$tagKey = $url->slug();
	$amountOfItems = $tags->countPagesByTag($tagKey);
}
elseif($url->whereAmI()=='category') {
	$itemsPerPage = $site->itemsPerPage();
	$categoryKey = $url->slug();
	$amountOfItems = $categories->countPagesByCategory($categoryKey);
}
else {
	$itemsPerPage = $site->itemsPerPage();
	$amountOfItems = $dbPages->count(true);
}

// Items per page
Paginator::set('itemsPerPage', $itemsPerPage);

// Amount of items
Paginator::set('amountOfItems', $amountOfItems);

// Amount of pages
$amountOfPages = (int) max(ceil($amountOfItems / $itemsPerPage), 1);
Paginator::set('amountOfPages', $amountOfPages);

// TRUE if exists a next page to show
$showNext = $amountOfPages > $currentPage;
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
$prevPage = min($amountOfPages, $currentPage-1);
Paginator::set('prevPage', $prevPage);
