<?php

class pluginOpenGraph extends Plugin {

	private function getImage($content)
	{
		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$content);
		$finder = new DomXPath($dom);

		$images = $finder->query("//img[contains(@class, 'bludit-img-opengraph')]");

		if($images->length==0) {
			$images = $finder->query("//img");
		}

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

				$content = $Post->content();
				break;

			case 'page':
				$og['type']		= 'article';
				$og['title']		= $Page->title().' | '.$og['title'];
				$og['description']	= $Page->description();
				$og['url']		= $Page->permalink(true);

				$content = $Page->content();
				break;

			default:
				$content = isset($posts[0])?$posts[0]->content():'';
				break;
		}

		$html  = PHP_EOL.'<!-- Open Graph -->'.PHP_EOL;
		$html .= '<meta property="og:locale" content="'.$og['locale'].'">'.PHP_EOL;
		$html .= '<meta property="og:type" content="'.$og['type'].'">'.PHP_EOL;
		$html .= '<meta property="og:title" content="'.$og['title'].'">'.PHP_EOL;
		$html .= '<meta property="og:description" content="'.$og['description'].'">'.PHP_EOL;
		$html .= '<meta property="og:url" content="'.$og['url'].'">'.PHP_EOL;
		$html .= '<meta property="og:siteName" content="'.$og['siteName'].'">'.PHP_EOL;

		// Get the image from the content
		$src = $this->getImage( $content );
		if($src!==false) {
			$og['image'] = $Site->domain().$src;
			$html .= '<meta property="og:image" content="'.$og['image'].'">'.PHP_EOL;
		}

		return $html;
	}
}