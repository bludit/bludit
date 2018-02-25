<?php

class pluginLightbox extends Plugin {

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= 'There are not settings for this plugin at the moment.';
		$html .= '</div>';

		return $html;
	}

	public function siteHead(){

		$html  = '<link href="'.HTML_PATH_PLUGINS.'lightbox/css/lightbox.css" rel="stylesheet">';
		return $html;

    }

 	public function siteBodyEnd()
 	{

 		$html  = PHP_EOL.'<script src="'.HTML_PATH_PLUGINS.'lightbox/js/lightbox.js"></script>'.PHP_EOL;
 		return $html;
	}

}
