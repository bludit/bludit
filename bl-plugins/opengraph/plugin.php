<?php

class pluginOpenGraph extends Plugin {

	// Returns the first image from the HTML content
	private function getImage($content)
	{
		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$content);
		$finder = new DomXPath($dom);

		$images = $finder->query("//img");

		if($images->length>0) {
			// First image from the list
			$image = $images->item(0);
			// Get value from attribute src
			$imgSrc = $image->getAttribute('src');
			// Returns the image src
			return $imgSrc;
		}

		return false;
	}

	public function siteHead()
	{
		global $Url;
		global $Site;
		global $WHERE_AM_I;
		global $pages;

		$og = array(
			'locale'	=>$Site->locale(),
			'type'		=>'website',
			'title'		=>$Site->title(),
			'description'	=>$Site->description(),
			'url'		=>$Site->url(),
			'image'		=>'',
			'siteName'	=>$Site->title()
		);

		switch($WHERE_AM_I)
		{
			// The user filter by page
			case 'page':
				$og['type']		= 'article';
				$og['title']		= $Page->title();
				$og['description']	= $Page->description();
				$og['url']		= $Page->permalink($absolute=true);
				$og['image'] 		= $Page->coverImage($absolute=true);

				$content = $Page->content();
				break;

			// The user is in the homepage
			default:
				// The image it's from the first page
				if(isset($pages[0]) ) {
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
		if( empty($og['image']) ) {
			// Get the image from the content
			$src = $this->getImage($content);
			if($src!==false) {
				$og['image'] = DOMAIN.$src;
			}
		}

		$html .= '<meta property="og:image" content="'.$og['image'].'">'.PHP_EOL;

		return $html;
	}
}