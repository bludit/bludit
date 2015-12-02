<?php

class pluginsimpleMDE extends Plugin {

	private $loadWhenController = array(
		'new-post',
		'new-page',
		'edit-post',
		'edit-page'
	);

	public function init()
	{
		$this->dbFields = array(
			'tabSize'=>'2',
			'toolbar'=>'"bold", "italic", "heading", "|", "quote", "unordered-list", "|", "link", "image", "code", "horizontal-rule", "|", "preview", "side-by-side", "fullscreen", "guide"'
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Toolbar').'</label>';
		$html .= '<input name="toolbar" id="jstoolbar" type="text" value="'.$this->getDbField('toolbar').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Tab size').'</label>';
		$html .= '<input name="tabSize" id="jstabSize" type="text" value="'.$this->getDbField('tabSize').'">';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		global $layout;

		$html = '';

		// Load CSS and JS only on Controllers in array.
		if(in_array($layout['controller'], $this->loadWhenController))
		{
			// Path plugin.
			$pluginPath = $this->htmlPath();

			// SimpleMDE css
			$html .= '<link rel="stylesheet" href="'.$pluginPath.'css/simplemde.min.css">';

			// Font-awesome is a dependency of SimpleMDE
			$html .= '<link rel="stylesheet" href="'.$pluginPath.'css/font-awesome.min.css">';

			// SimpleMDE js
			$html .= '<script src="'.$pluginPath.'js/simplemde.min.js"></script>';

			// Hack for Bludit
			$html .= '<style>
					.editor-toolbar { background: #f1f1f1; }
					.editor-toolbar::before { margin-bottom: 2px !important }
					.editor-toolbar::after { margin-top: 2px !important }
					.CodeMirror, .CodeMirror-scroll { min-height: 400px !important; }
				</style>';

		}

		return $html;
	}

	public function adminBodyEnd()
	{
		global $layout;

		$html = '';

		// Load CSS and JS only on Controllers in array.
		if(in_array($layout['controller'], $this->loadWhenController))
		{
			$pluginPath = $this->htmlPath();

			$html  = '<script>'.PHP_EOL;
			$html .= '$(document).ready(function() { '.PHP_EOL;
			$html .= 'var simplemde = new SimpleMDE({
					element: document.getElementById("jscontent"),
					status: false,
					toolbarTips: true,
					toolbarGuideIcon: true,
					autofocus: false,
					lineWrapping: true,
					autoDownloadFontAwesome: false,
					indentWithTabs: true,
					tabSize: '.$this->getDbField('tabSize').',
					spellChecker: false,
					toolbar: ['.Sanitize::htmlDecode($this->getDbField('toolbar')).']
			});';

			$html .= '$("#jsaddImage").on("click", function() {
					var filename = $("#jsimageList option:selected" ).text();
					if(!filename.trim()) {
						return false;
					}
					var text = simplemde.value();
					simplemde.value(text + "![alt text]("+filename+")" + "\n");
			});';

			$html .= '}); </script>';
		}

		return $html;
	}
}
