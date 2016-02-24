<?php

class pluginSitemap extends Plugin {

	private function createXML()
	{
		global $Site;
		global $dbPages;
		global $dbPosts;
		global $Url;

		$doc = new DOMDocument('1.0', 'UTF-8');

		// Friendly XML code
		$doc->formatOutput = true;

		// Create urlset element
		$urlset = $doc->createElement('urlset');
		$attribute = $doc->createAttribute('xmlns');
		$attribute->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
		$urlset->appendChild($attribute);

		// --- Base URL ---

		// Create url, loc and lastmod elements
		$url 		= $doc->createElement('url');
		$loc 		= $doc->createElement('loc', $Site->url());
		$lastmod	= $doc->createElement('lastmod', '');

		// Append loc and lastmod -> url
		$url->appendChild($loc);
		$url->appendChild($lastmod);

		// Append url -> urlset
		$urlset->appendChild($url);

		// --- Pages and Posts ---
		$all = array();
		$url = trim($Site->url(),'/');

		// --- Pages ---
		$filter = trim($Url->filters('page'),'/');
		$pages = $dbPages->getDB();
		unset($pages['error']);
		foreach($pages as $key=>$db)
		{
			if($db['status']=='published')
			{
				$permalink = empty($filter) ? $url.'/'.$key : $url.'/'.$filter.'/'.$key;
				$date = Date::format($db['date'], DB_DATE_FORMAT, SITEMAP_DATE_FORMAT);
				array_push($all, array('permalink'=>$permalink, 'date'=>$date));
			}
		}

		// --- Posts ---
		$filter = rtrim($Url->filters('post'),'/');
		$posts = $dbPosts->getDB();
		foreach($posts as $key=>$db)
		{
			if($db['status']=='published')
			{
				$permalink = empty($filter) ? $url.'/'.$key : $url.'/'.$filter.'/'.$key;
				$date = Date::format($db['date'], DB_DATE_FORMAT, SITEMAP_DATE_FORMAT);
				array_push($all, array('permalink'=>$permalink, 'date'=>$date));
			}
		}

		// Generate the XML for posts and pages
		foreach($all as $db)
		{
			// Create url, loc and lastmod elements
			$url 		= $doc->createElement('url');
			$loc 		= $doc->createElement('loc', $db['permalink']);
			$lastmod	= $doc->createElement('lastmod', $db['date']);

			// Append loc and lastmod -> url
			$url->appendChild($loc);
			$url->appendChild($lastmod);

			// Append url -> urlset
			$urlset->appendChild($url);
		}

		// Append urlset -> XML
		$doc->appendChild($urlset);

		$doc->save(PATH_PLUGINS_DATABASES.$this->directoryName.DS.'sitemap.xml');
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

	public function beforeRulesLoad()
	{
		global $Url;

		if( $Url->uri() === HTML_PATH_ROOT.'sitemap.xml' )
		{
			// Send XML header
			header('Content-type: text/xml');

			// New DOM document
			$doc = new DOMDocument();

			// Load XML
			$doc->load(PATH_PLUGINS_DATABASES.$this->directoryName.DS.'sitemap.xml');

			// Print the XML
			echo $doc->saveXML();

			// Stop Bludit running
			exit;
		}
	}

}