<?php defined('BLUDIT') or die('Bludit CMS.');

// Current page number
$currentPage = $Url->pageNumber();
Paginator::set('currentPage', $currentPage);

if($Url->whereAmI()=='admin') {
	$itemsPerPage = ITEMS_PER_PAGE_ADMIN;
	$amountOfItems = $dbPages->count(false);
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
$amountOfPages = (int) max(ceil($amountOfItems / $itemsPerPage) -1, 0);
Paginator::set('amountOfPages', $amountOfPages);

$showOlder = $amountOfPages > $currentPage;
Paginator::set('showOlder', $showOlder);

$showNewer = $currentPage > 0;
Paginator::set('showNewer', $showNewer);

$show = $showNewer && $showOlder;
Paginator::set('show', true);

$nextPage = max(0, $currentPage+1);
Paginator::set('nextPage', $nextPage);

$prevPage = min($amountOfPages, $currentPage-1);
Paginator::set('prevPage', $prevPage);
