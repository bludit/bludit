<?php

class pluginOpenGraph extends Plugin
{

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'defaultImage' => '',
			'fbAppId' => ''
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Default image') . '</label>';
		$html .= '<input id="jsdefaultImage" name="defaultImage" type="text" dir="auto" value="' . $this->getValue('defaultImage') . '" placeholder="https://">';
		$html .= '<span class="tip">' . $L->g('set-a-default-image-for-content') . '</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Facebook App ID') . '</label>';
		$html .= '<input name="fbAppId" type="text" dir="auto" value="' . $this->getValue('fbAppId') . '" placeholder="App ID">';
		$html .= '<span class="tip">' . $L->g('set-your-facebook-app-id') . '</span>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Sanitize and escape content for use in meta tags
	 */
	private function sanitize($text, $maxLength = 0)
	{
		// Strip HTML tags
		$text = strip_tags($text);
		// Decode HTML entities first to avoid double encoding
		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		// Trim whitespace
		$text = trim($text);
		// Truncate if needed
		if ($maxLength > 0 && mb_strlen($text, 'UTF-8') > $maxLength) {
			$text = mb_substr($text, 0, $maxLength, 'UTF-8') . '...';
		}
		// Escape for HTML attribute
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}

	// Returns the first language_TERRITORY value from the site locale list
	private function locale()
	{
		global $site;

		if (preg_match('/(?<![A-Za-z])[a-z]{2,3}_[A-Z]{2}(?![A-Za-z])/', $site->locale(), $match)) {
			return $match[0];
		}
		return '';
	}

	public function siteHead()
	{
		global $url;
		global $site;
		global $WHERE_AM_I;
		global $page;
		global $content;

		if ($url->notFound()) {
			return '';
		}

		$og = array(
			'locale'	=> $this->locale(),
			'type'		=> 'website',
			'title'		=> $this->sanitize($site->title()),
			'description'	=> $this->sanitize($site->description(), 200),
			'url'		=> $site->url(),
			'image'		=> '',
			'siteName'	=> $this->sanitize($site->title()),
			'publishedTime'	=> '',
			'modifiedTime'	=> ''
		);

		switch ($WHERE_AM_I) {
			case 'page':
				$og['type'] = 'article';
				$og['title'] = $this->sanitize($page->title());

				// Get description: use page description or fallback to content excerpt
				$description = $page->description();
				if (empty($description)) {
					$description = Text::truncate(strip_tags($page->content()), 160);
				}
				$og['description'] = $this->sanitize($description, 200);

				$og['url'] = $page->permalink($absolute = true);
				$og['image'] = $page->coverImage($absolute = true);

				// Article-specific meta tags (ISO 8601 format)
				$og['publishedTime'] = $page->date('c');
				$dateModified = $page->dateModified('c');
				if (!empty($dateModified)) {
					$og['modifiedTime'] = $dateModified;
				}

				$pageContent = $page->content();
				break;

			default:
				$pageContent = '';
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$og['image'] = $this->getValue('defaultImage');
				} elseif (isset($content[0])) {
					$og['image'] = $content[0]->coverImage($absolute = true);
					$pageContent = $content[0]->content();
				}
				break;
		}

		// Build HTML output
		$html = '';
		if (!empty($og['locale'])) {
			$html .= '<meta property="og:locale" content="' . $og['locale'] . '">' . PHP_EOL;
		}
		$html .= '<meta property="og:type" content="' . $og['type'] . '">' . PHP_EOL;
		$html .= '<meta property="og:title" content="' . $og['title'] . '">' . PHP_EOL;
		$html .= '<meta property="og:description" content="' . $og['description'] . '">' . PHP_EOL;
		$html .= '<meta property="og:url" content="' . $this->sanitize($og['url']) . '">' . PHP_EOL;
		$html .= '<meta property="og:site_name" content="' . $og['siteName'] . '">' . PHP_EOL;

		// Article-specific tags
		if ($og['type'] === 'article') {
			if (!empty($og['publishedTime'])) {
				$html .= '<meta property="article:published_time" content="' . $og['publishedTime'] . '">' . PHP_EOL;
			}
			if (!empty($og['modifiedTime'])) {
				$html .= '<meta property="article:modified_time" content="' . $og['modifiedTime'] . '">' . PHP_EOL;
			}
		}

		// If the page doesn't have a coverImage try to get an image from the HTML content
		if (empty($og['image'])) {
			$src = DOM::getFirstImage($pageContent);
			if ($src !== false) {
				$og['image'] = $src;
			} elseif (Text::isNotEmpty($this->getValue('defaultImage'))) {
				$og['image'] = $this->getValue('defaultImage');
			}
		}

		if (!empty($og['image'])) {
			$html .= '<meta property="og:image" content="' . $this->sanitize($og['image']) . '">' . PHP_EOL;
			// Add image alt for accessibility
			$html .= '<meta property="og:image:alt" content="' . $og['title'] . '">' . PHP_EOL;
		}

		if (Text::isNotEmpty($this->getValue('fbAppId'))) {
			$html .= '<meta property="fb:app_id" content="' . $this->sanitize($this->getValue('fbAppId')) . '">' . PHP_EOL;
		}

		return $html;
	}
}
