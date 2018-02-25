<?php
$GITHUB_BASE_URL = '__GITHUB_BASE_URL__';

$REDIRECT_PARENT_TO_FIRST_CHILD = true;

if (!$Site->homepage()) {
        echo 'This theme need a home page defined, please select one page on <b>Admin panel->Settings->Advanced->Home page</b>';
        exit;
}

if ($REDIRECT_PARENT_TO_FIRST_CHILD) {
	if (!empty($page)) {
		if ($page->isParent() && $page->hasChildren()) {
			$children = $page->children();
			if (!empty($children[0])) {
				$firstChild = $children[0];
			
				header('Location: '.$firstChild->permalink(), true, 302);
				exit;
			}
		}
	}
}
