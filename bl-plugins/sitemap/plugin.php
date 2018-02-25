<?php

class pluginSitemap extends Plugin {

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Sitemap URL').'</label>';
		$html .= '<a href="'.Theme::sitemapUrl().'">'.Theme::sitemapUrl().'</a>';
		$html .= '</div>';

		return $html;
	}

	private function createXML()
	{
		global $Site;
		global $dbPages;

		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$xml .= '<url>';
		$xml .= '<loc>'.$Site->url().'</loc>';
		$xml .= '</url>';

		// Get DB
		$pageNumber = 1;
		$amountOfItems = -1;
		$onlyPublished = true;
		$pages = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);

		foreach($pages as $pageKey) {
			// Create the page object from the page key
			$page = buildPage($pageKey);

			$xml .= '<url>';
			$xml .= '<loc>'.$page->permalink().'</loc>';
			$xml .= '<lastmod>'.$page->dateRaw(SITEMAP_DATE_FORMAT).'</lastmod>';
			$xml .= '<changefreq>daily</changefreq>';
			$xml .= '</url>';
		}

		$xml .= '</urlset>';

		// New DOM document
		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->loadXML($xml);
		$doc->save($this->workspace().'sitemap.xml');
	}

	public function install($position=0)
	{
		parent::install($position);
		$this->createXML();
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
			// Send XML header
			header('Content-type: text/xml');
			$doc = new DOMDocument();

			// Workaround for a bug https://bugs.php.net/bug.php?id=62577
			libxml_disable_entity_loader(false);

			// Load XML
			$doc->load($this->workspace().'sitemap.xml');

			libxml_disable_entity_loader(true);

			// Print the XML
			echo $doc->saveXML();

			// Terminate the run successfully
			exit(0);
		}
	}
}