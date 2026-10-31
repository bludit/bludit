<?php

class pluginSearch extends Plugin
{

	private $pagesFound = array();
	private $numberOfItems = 0;
	private $searchTerm = '';

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label' => 'Search',
			'minChars' => 3,
			'wordsToCachePerPage' => 800,
			'showButtonSearch' => false,
			'highlightResults' => true,
			'showResultCount' => true,
			'searchInTags' => true,
			'searchInCategories' => true
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Label') . '</label>';
		$html .= '<input name="label" type="text" dir="auto" value="' . $this->getValue('label') . '">';
		$html .= '<span class="tip">' . $L->get('This title is almost always used in the sidebar of the site') . '</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Minimum number of characters when searching') . '</label>';
		$html .= '<input name="minChars" type="text" dir="auto" value="' . $this->getValue('minChars') . '">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Show button search') . '</label>';
		$html .= '<select name="showButtonSearch">';
		$html .= '<option value="true" ' . ($this->getValue('showButtonSearch') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('showButtonSearch') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Highlight search terms in results') . '</label>';
		$html .= '<select name="highlightResults">';
		$html .= '<option value="true" ' . ($this->getValue('highlightResults') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('highlightResults') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'. $L->get('Show result count') . '</label>';
		$html .= '<select name="showResultCount">';
		$html .= '<option value="true" ' . ($this->getValue('showResultCount') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('showResultCount') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Search in tags') . '</label>';
		$html .= '<select name="searchInTags">';
		$html .= '<option value="true" ' . ($this->getValue('searchInTags') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('searchInTags') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Search in categories') . '</label>';
		$html .= '<select name="searchInCategories">';
		$html .= '<option value="true" ' . ($this->getValue('searchInCategories') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('searchInCategories') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	// HTML for the search form, it needs the assets from the method assets()
	private function searchForm($id, $value = '')
	{
		global $L;
		$searchText = $L->get('Search');

		$html  = '<form class="search-plugin-form" role="search" data-search-url="' . DOMAIN_BASE . 'search/">';
		$html .= '<label for="' . $id . '" class="visually-hidden">' . $searchText . '</label>';
		$html .= '<input type="search" id="' . $id . '" value="' . $value . '" placeholder="' . $searchText . '..." autocomplete="off" dir="auto" required minlength="' . (int)$this->getValue('minChars') . '">';
		if ($this->getValue('showButtonSearch')) {
			$html .= '<button type="submit">' . $searchText . '</button>';
		}
		$html .= '</form>';

		return $html;
	}

	// CSS and JS are only loaded on pages where the plugin shows something
	private function assets($highlight = '')
	{
		$html = $this->includeCSS('search.css');
		$html .= Theme::javascript('js/search.js', $this->domainPath(), 'defer data-highlight="' . Sanitize::html($highlight) . '"');
		return $html;
	}

	// HTML for sidebar
	public function siteSidebar()
	{
		$html  = '<div class="plugin plugin-search">';
		$html .= '<h2 class="plugin-label">' . $this->getValue('label') . '</h2>';
		$html .= '<div class="plugin-content">';
		$html .= $this->searchForm('jspluginSearchText');
		$html .= '</div>';
		$html .= '</div>';

		// The search results page loads the assets in the head
		if (!$this->webhook('search', false, false)) {
			$html .= $this->assets();
		}

		return $html;
	}

	// Search results are not useful for search engines
	public function siteHead()
	{
		if (!$this->webhook('search', false, false)) {
			return '';
		}

		$html = '<meta name="robots" content="noindex">' . PHP_EOL;
		$html .= $this->assets($this->getValue('highlightResults') ? $this->getSearchTerm() : '');
		return $html;
	}

	// Display search header before content
	public function siteBodyBegin()
	{
		$webhook = 'search';
		if (!$this->webhook($webhook, false, false)) {
			return false;
		}

		global $L;
		$searchTerm = $this->getSearchTerm();
		$searchTermEscaped = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
		$count = $this->numberOfItems;

		if (empty($searchTerm)) {
			return '';
		}

		$html = '<div class="container">';
		$html .= '<div class="search-results-header">';
		$html .= '<h1>' . $L->get('Search results for') . ' "<span class="search-query">' . $searchTermEscaped . '</span>"</h1>';

		if ($this->getValue('showResultCount')) {
			if ($count === 0) {
				$html .= '<p class="search-count">' . $L->get('No pages found') . '</p>';
			} elseif ($count === 1) {
				$html .= '<p class="search-count">1 ' . strtolower($L->get('result found')) . '</p>';
			} else {
				$html .= '<p class="search-count">' . $count . ' ' . strtolower($L->get('results found')) . '</p>';
			}
		}

		$html .= '</div>';
		$html .= '</div>';

		// If no results, show helpful message
		if ($count === 0) {
			$html .= '<div class="container">';
			$html .= '<div class="search-no-results">';
			$html .= '<h2>' . $L->get('No results found') . '</h2>';
			$html .= '<p>' . sprintf($L->get('no-pages-found-for-the-search'), $searchTermEscaped) . '</p>';
			$html .= '<div class="search-suggestions">';
			$html .= '<h3>' . $L->get('Suggestions') . ':</h3>';
			$html .= '<ul>';
			$html .= '<li>' . $L->get('Check your spelling') . '</li>';
			$html .= '<li>' . $L->get('Try different keywords') . '</li>';
			$html .= '<li>' . $L->get('Try more general keywords') . '</li>';
			$html .= '</ul>';
			$html .= '</div>';
			$html .= '<div class="search-inline-form">';
			$html .= $this->searchForm('jssearchAgain', $searchTermEscaped);
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}

		return $html;
	}

	public function install($position = 0)
	{
		parent::install($position);
		return $this->createCache();
	}

	// Method called when the user click on button save in the settings of the plugin
	public function post()
	{
		parent::post();
		return $this->createCache();
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

	// Get the current search term
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	// Get the number of results
	public function getResultCount()
	{
		return $this->numberOfItems;
	}

	public function beforeAll()
	{
		// Check if the URL match with the webhook
		$webhook = 'search';
		if ($this->webhook($webhook, false, false)) {
			global $site;
			global $url;

			// Change the whereAmI to avoid load pages in the rule 69.pages
			// This is only for performance purpose
			$url->setWhereAmI('search');

			// Get the string to search from the URL
			$stringToSearch = $this->webhook($webhook, true, false);
			$stringToSearch = trim($stringToSearch, '/');
			$stringToSearch = urldecode($stringToSearch);
			$this->searchTerm = $stringToSearch;

			// Search the string in the cache and get all pages with matches
			$list = $this->search($stringToSearch);
			$this->numberOfItems = count($list);

			// Split the content in pages
			// The first page number is 1, so the real is 0
			$realPageNumber = $url->pageNumber() - 1;
			$itemsPerPage = $site->itemsPerPage();
			if ($itemsPerPage <= 0) {
				if ($realPageNumber === 0) {
					$this->pagesFound = $list;
				}
			} else {
				$chunks = array_chunk($list, $itemsPerPage);
				if (isset($chunks[$realPageNumber])) {
					$this->pagesFound = $chunks[$realPageNumber];
				}
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
		global $categories;
		global $tags;

		$list = $pages->getList($pageNumber = 1, $numberOfItems = -1, $published = true, $static = true, $sticky = true, $draft = false, $scheduled = false);

		$cache = array();
		foreach ($list as $pageKey) {
			$page = buildPage($pageKey);

			// Process content
			$words = $this->getValue('wordsToCachePerPage') * 5; // Assuming avg of characters per word is 5
			$content = $page->content();
			$content = Text::removeHTMLTags($content);
			$content = Text::truncate($content, $words, '');

			// Include the page to the cache
			$cache[$pageKey]['title'] = $page->title();
			$cache[$pageKey]['description'] = $page->description();
			$cache[$pageKey]['content'] = $content;

			// Add tags if enabled
			if ($this->getValue('searchInTags')) {
				$pageTags = $page->tags(true);
				if (!empty($pageTags)) {
					$cache[$pageKey]['tags'] = implode(' ', $pageTags);
				}
			}

			// Add category if enabled
			if ($this->getValue('searchInCategories')) {
				$categoryKey = $page->categoryKey();
				if (!empty($categoryKey) && isset($categories)) {
					try {
						$category = new Category($categoryKey);
						$cache[$pageKey]['category'] = $category->name();
					} catch (Exception $e) {
						// Category not found
					}
				}
			}
		}

		// Generate JSON file with the cache
		$json = json_encode($cache);
		return file_put_contents($this->cacheFile(), $json, LOCK_EX);
	}

	// Returns the absolute path of the cache file
	private function cacheFile()
	{
		return $this->workspace() . 'cache.json';
	}

	// Search text inside the cache
	// Returns an array with the pages keys related to the text
	// The array is sorted by score
	private function search($text)
	{
		$text = trim($text);

		// Check minimum characters
		$minChars = $this->getValue('minChars');
		if (mb_strlen($text, 'UTF-8') < $minChars) {
			return array();
		}

		// Read the cache file
		$cacheFile = $this->cacheFile();
		if (!file_exists($cacheFile)) {
			$this->createCache();
		}

		$json = file_get_contents($cacheFile);
		$cache = json_decode($json, true);

		if (empty($cache)) {
			return array();
		}

		// Include Fuzz algorithm
		require_once($this->phpPath() . 'vendors/fuzz.php');
		$fuzz = new Fuzz($cache, 100, 1, true);
		$results = $fuzz->search($text, $this->getValue('minChars'));

		return (array_keys($results));
	}
}
