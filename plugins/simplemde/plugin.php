<?php

class pluginsimpleMDE extends Plugin {

	private $loadWhenController = array(
		'new-post',
		'new-page',
		'edit-post',
		'edit-page'
	);

	public function adminHead()
	{
		global $Language;
		global $Site;
		global $layout;

		$html = '';

		// Load CSS and JS only on Controllers in array.
		if(in_array($layout['controller'], $this->loadWhenController))
		{
			$language = $Site->shortLanguage();
			$pluginPath = $this->htmlPath();

			$html  = '<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">';
			$html .= '<link rel="stylesheet" href="//cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">';
			$html .= '<script src="//cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>';

		}

		return $html;
	}

	public function adminBodyEnd()
	{
		global $Language;
		global $Site;
		global $layout;

		$html = '';

		// Load CSS and JS only on Controllers in array.
		if(in_array($layout['controller'], $this->loadWhenController))
		{
			$language = $Site->shortLanguage();
			$pluginPath = $this->htmlPath();

			$html  = '<script>$(document).ready(function() { ';
			$html .=
				'var simplemde = new SimpleMDE({
					element: document.getElementById("jscontent"),
					status: true,
					toolbarTips: true,
					toolbarGuideIcon: true,
					autofocus: true,
					lineWrapping: false,
					indentWithTabs: true,
					tabSize: 4,
					spellChecker: true
				});';
			$html .= '}); </script>';
		}

		return $html;
	}
}
