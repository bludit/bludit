<?php

class pluginTwitterCards extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'defaultImage'   => '',
			'twitterSite'    => '',
			'twitterCreator' => ''
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
		$html .= '<p class="text-muted small">' . $L->get('minimum-image-dimensions') . ' ' . $L->get('image-must-be-less') . ' ' . $L->get('formats-are-supported') . '</p>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Twitter site profile').'</label>';
		$html .= '<input id="jstwitterSite" name="twitterSite" type="text" value="'.$this->getValue('twitterSite').'" placeholder="@yourSiteProfile">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Twitter author profile').'</label>';
		$html .= '<input id="jstwitterCreator" name="twitterCreator" type="text" value="'.$this->getValue('twitterCreator').'" placeholder="@authorProfile">';
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
			'card'		     => 'summary_large_image',
			'twitterSite'    => $this->getValue('twitterSite'),
			'twitterCreator' => $this->getValue('twitterCreator'),
			'title'		     => $site->title(),
			'description'    => $site->description(),
			'image'		     => ''
		);

		switch( $WHERE_AM_I ) {
			// The user filter by page
			case 'page':
				$data['title']		  = $page->title();
				$data['description']  = $page->description();
				$data['image'] 		  = $page->coverImage( $absolute = true );

				$pageContent = $page->content();
				break;

			// The user is in the homepage
			default:
				$pageContent = '';
				// The image it's from the first page
				if(isset($content[0]) ) {
					$data['image'] 	= $content[0]->coverImage( $absolute = true );
					$pageContent 	= $content[0]->content();
				}
				break;
		}

		$html  = PHP_EOL.'<!-- Twitter Cards -->'.PHP_EOL;
		$html .= '<meta name="twitter:card" content="' . $data['card'] . '">'.PHP_EOL;
		$html .= '<meta name="twitter:site" content="' . $data['site'] . '">'.PHP_EOL;
		$html .= '<meta name="twitter:title" content="' . $data['title'] . '">'.PHP_EOL;
		$html .= '<meta name="twitter:creator" content="' . $data['twitterCreator'] . '">'.PHP_EOL;
		// If the page doesn't have a description try to get excerpt from content
		if( empty( $data['description'] ) )
		{
			$data['description'] = $this->content_excerpt( $pageContent , 150, '...');
		}
		$html .= '<meta name="twitter:description" content="' . $data['description'] . '">'.PHP_EOL;

		// If the page doesn't have a coverImage try to get an image from the HTML content
		if( empty( $data['image'] ) ) {
			// Get the image from the content
			$src = DOM::getFirstImage( $pageContent );
			if ( $src !== false ) {
				$data['image'] = $src;
			} else {
				if ( Text::isNotEmpty( $this->getValue('defaultImage') ) ) {
					$data['image'] = $this->getValue('defaultImage');
				}
			}
		}

		$html .= '<meta name="twitter:image" content="'.$data['image'].'">'.PHP_EOL;
		$html .= '<!-- /Twitter Cards -->'.PHP_EOL.PHP_EOL;

		unset( $pageContent );
		unset( $data );

		return $html;
	}

	/** Return excerpt from full content of post
	 *
	 * @param string $str Post content
	 * @param int $n Number of characters
	 * @param string $endChar End char append to after excerpt (default "...")
	 * @return string
	 */
	private function content_excerpt( string $str, int $n = 500, string $endChar = '&#8230;' ): string
	{
		$str = strip_tags( $str );
		if ( mb_strlen( $str ) < $n )
		{
	        return $str;
	    }
	    $str = preg_replace('/ {2,}/', ' ', str_replace(["\r", "\n", "\t", "\x0B", "\x0C"], ' ', $str));
	    if ( mb_strlen( $str ) <= $n )
	    {
	        return $str;
	    }
	    $out = '';
	    foreach ( explode( ' ', trim( $str ) ) as $val )
	    {
	        $out .= $val . ' ';
	        if ( mb_strlen( $out ) >= $n )
	        {
	            $out = trim( $out );
	            break;
	        }
	    }
	    return ( mb_strlen( $out ) === mb_strlen( $str ) ) ? $out : $out . $endChar;
	}
}
