<?php

class pluginJsonLd extends Plugin {

	public function siteHead()
	{
		global $url;
		global $page;
		global $WHERE_AM_I;

		if ($url->notFound()) {
			return '';
		}

		if ($WHERE_AM_I === 'home') {
			return $this->script($this->webSiteSchema());
		}

		if ($WHERE_AM_I === 'page' && !$page->isStatic() && !$page->noindex()) {
			return $this->script($this->blogPostingSchema($page)) . $this->script($this->breadcrumbListSchema($page));
		}

		return '';
	}

	private function webSiteSchema()
	{
		global $site;

		$data = array(
			'@context' => 'https://schema.org',
			'@type' => 'WebSite',
			'name' => $site->title(),
			'url' => DOMAIN_BASE
		);

		if ($site->description()) {
			$data['description'] = $site->description();
		}

		return $data;
	}

	private function blogPostingSchema($page)
	{
		global $site;

		$publisher = array(
			'@type' => 'Organization',
			'name' => $site->title()
		);
		if ($site->logo()) {
			$publisher['logo'] = array(
				'@type' => 'ImageObject',
				'url' => $site->logo()
			);
		}

		$data = array(
			'@context' => 'https://schema.org',
			'@type' => 'BlogPosting',
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id' => $page->permalink()
			),
			'headline' => $page->title(),
			'datePublished' => $page->date('c'),
			'url' => $page->permalink(),
			'publisher' => $publisher
		);

		if ($page->description()) {
			$data['description'] = $page->description();
		}

		if ($page->dateModified()) {
			$data['dateModified'] = $page->dateModified('c');
		}

		if ($page->coverImage()) {
			$data['image'] = array(
				'@type' => 'ImageObject',
				'url' => $page->coverImage(true)
			);
		}

		if ($page->user('nickname')) {
			$data['author'] = array(
				'@type' => 'Person',
				'name' => $page->user('nickname')
			);
		}

		$tags = $page->tags(true);
		if (!empty($tags)) {
			$data['keywords'] = implode(', ', array_values($tags));
		}

		if ($page->readingTime()) {
			$data['timeRequired'] = 'PT' . max(1, intval($page->readingTime())) . 'M';
		}

		return $data;
	}

	private function breadcrumbListSchema($page)
	{
		global $site;

		$items = array(array($site->title(), DOMAIN_BASE));
		if ($page->categoryKey()) {
			$items[] = array($page->category(), $page->categoryPermalink());
		}
		$items[] = array($page->title(), $page->permalink());

		$list = array();
		foreach ($items as $index => $item) {
			$list[] = array(
				'@type' => 'ListItem',
				'position' => $index + 1,
				'name' => $item[0],
				'item' => $item[1]
			);
		}

		return array(
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => $list
		);
	}

	private function script($data)
	{
		// Bludit stores text with HTML entities, JSON-LD needs the plain text
		array_walk_recursive($data, function (&$value) {
			if (is_string($value)) {
				$value = Sanitize::htmlDecode($value);
			}
		});

		$json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
		return '<script type="application/ld+json">' . $json . '</script>' . PHP_EOL;
	}
}
