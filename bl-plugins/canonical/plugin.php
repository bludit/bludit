<?php

class pluginCanonical extends Plugin {

	public function siteHead()
	{
		global $url;
		global $page;
		global $WHERE_AM_I;

		if ($url->notFound()) {
			return '';
		}

		switch ($WHERE_AM_I) {
			case 'home':
				$canonical = DOMAIN_BASE;
				break;

			case 'page':
				$canonical = $page->permalink($absolute = true);
				break;

			case 'category':
				$canonical = DOMAIN_CATEGORIES . $url->slug();
				break;

			case 'tag':
				$canonical = DOMAIN_TAGS . $url->slug();
				break;

			case 'blog':
				$canonical = DOMAIN_BASE . ltrim($url->uri(), '/');
				break;

			default:
				return '';
		}

		// Each page of a listing is its own canonical, using the same URL as the paginator
		$pageNumber = $url->pageNumber();
		if ($WHERE_AM_I !== 'page' && $pageNumber > 1) {
			$canonical = $canonical . '?page=' . $pageNumber;
		}

		return '<link rel="canonical" href="' . htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
	}
}
