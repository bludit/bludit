<?php defined('BLUDIT') or die('Bludit CMS.');

var_dump($url);exit;

// Redirect admin, from /admin to /admin/
if ($url->uri()=='/'.ADMIN_URI_FILTER) {
	Redirect::url(DOMAIN_ADMIN);
}

// Redirect pages, from /my-page/ to /my-page
if ($url->whereAmI()=='page' && !$url->notFound()) {
	$pageKey = $url->slug();
	if (Text::endsWith($pageKey, '/')) {
		$pageKey = rtrim($pageKey, '/');
		Redirect::url(DOMAIN_PAGES.$pageKey);
	}
}
