<?php

class pluginOpenGraph extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'defaultImage'=>''
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Default image').'</label>';
		$html .= '<input id="jsdefaultImage" name="defaultImage" type="text" value="'.$this->getValue('defaultImage').'" placeholder="https://">';
		$html .= '</div>';

		/*
		$html  = '<div>';
		$html .= '<label>'.$Language->get('Default image').'</label>';
		$html .= '<select name="defaultImage">';

		$images = Filesystem::listFiles(PATH_UPLOADS);
		foreach ($images as $image) {
			$base = basename($image);
			$html .= '<option value="'.$base.'" '.(($this->getValue('defaultImage')==$base)?'selected':'').'>'.$base.'</option>';
		}

		$html .= '</select>';
		$html .= '</div>';
		*/

		return $html;
	}

	public function siteHead()
	{
		global $Url;
		global $Site;
		global $WHERE_AM_I;
		global $pages;
		global $page;

		$og = array(
			'locale'	=>$Site->locale(),
			'type'		=>'website',
			'title'		=>$Site->title(),
			'description'	=>$Site->description(),
			'url'		=>$Site->url(),
			'image'		=>'',
			'siteName'	=>$Site->title()
		);

		switch ($WHERE_AM_I) {
			// The user filter by page
			case 'page':
				$og['type']		= 'article';
				$og['title']		= $page->title();
				$og['description']	= $page->description();
				$og['url']		= $page->permalink($absolute=true);
				$og['image'] 		= $page->coverImage($absolute=true);

				$content = $page->content();
				break;

			// The user is in the homepage
			default:
				$content = '';
				// The image it's from the first page
				if (isset($pages[0]) ) {
					$og['image'] 	= $pages[0]->coverImage($absolute=true);
					$content 	= $pages[0]->content();
				}
				break;
		}

		$html  = PHP_EOL.'<!-- Open Graph -->'.PHP_EOL;
		$html .= '<meta property="og:locale" content="'.$og['locale'].'">'.PHP_EOL;
		$html .= '<meta property="og:type" content="'.$og['type'].'">'.PHP_EOL;
		$html .= '<meta property="og:title" content="'.$og['title'].'">'.PHP_EOL;
		$html .= '<meta property="og:description" content="'.$og['description'].'">'.PHP_EOL;
		$html .= '<meta property="og:url" content="'.$og['url'].'">'.PHP_EOL;
		$html .= '<meta property="og:siteName" content="'.$og['siteName'].'">'.PHP_EOL;

		// If the page doesn't have a coverImage try to get an image from the HTML content
		if (empty($og['image'])) {
			// Get the image from the content
			$src = DOM::getFirstImage($content);
			if ($src!==false) {
				$og['image'] = $src;
			} else {
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$og['image'] = $this->getValue('defaultImage');
				}
			}
		}

		$html .= '<meta property="og:image" content="'.$og['image'].'">'.PHP_EOL;
		return $html;
	}

}