<?php

class pluginTwitterCards extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'defaultImage'=>''
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Default image').'</label>';
		$html .= '<input id="jsdefaultImage" name="defaultImage" type="text" value="'.$this->getValue('defaultImage').'" placeholder="https://">';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		global $url;
		global $site;
		global $WHERE_AM_I;
		global $content;
		global $page;

		$data = array(
			'card'		=>'summary',
			'site'		=>'',
			'title'		=>$site->title(),
			'description'	=>$site->description(),
			'image'		=>''
		);

		switch($WHERE_AM_I) {
			// The user filter by page
			case 'page':
				$data['title']		= $page->title();
				$data['description']	= $page->description();
				$data['image'] 		= $page->coverImage($absolute=true);

				$pageContent = $page->content();
				break;

			// The user is in the homepage
			default:
				$pageContent = '';
				// The image it's from the first page
				if(isset($content[0]) ) {
					$data['image'] 	= $content[0]->coverImage($absolute=true);
					$pageContent 	= $content[0]->content();
				}
				break;
		}

		$html  = PHP_EOL.'<!-- Twitter Cards -->'.PHP_EOL;
		$html .= '<meta property="twitter:card" content="'.$data['card'].'">'.PHP_EOL;
		$html .= '<meta property="twitter:site" content="'.$data['site'].'">'.PHP_EOL;
		$html .= '<meta property="twitter:title" content="'.$data['title'].'">'.PHP_EOL;
		$html .= '<meta property="twitter:description" content="'.$data['description'].'">'.PHP_EOL;

		// If the page doesn't have a coverImage try to get an image from the HTML content
		if( empty($data['image']) ) {
			// Get the image from the content
			$src = DOM::getFirstImage($pageContent);
			if ($src!==false) {
				$data['image'] = $src;
			} else {
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$data['image'] = $this->getValue('defaultImage');
				}
			}
		}

		$html .= '<meta property="twitter:image" content="'.$data['image'].'">'.PHP_EOL;
		return $html;
	}
}