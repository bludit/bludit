<?php

class pluginCanonical extends Plugin {

	public function siteHead()
	{
		global $url;
		global $page;
		global $site;
		global $WHERE_AM_I;

		$html = '';
		$canonical = '';
		$listing = false;

		switch ($WHERE_AM_I) {
			case 'home':
				// A static homepage is served at /. Its listing lives on the blog filter.
				if ($site->homepage()) {
					$canonical = DOMAIN_BASE;
					break;
				}
				$listing = true;
				$canonical = $this->listingUrl($url->pageNumber());
				break;

			case 'blog':
			case 'tag':
			case 'category':
				$listing = true;
				$canonical = $this->listingUrl($url->pageNumber());
				break;

			case 'page':
				$canonical = $page->permalink(true);
				break;

			default:
				$canonical = DOMAIN_BASE . ltrim($url->uri(), '/');
				break;
		}

		if (!empty($canonical)) {
			$html .= '<link rel="canonical" href="' . htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;

			// Same href as the theme pager. Do not invent /page/N/.
			if ($listing) {
				if (Paginator::showPrev()) {
					$html .= '<link rel="prev" href="' . htmlspecialchars(Paginator::previousPageUrl(), ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
				}
				if (Paginator::showNext()) {
					$html .= '<link rel="next" href="' . htmlspecialchars(Paginator::nextPageUrl(), ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
				}
			}
		}

		return $html;
	}

	// Page 1 has no query. Later pages use Paginator::numberUrl(), which is ?page=N.
	private function listingUrl($pageNumber)
	{
		$pageNumber = (int) $pageNumber;
		if ($pageNumber > 1) {
			return Paginator::numberUrl($pageNumber);
		}
		return preg_replace('/\?page=1$/', '', Paginator::numberUrl(1));
	}
}
