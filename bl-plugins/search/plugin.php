<?php

class pluginSearch extends Plugin
{
	// Increase it when the format of the cache changes, it is part of the filename of the cache
	// so the old cache is ignored and the new one is generated on the next load of the admin panel
	const CACHE_VERSION = 2;

	// Full scans of the cache per search, it keeps the cost of any search under a limit
	// Each word of the search is a scan, and each similar word of a typo is one more
	// The words of the search after the limit are ignored
	const MAX_SCANS = 10;

	// Words of the site used for a word of the search that is not on the site (typo)
	const MAX_SIMILAR_WORDS = 3;

	// Score of a match in each field
	const FIELD_WEIGHTS = array(
		'title' => 10,
		'tags' => 6,
		'category' => 4,
		'description' => 3,
		'content' => 1
	);

	// Lowercase letters with accents and their letters without them
	const ACCENTS = array(
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ā' => 'a', 'ă' => 'a', 'ą' => 'a',
		'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ď' => 'd', 'đ' => 'd',
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ē' => 'e', 'ė' => 'e', 'ę' => 'e', 'ě' => 'e',
		'ğ' => 'g', 'ģ' => 'g', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i', 'į' => 'i', 'ı' => 'i',
		'ķ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ļ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n',
		'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'œ' => 'oe',
		'ŕ' => 'r', 'ř' => 'r', 'ś' => 's', 'š' => 's', 'ş' => 's', 'ș' => 's', 'ß' => 'ss',
		'ť' => 't', 'ţ' => 't', 'ț' => 't', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ū' => 'u', 'ů' => 'u', 'ű' => 'u', 'ų' => 'u',
		'ý' => 'y', 'ÿ' => 'y', 'ź' => 'z', 'ż' => 'z', 'ž' => 'z',
		'ά' => 'α', 'έ' => 'ε', 'ή' => 'η', 'ί' => 'ι', 'ϊ' => 'ι', 'ΐ' => 'ι', 'ό' => 'ο', 'ύ' => 'υ', 'ϋ' => 'υ', 'ΰ' => 'υ', 'ώ' => 'ω', 'ς' => 'σ',
		'ё' => 'е'
	);


	private $pagesFound = array();
	private $numberOfItems = 0;
	private $searchTerm = '';
	private $headerTaken = false;
	private $boxShown = false;

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

		$html  = '<form class="search-plugin-form" role="search" data-search-url="' . DOMAIN_BASE . 'search/" data-min-chars="' . (int)$this->getValue('minChars') . '">';
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
		// The theme already printed the search form on this page
		if ($this->boxShown) {
			return '';
		}

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

	// HTML for the themes without sidebar that print the search form in their layout
	public function searchBox()
	{
		$this->boxShown = true;

		// The search results page loads the assets in the head
		if ($this->webhook('search', false, false)) {
			return $this->searchForm('jspluginSearchBox', htmlspecialchars($this->getSearchTerm(), ENT_QUOTES, 'UTF-8'));
		}

		return $this->assets() . $this->searchForm('jspluginSearchBox');
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

	// For the themes that print the search header inside the layout, siteBodyBegin prints nothing after this
	public function takeHeader()
	{
		$html = $this->siteBodyBegin();
		$this->headerTaken = true;
		return $html;
	}

	// Display search header before content
	public function siteBodyBegin()
	{
		$webhook = 'search';
		if ($this->headerTaken || !$this->webhook($webhook, false, false)) {
			return false;
		}

		global $L;
		$searchTerm = $this->getSearchTerm();
		$searchTermEscaped = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
		$count = $this->numberOfItems;

		if (empty($searchTerm)) {
			return '<div class="container"><p class="search-count">' . $L->get('No pages found') . '</p></div>';
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
		$installed = parent::install($position);
		$this->createCache();
		return $installed;
	}

	// Method called when the user click on button save in the settings of the plugin
	public function post()
	{
		$saved = parent::post();
		$this->createCache();
		return $saved;
	}

	// The cache is generated only by the users logged in, never by the visitors of the site
	// It is missing after an update of the plugin with a new version of the cache
	public function beforeAdminLoad()
	{
		global $login;
		if ($login->isLogged() && !file_exists($this->cacheFile())) {
			$this->createCache();
		}
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

	// Generate the cache file, an index with the normalized text of each published page
	// This function is necessary to call it when you create, edit or remove content
	private function createCache()
	{
		global $pages;

		$list = $pages->getList($pageNumber = 1, $numberOfItems = -1, $published = true, $static = true, $sticky = true, $draft = false, $scheduled = false);

		$index = array();
		$words = array();
		foreach ($list as $pageKey) {
			$page = buildPage($pageKey);
			if ($page === false) {
				continue;
			}

			// The tags are replaced with spaces to avoid joining the words of two paragraphs
			$content = strip_tags(str_replace('<', ' <', $page->content()));
			$content = Text::truncate($content, $this->getValue('wordsToCachePerPage') * 5, ''); // Assuming avg of characters per word is 5

			$fields = array(
				'title' => $page->title(),
				'description' => $page->description(),
				'content' => $content
			);

			if ($this->getValue('searchInTags')) {
				$fields['tags'] = implode(' ', $page->tags(true));
			}

			if ($this->getValue('searchInCategories') && $page->categoryKey()) {
				$fields['category'] = $page->category();
			}

			foreach ($fields as $field => $text) {
				$text = $this->normalize($text);
				if ($text === '') {
					continue;
				}
				// Padded with spaces to find whole words with ' word '
				$index[$pageKey][$field] = ' ' . $text . ' ';
				foreach (explode(' ', $text) as $word) {
					$words[$word] = true;
				}
			}
		}

		$cache = array(
			'pages' => $index,
			'words' => array_map('strval', array_keys($words))
		);

		file_put_contents($this->cacheFile(), json_encode($cache), LOCK_EX);

		// Remove the caches of the old versions of the plugin
		foreach (glob($this->workspace() . 'cache*.json') as $file) {
			if ($file !== $this->cacheFile()) {
				unlink($file);
			}
		}
	}

	// Returns the absolute path of the cache file
	private function cacheFile()
	{
		return $this->workspace() . 'cache-v' . self::CACHE_VERSION . '.json';
	}

	// Returns the cache, or FALSE when it is missing or broken
	// The visitors never generate the cache, see beforeAdminLoad()
	private function readCache()
	{
		if (!file_exists($this->cacheFile())) {
			return false;
		}

		$cache = json_decode(file_get_contents($this->cacheFile()), true);
		if (!isset($cache['pages'], $cache['words'])) {
			return false;
		}

		return $cache;
	}

	// Lowercase, without accents and with the punctuation replaced with spaces
	// The same function is used for the content and the search, so "Café," finds "cafe"
	private function normalize($text)
	{
		$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, CHARSET);
		$text = mb_strtolower($text, CHARSET);
		// Accents typed as a letter plus a combining mark are joined into one letter (intl extension)
		if (class_exists('Normalizer')) {
			$text = Normalizer::normalize($text, Normalizer::FORM_C) ?: $text;
		}
		$text = strtr($text, self::ACCENTS);
		// \p{M} keeps the marks of the scripts that need them (Hindi, Thai, etc.) inside the words
		$text = preg_replace('/[^\p{L}\p{N}\p{M}]+/u', ' ', $text);
		return trim($text);
	}

	// Search text inside the cache
	// Returns an array with the pages keys related to the text, sorted by score
	// A page must contain all the words of the search, a word that is not on the site
	// is replaced with the similar words of the site (typos)
	private function search($text)
	{
		$query = $this->normalize($text);
		if ($query === '' || mb_strlen($query, CHARSET) < $this->getValue('minChars')) {
			return array();
		}

		$cache = $this->readCache();
		if ($cache === false) {
			return array();
		}
		$pages = $cache['pages'];

		$scores = null;
		$scans = 0;
		foreach (array_unique(explode(' ', $query)) as $term) {
			if ($scans >= self::MAX_SCANS) {
				break;
			}

			$termScores = $this->scoreTerm($term, $pages);
			$scans++;

			if (empty($termScores)) {
				$similarWords = $this->similarWords($term, $cache['words'], min(self::MAX_SIMILAR_WORDS, self::MAX_SCANS - $scans));
				$scans += count($similarWords);
				foreach ($similarWords as $word) {
					foreach ($this->scoreTerm($word, $pages) as $pageKey => $score) {
						// Half of the score, an exact match is always better than a typo
						$termScores[$pageKey] = max($termScores[$pageKey] ?? 0, $score / 2);
					}
				}
			}

			// Keep only the pages with all the words
			if ($scores === null) {
				$scores = $termScores;
			} else {
				$scores = array_intersect_key($scores, $termScores);
				foreach ($scores as $pageKey => $score) {
					$scores[$pageKey] = $score + $termScores[$pageKey];
				}
			}

			if (empty($scores)) {
				return array();
			}
		}

		// Bonus for the pages with the exact phrase
		if (strpos($query, ' ') !== false) {
			foreach ($scores as $pageKey => $score) {
				foreach ($pages[$pageKey] as $field => $fieldText) {
					if (strpos($fieldText, ' ' . $query . ' ') !== false) {
						$scores[$pageKey] += self::FIELD_WEIGHTS[$field] * 5;
					}
				}
			}
		}

		// The sort is stable, the pages with the same score keep the order of the site
		arsort($scores);
		return array_map('strval', array_keys($scores));
	}

	// Returns the score of each page that contains the term, an array with the page key => score
	private function scoreTerm($term, $pages)
	{
		$scores = array();
		foreach ($pages as $pageKey => $fields) {
			$score = 0;
			foreach ($fields as $field => $text) {
				$count = substr_count($text, $term);
				if ($count === 0) {
					continue;
				}

				// Whole words are better than the start of a word, and the start better than the middle
				$weight = self::FIELD_WEIGHTS[$field];
				if (strpos($text, ' ' . $term . ' ') !== false) {
					$weight *= 3;
				} elseif (strpos($text, ' ' . $term) !== false) {
					$weight *= 2;
				}

				// Repetitions count up to a limit, to avoid long pages ranking first by size
				$score += $weight * min($count, 5);
			}

			if ($score > 0) {
				$scores[$pageKey] = $score;
			}
		}
		return $scores;
	}

	// Returns the closest words of the site that are at 1 typo of the term (2 typos for long terms)
	private function similarWords($term, $words, $limit)
	{
		// levenshtein() cost grows with the length of both strings, long terms are not typos
		// but long text without spaces (Chinese, Japanese, etc.)
		$length = mb_strlen($term, CHARSET);
		if ($limit <= 0 || $length < 4 || $length > 32) {
			return array();
		}

		$maxDistance = $length >= 8 ? 2 : 1;
		$similar = array();

		// levenshtein() counts bytes, for a term with multibyte letters (Cyrillic, Greek, etc.)
		// each letter of both words is replaced with one byte
		$multibyte = strlen($term) !== $length;
		if ($multibyte) {
			$termMap = array();
			$termBytes = $this->singleBytes($term, $termMap);
		}

		foreach ($words as $word) {
			if ($multibyte) {
				// A letter is 4 bytes at most, a quick filter before counting the letters
				if (abs(strlen($word) - strlen($term)) > $maxDistance * 4 || abs(mb_strlen($word, CHARSET) - $length) > $maxDistance) {
					continue;
				}
				$map = $termMap;
				$distance = levenshtein($termBytes, $this->singleBytes($word, $map));
			} else {
				if (abs(strlen($word) - $length) > $maxDistance) {
					continue;
				}
				$distance = levenshtein($term, $word);
			}

			if ($distance <= $maxDistance) {
				$similar[$word] = $distance;
			}
		}

		asort($similar);
		return array_map('strval', array_keys(array_slice($similar, 0, $limit, true)));
	}

	// Returns the text with each letter replaced with one byte, the same letter with the same byte
	// The map is shared by the two words to compare, they are 34 letters at most, under the 256 bytes
	private function singleBytes($text, &$map)
	{
		$bytes = '';
		foreach (mb_str_split($text, 1, CHARSET) as $letter) {
			if (!isset($map[$letter])) {
				$map[$letter] = chr(count($map));
			}
			$bytes .= $map[$letter];
		}
		return $bytes;
	}
}
