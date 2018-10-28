<?php

class pluginSearch extends Plugin {

	private $pagesFound = array();
	private $numberOfItems = 0;

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Search',
			'wordsToCachePerPage'=>800
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Label').'</label>';
		$html .= '<input name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$L->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		return $html;
	}

	// HTML for sidebar
	public function siteSidebar()
	{
		global $L;

		$html  = '<div class="plugin plugin-search">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<input type="text" id="plugin-search-input" /> ';
		$html .= '<input type="button" value="'.$L->get('Search').'" onClick="javascript: window.open(\''.DOMAIN_BASE.'search/\' + document.getElementById(\'plugin-search-input\').value, \'_self\');" />';
		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}

	public function install($position=0)
	{
		parent::install($position);
		return $this->createCache();
	}

	// Method called when the user click on button save in the settings of the plugin
	public function post()
	{
		parent::post();
		$this->createCache();
	}

	public function afterPageCreate()
	{
		$this->createCache();
	}

	public function afterPageModify()
	{
		$this->createCache();
	}

	public function afterPageDelete()
	{
		$this->createCache();
	}

	public function beforeAll()
	{
		// Check if the URL match with the webhook
		$webhook = 'search';
		if ($this->webhook($webhook, false, false)) {
			global $site;
			global $url;

			// Change the whereAmI to avoid load pages in the rule 69.pages
			// This is only for performance propose
			$url->setWhereAmI('search');

			// Get the string to search from the URL
			$stringToSearch = $this->webhook($webhook, true, false);
			$stringToSearch = trim($stringToSearch, '/');

			// Search the string in the cache and get all pages with matches
			$list = $this->search($stringToSearch);

			$this->numberOfItems = count($list);

			// Split the content in pages
			// The first page number is 1, so the real is 0
			$realPageNumber = $url->pageNumber() - 1;
			$itemsPerPage = $site->itemsPerPage();
			$chunks = array_chunk($list, $itemsPerPage);
			if (isset($chunks[$realPageNumber])) {
				$this->pagesFound = $chunks[$realPageNumber];
			}
		}
	}

	public function paginator()
	{
		$webhook = 'search';
		if ($this->webhook($webhook, false, false)) {
			// Get the pre-defined variable from the rule 99.paginator.php
			// Is necessary to change this variable to fit the paginator with the result from the search
			global $numberOfItems;
			$numberOfItems = $this->numberOfItems;
		}
	}

	public function beforeSiteLoad()
	{
		$webhook = 'search';
		if ($this->webhook($webhook, false, false)) {

			global $url;
			global $WHERE_AM_I;
			$WHERE_AM_I = 'search';

			// Get the pre-defined variable from the rule 69.pages.php
			// We change the content to show in the website
			global $content;
			$content = array();
			foreach ($this->pagesFound as $pageKey) {
				try {
					$page = new Page($pageKey);
					array_push($content, $page);
				} catch (Exception $e) {
					// continue
				}
			}
		}
	}

	// Generate the cache file
	// This function is necessary to call it when you create, edit or remove content
	private function createCache()
	{
		// Get all pages published
		global $pages;
		$list = $pages->getList($pageNumber = 1, $numberOfItems = -1, $onlyPublished = true);

		$cache = array();
		foreach ($list as $pageKey) {
			$page = buildPage($pageKey);

			// Process content
			$words = $this->getValue('wordsToCachePerPage') * 5; // Asumming avg of characters per word is 5
			$content = $page->content();
			$content = Text::removeHTMLTags($content);
			$content = Text::truncate($content, $words, '');

			// Include the page to the cache
			$cache[$pageKey]['title'] = $page->title();
			$cache[$pageKey]['description'] = $page->description();
			$cache[$pageKey]['content'] = $content;
			$cache[$pageKey]['key'] = $pageKey;
		}

		// Generate JSON file with the cache
		$json = json_encode($cache);
		return file_put_contents($this->cacheFile(), $json, LOCK_EX);
	}

	// Returns the absolute path of the cache file
	private function cacheFile()
	{
		return $this->workspace().'cache.json';
	}

	// Search text inside the cache
	// Returns an array with the pages keys related to the text
	// The array is sorted by score
	private function search($text)
	{
		// Read the cache file
		$json = file_get_contents($this->cacheFile());
		$cache = json_decode($json, true);

		$found = array();
		foreach ($cache as $page) {
			$score = 0;
			if (Text::stringContains($page['title'], $text, false)) {
				$score += 10;
			}
			if (Text::stringContains($page['description'], $text, false)) {
				$score += 7;
			}
			if (Text::stringContains($page['content'], $text, false)) {
				$score += 5;
			}
			if ($score>0) {
				$found[$page['key']] = $score;
			}
		}

		// Sort array by the score, from max to min
		arsort($found);
		// Returns only the keys of the array contained the page key
		return array_keys($found);
	}

}
