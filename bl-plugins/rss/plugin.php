<?php

class pluginRSS extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'amountOfItems'=>5
		);
	}

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('RSS URL').'</label>';
		$html .= '<a href="'.Theme::rssUrl().'">'.Theme::rssUrl().'</a>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Amount of items').'</label>';
		$html .= '<input id="jsamountOfItems" name="amountOfItems" type="text" value="'.$this->getValue('amountOfItems').'">';
		$html .= '<span class="tip">'.$Language->get('Amount of items to show on the feed').'</span>';
		$html .= '</div>';

		return $html;
	}

	private function createXML()
	{
		global $Site;
		global $dbPages;
		global $Url;

		// Amount of pages to show
		$amountOfItems = $this->getValue('amountOfItems');

		// Page number the first one
		$pageNumber = 1;

		// Only published pages
		$onlyPublished = true;

		// Get the list of pages
		$pages = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished, true);

		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml .= '<rss version="2.0">';
		$xml .= '<channel>';
		$xml .= '<title>'.$Site->title().'</title>';
		$xml .= '<link>'.$Site->url().'</link>';
		$xml .= '<description>'.$Site->description().'</description>';

		// Get keys of pages
		foreach($pages as $pageKey) {
			// Create the page object from the page key
			$page = buildPage($pageKey);
			$xml .= '<item>';
			$xml .= '<title>'.$page->title().'</title>';
			$xml .= '<link>'.$page->permalink().'</link>';
			$xml .= '<description>'.Sanitize::html($page->contentBreak()).'</description>';
			$xml .= '<pubDate>'.$page->dateRaw('r').'</pubDate>';
			$xml .= '<guid isPermaLink="false">'.$page->uuid().'</guid>';
			$xml .= '</item>';
		}

		$xml .= '</channel></rss>';

		// New DOM document
		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->loadXML($xml);
		$doc->save($this->workspace().'rss.xml');
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

	public function siteHead()
	{
		return '<link rel="alternate" type="application/rss+xml" href="'.DOMAIN_BASE.'rss.xml" title="RSS Feed">'.PHP_EOL;
	}

	public function beforeAll()
	{
		$webhook = 'rss.xml';
		if( $this->webhook($webhook) ) {
			// Send XML header
			header('Content-type: text/xml');
			$doc = new DOMDocument();

			// Load XML
			libxml_disable_entity_loader(false);
			$doc->load($this->workspace().'rss.xml');
			libxml_disable_entity_loader(true);

			// Print the XML
			echo $doc->saveXML();

			// Stop Bludit running
			exit(0);
		}
	}
}