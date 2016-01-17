<?php

class pluginRSS extends Plugin {

	private function createXML()
	{
		global $Site;
		global $dbPages;
		global $dbPosts;
		global $Url;

		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml .= '<rss version="2.0">';
		$xml .= '<channel>';
		$xml .= '<title>'.$Site->title().'</title>';
		$xml .= '<link>'.$Site->url().'</link>';
		$xml .= '<description>'.$Site->description().'</description>';

		$posts = buildPostsForPage(0, 10, true);
		foreach($posts as $Post)
		{
			$xml .= '<item>';
			$xml .= '<title>'.$Post->title().'</title>';
			$xml .= '<link>'.$Post->permalink(true).'</link>';
			$xml .= '<description>'.$Post->description().'</description>';
			$xml .= '</item>';
		}

		$xml .= '</channel></rss>';

		// New DOM document
		$doc = new DOMDocument();

		// Friendly XML code
		$doc->formatOutput = true;

		$doc->loadXML($xml);

		$doc->save(PATH_PLUGINS_DATABASES.$this->directoryName.DS.'rss.xml');
	}

	public function install($position = 0)
	{
		parent::install($position);

		$this->createXML();
	}

	public function afterPostCreate()
	{
		$this->createXML();
	}

	public function afterPageCreate()
	{
		$this->createXML();
	}

	public function afterPostModify()
	{
		$this->createXML();
	}

	public function afterPageModify()
	{
		$this->createXML();
	}

	public function afterPostDelete()
	{
		$this->createXML();
	}

	public function afterPageDelete()
	{
		$this->createXML();
	}

	public function siteHead()
	{
		$html = '<link rel="alternate" type="application/rss+xml" href="'.DOMAIN_BASE.'rss.xml" title="RSS Feed">'.PHP_EOL;
		return $html;
	}

	public function beforeRulesLoad()
	{
		global $Url;

		if( $Url->uri() === HTML_PATH_ROOT.'rss.xml' )
		{
			// Send XML header
			header('Content-type: text/xml');

			// New DOM document
			$doc = new DOMDocument();

			// Load XML
			$doc->load(PATH_PLUGINS_DATABASES.$this->directoryName.DS.'rss.xml');

			// Print the XML
			echo $doc->saveXML();

			// Stop Bludit running
			exit;
		}
	}

}