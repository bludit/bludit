<?php

class pluginSitemap extends Plugin {

	public function init()
	{
		$this->formButtons = false;
	}

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

		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$xml .= '<url>';
		$xml .= '<loc>'.$site->url().'</loc>';
		$xml .= '</url>';

		// Get DB
		$pageNumber = 1;
		$numberOfItems = -1;
		$onlyPublished = true;
		$list = $pages->getList($pageNumber, $numberOfItems, $onlyPublished);

		foreach($list as $pageKey) {
			try {
				// Create the page object from the page key
				$page = new Page($pageKey);
				$xml .= '<url>';
				$xml .= '<loc>'.$page->permalink().'</loc>';
				$xml .= '<lastmod>'.$page->dateRaw(SITEMAP_DATE_FORMAT).'</lastmod>';
				$xml .= '<changefreq>daily</changefreq>';
				$xml .= '</url>';
			} catch (Exception $e) {
				// Continue
			}
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

	public function post()
	{
		// Call the method
		parent::post();

		// After POST request
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
