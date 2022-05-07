<?php defined('BLUDIT') or die('Bludit CMS.');

// Redirect admin, from /admin to /admin/
if ($url->uri()==HTML_PATH_ROOT.ADMIN_URI_FILTER) {
	Redirect::url(DOMAIN_ADMIN);
}

// Redirect blog, from /blog to /blog/
// If the user define the blog's filter as "myblog" the redirection will be from /myblog to /myblog/
if ($site->homepage()) {
    $filter = $url->filters('blog');
    if ($url->uri()==HTML_PATH_ROOT.$filter) {
        $finalURL = Text::addSlashes(DOMAIN_BASE.$filter, false, true);
        Redirect::url($finalURL);
    }
}

// Redirect pages, from /my-page/ to /my-page
if ($url->whereAmI()=='page' && !$url->notFound()) {
	$pageKey = $url->slug();
	if (Text::endsWith($pageKey, '/')) {
		$pageKey = rtrim($pageKey, '/');
		Redirect::url(DOMAIN_PAGES.$pageKey);
	}
}
