<?php defined('BLUDIT') or die('Bludit CMS.');

// Redirect admin, from /admin to /admin/
if ($url->uri()==HTML_PATH_ROOT.ADMIN_URI_FILTER) {
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
