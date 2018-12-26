<?php defined('BLUDIT') or die('Bludit CMS.');

// Redirect admin, from /admin to /admin/
if ($url->uri()==HTML_PATH_ROOT.ADMIN_URI_FILTER) {
	Redirect::url(DOMAIN_ADMIN);
}

// Redirect blog, from /blog to /blog/
// This rule only works when the user set a page as homepage
if ($url->uri()==HTML_PATH_ROOT.'blog' && $site->homepage()) {
	$filter = $url->filters('blog');
	$finalURL = Text::addSlashes(DOMAIN_BASE.$filter, false, true);
	Redirect::url($finalURL);
}

// Redirect pages, from /my-page/ to /my-page
if ($url->whereAmI()=='page' && !$url->notFound()) {
	$pageKey = $url->slug();
	if (Text::endsWith($pageKey, '/')) {
		$pageKey = rtrim($pageKey, '/');
		Redirect::url(DOMAIN_PAGES.$pageKey);
	}
}
