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
			'searchInCategories' => true,
			'excerptLength' => 200
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
		$html .= '<label>Highlight search terms in results</label>';
		$html .= '<select name="highlightResults">';
		$html .= '<option value="true" ' . ($this->getValue('highlightResults') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('highlightResults') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Show result count</label>';
		$html .= '<select name="showResultCount">';
		$html .= '<option value="true" ' . ($this->getValue('showResultCount') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('showResultCount') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Search in tags</label>';
		$html .= '<select name="searchInTags">';
		$html .= '<option value="true" ' . ($this->getValue('searchInTags') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('searchInTags') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Search in categories</label>';
		$html .= '<select name="searchInCategories">';
		$html .= '<option value="true" ' . ($this->getValue('searchInCategories') === true ? 'selected' : '') . '>' . $L->get('enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('searchInCategories') === false ? 'selected' : '') . '>' . $L->get('disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Excerpt length (characters)</label>';
		$html .= '<input name="excerptLength" type="number" value="' . $this->getValue('excerptLength') . '" min="50" max="500">';
		$html .= '</div>';

		return $html;
	}

	// HTML for sidebar
	public function siteSidebar()
	{
		global $L;
		$label = $this->getValue('label');
		$labelEscaped = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
		$searchText = $L->get('Search');

		$html  = '<div class="plugin plugin-search">';
		$html .= '<h2 class="plugin-label">' . $labelEscaped . '</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<form class="search-plugin-form" role="search" onsubmit="return pluginSearchSubmit()">';
		$html .= '<label for="jspluginSearchText" class="sr-only visually-hidden">' . $searchText . '</label>';
		$html .= '<input type="search" id="jspluginSearchText" name="search" placeholder="' . $searchText . '..." autocomplete="off" dir="auto" aria-label="' . $searchText . '">';
		if ($this->getValue('showButtonSearch')) {
			$html .= '<button type="submit">' . $searchText . '</button>';
		}
		$html .= '</form>';
		$html .= '</div>';
		$html .= '</div>';

		$DOMAIN_BASE = DOMAIN_BASE;
		$minChars = $this->getValue('minChars');
		$html .= <<<EOF
<script>
function pluginSearchSubmit() {
	var text = document.getElementById("jspluginSearchText").value.trim();
	if (text.length < {$minChars}) {
		alert("Please enter at least {$minChars} characters to search.");
		return false;
	}
	window.location.href = '{$DOMAIN_BASE}search/' + encodeURIComponent(text);
	return false;
}
</script>
EOF;

		return $html;
	}

	// Inject CSS styles for search results (works with any theme)
	public function siteHead()
	{
		$css = <<<'CSS'
<style>
/* Search Plugin Styles - Theme agnostic */
.search-results-header {
	padding: 2rem 0;
	margin-bottom: 1.5rem;
	border-bottom: 1px solid rgba(0,0,0,0.1);
}
.search-results-header h1 {
	font-size: 1.75rem;
	margin: 0 0 0.5rem 0;
}
.search-results-header .search-query {
	color: #0071e3;
	font-weight: 600;
}
.search-results-header .search-count {
	color: #6e6e73;
	font-size: 0.9375rem;
	margin: 0;
}
.search-no-results {
	text-align: center;
	padding: 3rem 1rem;
}
.search-no-results h2 {
	font-size: 1.5rem;
	margin-bottom: 0.75rem;
}
.search-no-results p {
	color: #6e6e73;
	margin-bottom: 1.5rem;
}
.search-no-results .search-suggestions {
	text-align: left;
	max-width: 400px;
	margin: 0 auto;
}
.search-no-results .search-suggestions h3 {
	font-size: 1rem;
	margin-bottom: 0.5rem;
}
.search-no-results .search-suggestions ul {
	padding-left: 1.25rem;
	color: #6e6e73;
}
.search-no-results .search-suggestions li {
	margin-bottom: 0.25rem;
}
.search-highlight {
	background-color: rgba(255, 230, 0, 0.4);
	padding: 0.1em 0.2em;
	border-radius: 2px;
	font-weight: 500;
}
.search-result-excerpt {
	color: #1d1d1f;
	line-height: 1.6;
}
.search-inline-form {
	max-width: 400px;
	margin: 1.5rem auto 0;
}
.search-inline-form input[type="search"] {
	width: 100%;
	padding: 0.75rem 1rem;
	font-size: 1rem;
	border: 1px solid rgba(0,0,0,0.1);
	border-radius: 8px;
}
.search-inline-form input[type="search"]:focus {
	outline: none;
	border-color: #0071e3;
	box-shadow: 0 0 0 4px rgba(0,113,227,0.1);
}
/* Sidebar search form styling */
.plugin-search .search-plugin-form {
	display: flex;
	gap: 0.5rem;
}
.plugin-search .search-plugin-form input[type="search"] {
	flex: 1;
	padding: 0.5rem 0.75rem;
	border: 1px solid rgba(0,0,0,0.15);
	border-radius: 6px;
	font-size: 0.9375rem;
}
.plugin-search .search-plugin-form input[type="search"]:focus {
	outline: none;
	border-color: #0071e3;
}
.plugin-search .search-plugin-form button {
	padding: 0.5rem 1rem;
	background: #0071e3;
	color: white;
	border: none;
	border-radius: 6px;
	cursor: pointer;
	font-size: 0.875rem;
}
.plugin-search .search-plugin-form button:hover {
	background: #0077ed;
}
/* Dark mode support */
@media (prefers-color-scheme: dark) {
	.search-results-header {
		border-bottom-color: rgba(255,255,255,0.1);
	}
	.search-results-header .search-count,
	.search-no-results p,
	.search-no-results .search-suggestions ul {
		color: #a1a1a6;
	}
	.search-result-excerpt {
		color: #f5f5f7;
	}
	.search-highlight {
		background-color: rgba(255, 214, 10, 0.3);
	}
	.search-inline-form input[type="search"],
	.plugin-search .search-plugin-form input[type="search"] {
		background: #1d1d1f;
		border-color: rgba(255,255,255,0.1);
		color: #f5f5f7;
	}
}
/* Accessibility - screen reader only class */
.sr-only, .visually-hidden {
	position: absolute;
	width: 1px;
	height: 1px;
	padding: 0;
	margin: -1px;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
	white-space: nowrap;
	border: 0;
}
</style>
CSS;

		// Add JavaScript for automatic highlighting on search results page
		$webhook = 'search';
		if ($this->webhook($webhook, false, false) && $this->getValue('highlightResults')) {
			$searchTerm = $this->getSearchTerm();
			$searchTermJS = json_encode($searchTerm);

			$js = '<script>' . PHP_EOL;
			$js .= 'document.addEventListener("DOMContentLoaded", function() {' . PHP_EOL;
			$js .= '  var searchTerm = ' . $searchTermJS . ';' . PHP_EOL;
			$js .= '  if (!searchTerm || searchTerm.length < 2) return;' . PHP_EOL;
			$js .= '  var words = searchTerm.toLowerCase().split(/\\s+/).filter(function(w) { return w.length >= 2; });' . PHP_EOL;
			$js .= '  if (words.length === 0) return;' . PHP_EOL;
			$js .= '  var articles = document.querySelectorAll("article, main, [role=main], .content");' . PHP_EOL;
			$js .= '  articles.forEach(function(article) {' . PHP_EOL;
			$js .= '    if (article.closest(".search-results-header") || article.closest("form")) return;' . PHP_EOL;
			$js .= '    var walker = document.createTreeWalker(article, NodeFilter.SHOW_TEXT, null, false);' . PHP_EOL;
			$js .= '    var textNodes = [];' . PHP_EOL;
			$js .= '    while(walker.nextNode()) {' . PHP_EOL;
			$js .= '      var parent = walker.currentNode.parentNode;' . PHP_EOL;
			$js .= '      if (parent && (parent.tagName === "SCRIPT" || parent.tagName === "STYLE" || parent.tagName === "MARK")) continue;' . PHP_EOL;
			$js .= '      textNodes.push(walker.currentNode);' . PHP_EOL;
			$js .= '    }' . PHP_EOL;
			$js .= '    textNodes.forEach(function(textNode) {' . PHP_EOL;
			$js .= '      var text = textNode.nodeValue;' . PHP_EOL;
			$js .= '      var escapedWords = words.map(function(w) { return w.replace(/[.*+?^${}()|[\\]\\\\]/g, "\\\\$&"); });' . PHP_EOL;
			$js .= '      var pattern = new RegExp("(" + escapedWords.join("|") + ")", "gi");' . PHP_EOL;
			$js .= '      if (pattern.test(text)) {' . PHP_EOL;
			$js .= '        var span = document.createElement("span");' . PHP_EOL;
			$js .= '        span.innerHTML = text.replace(pattern, "<mark class=\\"search-highlight\\">$1</mark>");' . PHP_EOL;
			$js .= '        textNode.parentNode.replaceChild(span, textNode);' . PHP_EOL;
			$js .= '      }' . PHP_EOL;
			$js .= '    });' . PHP_EOL;
			$js .= '  });' . PHP_EOL;
			$js .= '});' . PHP_EOL;
			$js .= '</script>' . PHP_EOL;

			$css .= $js;
		}

		return $css;
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
			$DOMAIN_BASE = DOMAIN_BASE;
			$minChars = $this->getValue('minChars');
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
			$html .= '<form role="search" onsubmit="return searchAgain()">';
			$html .= '<input type="search" id="searchAgainInput" value="' . $searchTermEscaped . '" placeholder="' . $L->get('Search') . '..." aria-label="' . $L->get('Search') . '">';
			$html .= '</form>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= <<<EOF
<script>
function searchAgain() {
	var text = document.getElementById("searchAgainInput").value.trim();
	if (text.length < {$minChars}) return false;
	window.location.href = '{$DOMAIN_BASE}search/' + encodeURIComponent(text);
	return false;
}
</script>
EOF;
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

	/**
	 * Highlight search terms in text
	 */
	public function highlightTerms($text, $searchTerm)
	{
		if (!$this->getValue('highlightResults') || empty($searchTerm)) {
			return $text;
		}

		// Split search term into words
		$words = preg_split('/\s+/', $searchTerm);
		$words = array_filter($words, function($word) {
			return mb_strlen($word, 'UTF-8') >= 2;
		});

		if (empty($words)) {
			return $text;
		}

		// Escape special regex characters and create pattern
		$escapedWords = array_map(function($word) {
			return preg_quote($word, '/');
		}, $words);

		$pattern = '/(' . implode('|', $escapedWords) . ')/iu';

		return preg_replace($pattern, '<mark class="search-highlight">$1</mark>', $text);
	}

	/**
	 * Get excerpt around the search term
	 */
	public function getSearchExcerpt($content, $searchTerm, $length = null)
	{
		if ($length === null) {
			$length = $this->getValue('excerptLength');
		}

		$content = strip_tags($content);
		$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

		// Find the first occurrence of any search word
		$words = preg_split('/\s+/', $searchTerm);
		$words = array_filter($words, function($word) {
			return mb_strlen($word, 'UTF-8') >= 2;
		});

		$firstPos = mb_strlen($content, 'UTF-8');
		foreach ($words as $word) {
			$pos = mb_stripos($content, $word, 0, 'UTF-8');
			if ($pos !== false && $pos < $firstPos) {
				$firstPos = $pos;
			}
		}

		// Calculate start position (center the found term)
		$start = max(0, $firstPos - (int)($length / 2));

		// Adjust to not cut words
		if ($start > 0) {
			$spacePos = mb_strpos($content, ' ', $start, 'UTF-8');
			if ($spacePos !== false && $spacePos < $start + 20) {
				$start = $spacePos + 1;
			}
		}

		// Extract excerpt
		$excerpt = mb_substr($content, $start, $length, 'UTF-8');

		// Clean up beginning and end
		if ($start > 0) {
			$excerpt = '...' . $excerpt;
		}
		if ($start + $length < mb_strlen($content, 'UTF-8')) {
			// Don't cut in the middle of a word
			$lastSpace = mb_strrpos($excerpt, ' ', 0, 'UTF-8');
			if ($lastSpace !== false && $lastSpace > $length - 30) {
				$excerpt = mb_substr($excerpt, 0, $lastSpace, 'UTF-8');
			}
			$excerpt .= '...';
		}

		return $excerpt;
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
					$tagNames = array_map(function($tag) {
						return $tag->name();
					}, $pageTags);
					$cache[$pageKey]['tags'] = implode(' ', $tagNames);
				}
			}

			// Add category if enabled
			if ($this->getValue('searchInCategories')) {
				$categoryKey = $page->category();
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
