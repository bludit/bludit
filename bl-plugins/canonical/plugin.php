<?php

class pluginCanonical extends Plugin {

	public function siteHead()
	{
		global $url;
		global $page;
		global $WHERE_AM_I;

		$html = '';
		$canonical = '';

		switch ($WHERE_AM_I) {
			case 'home':
				// Handle pagination on homepage
				$pageNumber = $url->pageNumber();
				if ($pageNumber > 1) {
					$canonical = DOMAIN_BASE . 'page/' . $pageNumber . '/';
				} else {
					$canonical = DOMAIN_BASE;
				}
				break;

			case 'page':
				$canonical = $page->permalink($absolute = true);
				break;

			case 'category':
				// Category pages
				$categoryKey = $url->slug();
				$canonical = DOMAIN_CATEGORIES . $categoryKey . '/';
				$pageNumber = $url->pageNumber();
				if ($pageNumber > 1) {
					$canonical .= 'page/' . $pageNumber . '/';
				}
				break;

			case 'tag':
				// Tag pages
				$tagKey = $url->slug();
				$canonical = DOMAIN_TAGS . $tagKey . '/';
				$pageNumber = $url->pageNumber();
				if ($pageNumber > 1) {
					$canonical .= 'page/' . $pageNumber . '/';
				}
				break;

			default:
				// For any other page type, use current URI
				$canonical = DOMAIN_BASE . ltrim($url->uri(), '/');
				break;
		}

		if (!empty($canonical)) {
			$html .= '<link rel="canonical" href="' . $canonical . '">' . PHP_EOL;

			// Add prev/next for paginated content (helps search engines)
			$pageNumber = $url->pageNumber();
			if ($pageNumber > 1) {
				// Previous page
				if ($pageNumber === 2) {
					$prevUrl = preg_replace('/page\/\d+\/?$/', '', $canonical);
				} else {
					$prevUrl = preg_replace('/page\/\d+\/?$/', 'page/' . ($pageNumber - 1) . '/', $canonical);
				}
				$html .= '<link rel="prev" href="' . $prevUrl . '">' . PHP_EOL;
			}

			// Next page (only if more content exists)
			// This requires checking if there's a next page of content
			global $content;
			global $site;
			if (isset($content) && is_array($content) && count($content) >= $site->itemsPerPage()) {
				$nextUrl = preg_replace('/page\/\d+\/?$/', '', $canonical);
				if ($pageNumber < 1) {
					$nextUrl .= 'page/2/';
				} else {
					$nextUrl = preg_replace('/page\/\d+\/?$/', 'page/' . ($pageNumber + 1) . '/', $canonical);
				}
				$html .= '<link rel="next" href="' . $nextUrl . '">' . PHP_EOL;
			}
		}

		return $html;
	}
}
