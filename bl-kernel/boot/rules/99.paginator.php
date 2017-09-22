<?php defined('BLUDIT') or die('Bludit CMS.');

// Current page number
$currentPage = $Url->pageNumber();
Paginator::set('currentPage', $currentPage);

if($Url->whereAmI()=='admin') {
	$itemsPerPage = ITEMS_PER_PAGE_ADMIN;
	$amountOfItems = $dbPages->count(true);
}
elseif($Url->whereAmI()=='tag') {
	$itemsPerPage = $Site->itemsPerPage();
	$tagKey = $Url->slug();
	$amountOfItems = $dbTags->countPagesByTag($tagKey);
}
elseif($Url->whereAmI()=='category') {
	$itemsPerPage = $Site->itemsPerPage();
	$categoryKey = $Url->slug();
	$amountOfItems = $dbCategories->countPagesByCategory($categoryKey);
}
else {
	$itemsPerPage = $Site->itemsPerPage();
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
