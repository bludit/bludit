<?php

class pluginTrumbowyg extends Plugin {

	private $loadWhenController = array(
		'new-post',
		'new-page',
		'edit-post',
		'edit-page'
	);

	public function onAdminHead()
	{
		global $Language;
		global $Site;
		global $layout;

		$html = '';

		if(in_array($layout['controller'], $this->loadWhenController))
		{
			$language = $Site->shortLanguage();

			$html  = '<script src="'.HTML_PATH_PLUGINS.'trumbowyg/trumbowyg/trumbowyg.min.js"></script>';
			$html .= '<script src="'.HTML_PATH_PLUGINS.'trumbowyg/trumbowyg/langs/'.$language.'.min.js"></script>';
			$html .= '<link rel="stylesheet" href="'.HTML_PATH_PLUGINS.'trumbowyg/trumbowyg/ui/trumbowyg.min.css">';

			// CSS fix for Bludit
			$html .= '<style>.trumbowyg-box {width: 80% !important; margin: 0 !important;}</style>';
		}

		return $html;
	}

	public function onAdminBodyEnd()
	{
		global $Language;
		global $Site;
		global $layout;

		$html = '';

		if(in_array($layout['controller'], $this->loadWhenController))
		{
			$language = $Site->shortLanguage();

			$html  = '<script>$(document).ready(function() { ';
			$html .= '$("#jscontent").trumbowyg({lang: "'.$language.'", resetCss: true, removeformatPasted: true, autogrow: true});';
			$html .= '}); </script>';
		}

		return $html;	
	}
}
