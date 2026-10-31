<?php

class pluginSitemap extends Plugin {

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Sitemap URL').'</label>';
		$html .= '<a href="'.Theme::sitemapUrl().'">'.Theme::sitemapUrl().'</a>';
		$html .= '</div>';

		return $html;
	}

	private function createXML()
	{
		global $site;
		global $pages;

		$xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$xml .= '<url>';
		$xml .= '<loc>'.Text::encodeURL($site->url()).'</loc>';
		$xml .= '</url>';

		$list = $pages->getList($pageNumber=1, $numberOfItems=-1, $published=true, $static=true, $sticky=true, $draft=false, $scheduled=false);
		foreach ($list as $pageKey) {
			try {
				// Create the page object from the page key
				$page = new Page($pageKey);
				if (!$page->noindex()) {
					$lastmod = $page->dateModified(SITEMAP_DATE_FORMAT);
					if ($lastmod === false) {
						$lastmod = $page->date(SITEMAP_DATE_FORMAT);
					}
					$xml .= '<url>';
					$xml .= '<loc>'.Text::encodeURL($page->permalink()).'</loc>';
					$xml .= '<lastmod>'.$lastmod.'</lastmod>';
					$xml .= '</url>';
				}
			} catch (Exception $e) {
				// Continue
			}
		}

		$xml .= '</urlset>';

		return file_put_contents($this->workspace().'sitemap.xml', $xml, LOCK_EX);
	}

	public function install($position=0)
	{
		parent::install($position);
		return $this->createXML();
	}

	public function post()
	{
		parent::post();
		return $this->createXML();
	}

	public function afterPageCreate()
	{
		$this->createXML();
	}

	public function afterPageModify()
	{
		$this->createXML();
	}

	public function afterPageDelete()
	{
		$this->createXML();
	}

	public function beforeAll()
	{
		$webhook = 'sitemap.xml';
		if( $this->webhook($webhook) ) {
			$sitemapFile = $this->workspace().'sitemap.xml';
			if (!Filesystem::fileExists($sitemapFile)) {
				$this->createXML();
			}

			// Send XML header
			header('Content-type: text/xml');
			Filesystem::sendFile($sitemapFile);

			// Terminate the run successfully
			exit(0);
		}
	}
}
