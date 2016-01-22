<?php

class pluginOpenGraph extends Plugin {

	// Returns the first image that is in the content
	private function getImage($content)
	{
		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$content);
		$finder = new DomXPath($dom);

		/* DEPRECATED
		$images = $finder->query("//img[contains(@class, 'bludit-img-opengraph')]");

		if($images->length==0) {
			$images = $finder->query("//img");
		}
		*/

		$images = $finder->query("//img");

		if($images->length>0)
		{
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
		global $Url, $Site;
		global $Post, $Page, $posts;

		$og = array(
			'locale'	=>$Site->locale(),
			'type'		=>'website',
			'title'		=>$Site->title(),
			'description'	=>$Site->description(),
			'url'		=>$Site->url(),
			'image'		=>'',
			'siteName'	=>$Site->title()
		);

		switch($Url->whereAmI())
		{
			case 'post':
				$og['type']		= 'article';
				$og['title']		= $Post->title().' | '.$og['title'];
				$og['description']	= $Post->description();
				$og['url']		= $Post->permalink(true);
				$og['image'] 		= $Post->coverImage(false);

				$content = $Post->content();
				break;

			case 'page':
				$og['type']		= 'article';
				$og['title']		= $Page->title().' | '.$og['title'];
				$og['description']	= $Page->description();
				$og['url']		= $Page->permalink(true);
				$og['image'] 		= $Page->coverImage(false);

				$content = $Page->content();
				break;

			default:
				if(isset($posts[0])) {
					$og['image'] = $posts[0]->coverImage(false);
					$content = $posts[0]->content();
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

		$domain = trim($Site->domain(),'/');
		$urlImage = $domain.HTML_PATH_UPLOADS;

		// If the post o page doesn't have a coverImage try to get it from the content
		if($og['image']===false)
		{
			// Get the image from the content
			$src = $this->getImage( $content );
			if($src!==false) {
				$html .= '<meta property="og:image" content="'.$urlImage.$og['image'].'">'.PHP_EOL;
			}
		}
		else
		{
			$html .= '<meta property="og:image" content="'.$urlImage.$og['image'].'">'.PHP_EOL;
		}

		return $html;
	}
}