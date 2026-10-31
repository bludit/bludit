<?php

class pluginTwitterCards extends Plugin
{

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'defaultImage' => '',
			'twitterSite' => '',
			'cardType' => 'summary_large_image'
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('X @username') . '</label>';
		$html .= '<input name="twitterSite" type="text" dir="auto" value="' . $this->getValue('twitterSite') . '" placeholder="@username">';
		$html .= '<span class="tip">' . $L->get('Your X @username for twitter:site attribution') . '</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Card Type') . '</label>';
		$html .= '<select name="cardType">';
		$html .= '<option value="summary_large_image"' . ($this->getValue('cardType') === 'summary_large_image' ? ' selected' : '') . '>' . $L->get('Summary with Large Image (recommended)') . '</option>';
		$html .= '<option value="summary"' . ($this->getValue('cardType') === 'summary' ? ' selected' : '') . '>' . $L->get('Summary') . '</option>';
		$html .= '</select>';
		$html .= '<span class="tip">' . $L->get('Large image cards get better engagement on X') . '</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Default image') . '</label>';
		$html .= '<input id="jsdefaultImage" name="defaultImage" type="text" dir="auto" value="' . $this->getValue('defaultImage') . '" placeholder="https://">';
		$html .= '<span class="tip">' . $L->get('Fallback image when content has no image, with the Open Graph plugin enabled X uses its image instead') . '</span>';
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
		// Truncate if needed (Twitter has limits)
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
		global $content;
		global $page;

		if ($url->notFound()) {
			return '';
		}

		$cardType = $this->getValue('cardType');
		if (empty($cardType)) {
			$cardType = 'summary_large_image';
		}

		$html = '<meta name="twitter:card" content="' . $this->sanitize($cardType) . '">' . PHP_EOL;

		// Add site handle if configured
		if (Text::isNotEmpty($this->getValue('twitterSite'))) {
			$html .= '<meta name="twitter:site" content="' . $this->sanitize($this->getValue('twitterSite')) . '">' . PHP_EOL;
		}

		// X reads og:title, og:description and og:image when the twitter tags are missing
		if (pluginActivated('pluginOpenGraph')) {
			return $html;
		}

		$data = array(
			'title'		=> $this->sanitize($site->title(), 70),
			'description'	=> $this->sanitize($site->description(), 200),
			'image'		=> '',
			'imageAlt'	=> ''
		);

		switch ($WHERE_AM_I) {
			case 'page':
				$data['title'] = $this->sanitize($page->title(), 70);

				// Get description: use page description or fallback to content excerpt
				$description = $page->description();
				if (empty($description)) {
					$description = Text::truncate(strip_tags($page->content()), 160);
				}
				$data['description'] = $this->sanitize($description, 200);
				$data['image'] = $page->coverImage($absolute = true);
				$data['imageAlt'] = $data['title'];

				$pageContent = $page->content();
				break;

			default:
				$pageContent = '';
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$data['image'] = $this->getValue('defaultImage');
				} elseif (isset($content[0])) {
					$data['image'] = $content[0]->coverImage($absolute = true);
					$data['imageAlt'] = $this->sanitize($content[0]->title(), 70);
					$pageContent = $content[0]->content();
				}
				break;
		}

		$html .= '<meta name="twitter:title" content="' . $data['title'] . '">' . PHP_EOL;
		$html .= '<meta name="twitter:description" content="' . $data['description'] . '">' . PHP_EOL;

		// If the page doesn't have a coverImage try to get an image from the HTML content
		if (empty($data['image'])) {
			$src = DOM::getFirstImage($pageContent);
			if ($src !== false) {
				$data['image'] = $src;
			} elseif (Text::isNotEmpty($this->getValue('defaultImage'))) {
				$data['image'] = $this->getValue('defaultImage');
			}
		}

		// Add image with alt text for accessibility
		if (!empty($data['image'])) {
			$html .= '<meta name="twitter:image" content="' . $this->sanitize($data['image']) . '">' . PHP_EOL;
			if (!empty($data['imageAlt'])) {
				$html .= '<meta name="twitter:image:alt" content="' . $data['imageAlt'] . '">' . PHP_EOL;
			}
		}

		return $html;
	}
}
