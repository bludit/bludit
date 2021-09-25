<?php

class pluginOpenGraph extends Plugin {

	public function init() {
		$this->dbFields = array(
			'defaultImage'=>'',
			'fbAppId'=>''
		);
	}

	public function form() {
		global $L;

        $html  = '<div class="mb-3">';
        $html .= '<label class="form-label" for="defaultImage">'.$L->get('Default image').'</label>';
        $html .= '<input class="form-control" id="defaultImage" name="defaultImage" type="text" value="'.$this->getValue('defaultImage').'">';
        $html .= '<div class="form-text">'.$L->get('Set a default image for pages without pictures').'</div>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="fbAppId">'.$L->get('Facebook App ID').'</label>';
        $html .= '<input class="form-control" id="fbAppId" name="fbAppId" type="text" value="'.$this->getValue('fbAppId').'">';
        $html .= '<div class="form-text">'.$L->get('Set your Facebook app id').'</div>';
        $html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		global $url;
		global $site;
		global $WHERE_AM_I;
		global $page;
		global $content;

		$og = array(
			'locale'	=>$site->locale(),
			'type'		=>'website',
			'title'		=>$site->title(),
			'description'	=>$site->description(),
			'url'		=>$site->url(),
			'image'		=>'',
			'siteName'	=>$site->title()
		);

		switch ($WHERE_AM_I) {
			// The user filter by page
			case 'page':
				$og['type']		= 'article';
				$og['title']		= $page->title();
				$og['description']	= $page->description();
				$og['url']		= $page->permalink($absolute=true);
				$og['image'] 		= $page->coverImage($absolute=true);

				$pageContent = $page->content();
				break;

			// The user is in the homepage
			default:
				$pageContent = '';
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$og['image'] = $this->getValue('defaultImage');
				}
				elseif (isset($content[0]) ) {
					$og['image'] 	= $content[0]->coverImage($absolute=true);
					$pageContent 	= $content[0]->content();
				}
				break;
		}

		$html  = PHP_EOL.'<!-- Open Graph -->'.PHP_EOL;
		$html .= '<meta property="og:locale" content="'.$og['locale'].'">'.PHP_EOL;
		$html .= '<meta property="og:type" content="'.$og['type'].'">'.PHP_EOL;
		$html .= '<meta property="og:title" content="'.$og['title'].'">'.PHP_EOL;
		$html .= '<meta property="og:description" content="'.$og['description'].'">'.PHP_EOL;
		$html .= '<meta property="og:url" content="'.$og['url'].'">'.PHP_EOL;
		$html .= '<meta property="og:site_name" content="'.$og['siteName'].'">'.PHP_EOL;

		// If the page doesn't have a coverImage try to get an image from the HTML content
		if (empty($og['image'])) {
			// Get the image from the content
			$src = DOM::getFirstImage($pageContent);
			if ($src!==false) {
				$og['image'] = $src;
			} else {
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$og['image'] = $this->getValue('defaultImage');
				}
			}
		}

		$html .= '<meta property="og:image" content="'.$og['image'].'">'.PHP_EOL;
		if (Text::isNotEmpty($this->getValue('fbAppId'))) {
			$html .= '<meta property="fb:app_id" content="'. $this->getValue('fbAppId').'">'.PHP_EOL;
		}
		return $html;
	}

}