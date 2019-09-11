<?php

class pluginSitemap extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'pingGoogle'=>false,
			'pingBing'=>false
		);
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

		$html .= '<div>';
		$html .= '<label>Ping Google</label>';
		$html .= '<select name="pingGoogle">';
		$html .= '<option value="true" '.($this->getValue('pingGoogle')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('pingGoogle')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">'.$L->get('notifies-google-when-you-created').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Ping Bing</label>';
		$html .= '<select name="pingBing">';
		$html .= '<option value="true" '.($this->getValue('pingBing')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('pingBing')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">'.$L->get('notifies-bing-when-you-created').'</span>';
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

		$list = $pages->getList($pageNumber=1, $numberOfItems=-1, $published=true, $static=true, $sticky=true, $draft=false, $scheduled=false);
		foreach ($list as $pageKey) {
			try {
				// Create the page object from the page key
				$page = new Page($pageKey);
				if (!$page->noindex()) {
					$xml .= '<url>';
					$xml .= '<loc>'.$page->permalink().'</loc>';
					$xml .= '<lastmod>'.$page->date(SITEMAP_DATE_FORMAT).'</lastmod>';
					$xml .= '</url>';
				}
			} catch (Exception $e) {
				// Continue
			}
		}

		$xml .= '</urlset>';

		// New DOM document
		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->loadXML($xml);
		return $doc->save($this->workspace().'sitemap.xml');
	}

	private function ping()
	{
		if ($this->getValue('pingGoogle')) {
			$url = 'https://www.google.com/ping?sitemap='.Theme::sitemapUrl();
			TCP::http($url, 'GET', true, 3);
		}

		if ($this->getValue('pingBing')) {
			$url = 'https://www.bing.com/ping?sitemap='.Theme::sitemapUrl();
			TCP::http($url, 'GET', true, 3);
		}
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
		$this->ping();
	}

	public function afterPageModify()
	{
		$this->createXML();
		$this->ping();
	}

	public function afterPageDelete()
	{
		$this->createXML();
		$this->ping();
	}

	public function beforeAll()
	{
		$webhook = 'sitemap.xml';
		if( $this->webhook($webhook) ) {
			$sitemapFile = $this->workspace().'sitemap.xml';
			$sitemapSize = filesize($sitemapFile);

			// Send XML header
			header('Content-type: text/xml');
			header('Content-length: '.$sitemapSize);

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
