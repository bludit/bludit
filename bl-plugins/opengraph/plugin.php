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

	public function siteHead()
	{
		global $url;
		global $site;
		global $WHERE_AM_I;
		global $page;
		global $content;

		$og = array(
			'locale'	=> $site->locale(),
			'type'		=> 'website',
			'title'		=> $this->sanitize($site->title()),
			'description'	=> $this->sanitize($site->description(), 200),
			'url'		=> $site->url(),
			'image'		=> '',
			'siteName'	=> $this->sanitize($site->title()),
			'publishedTime'	=> '',
			'modifiedTime'	=> '',
			'author'	=> ''
		);

		switch ($WHERE_AM_I) {
			case 'page':
				$og['type'] = 'article';
				$og['title'] = $this->sanitize($page->title());

				// Get description: use page description or fallback to content excerpt
				$description = $page->description();
				if (empty($description)) {
					$description = Text::truncate(strip_tags($page->contentRaw()), 160);
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
				$og['author'] = $this->sanitize($page->user('nickname'));

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
		$html  = PHP_EOL . '<!-- Open Graph -->' . PHP_EOL;
		$html .= '<meta property="og:locale" content="' . $og['locale'] . '">' . PHP_EOL;
		$html .= '<meta property="og:type" content="' . $og['type'] . '">' . PHP_EOL;
		$html .= '<meta property="og:title" content="' . $og['title'] . '">' . PHP_EOL;
		$html .= '<meta property="og:description" content="' . $og['description'] . '">' . PHP_EOL;
		$html .= '<meta property="og:url" content="' . $og['url'] . '">' . PHP_EOL;
		$html .= '<meta property="og:site_name" content="' . $og['siteName'] . '">' . PHP_EOL;

		// Article-specific tags
		if ($og['type'] === 'article') {
			if (!empty($og['publishedTime'])) {
				$html .= '<meta property="article:published_time" content="' . $og['publishedTime'] . '">' . PHP_EOL;
			}
			if (!empty($og['modifiedTime'])) {
				$html .= '<meta property="article:modified_time" content="' . $og['modifiedTime'] . '">' . PHP_EOL;
			}
			if (!empty($og['author'])) {
				$html .= '<meta property="article:author" content="' . $og['author'] . '">' . PHP_EOL;
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
			$html .= '<meta property="og:image" content="' . $og['image'] . '">' . PHP_EOL;
			// Add image alt for accessibility
			$html .= '<meta property="og:image:alt" content="' . $og['title'] . '">' . PHP_EOL;
		}

		if (Text::isNotEmpty($this->getValue('fbAppId'))) {
			$html .= '<meta property="fb:app_id" content="' . $this->getValue('fbAppId') . '">' . PHP_EOL;
		}

		return $html;
	}
}
