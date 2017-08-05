<?php

class pluginsimpleMDE extends Plugin {

	private $loadOnController = array(
		'new-page',
		'edit-page'
	);

	public function init()
	{
		$this->dbFields = array(
			'tabSize'=>'2',
			'toolbar'=>'"bold", "italic", "heading", "|", "quote", "unordered-list", "|", "link", "image", "code", "horizontal-rule", "|", "preview", "side-by-side", "fullscreen", "guide"',
			'autosave'=>true,
			'spellChecker'=>true
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

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Autosave').'</label>';
		$html .= '<select name="autosave">';
		$html .= '<option value="true" '.($this->getValue('autosave')===true?'selected':'').'>Enabled</option>';
		$html .= '<option value="false" '.($this->getValue('autosave')===false?'selected':'').'>Disabled</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Spell Checker').'</label>';
		$html .= '<select name="spellChecker">';
		$html .= '<option value="true" '.($this->getValue('spellChecker')===true?'selected':'').'>Enabled</option>';
		$html .= '<option value="false" '.($this->getValue('spellChecker')===false?'selected':'').'>Disabled</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		if (in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			$html = '';

			// Path plugin.
			$pluginPath = $this->htmlPath();

			// SimpleMDE css
			$html .= '<link rel="stylesheet" href="'.$pluginPath.'css/simplemde.min.css">';

			// Font-awesome is a dependency of SimpleMDE
			$html .= '<link rel="stylesheet" href="'.HTML_PATH_CORE_CSS.'font-awesome/font-awesome.min.css">';

			// SimpleMDE js
			$html .= '<script src="'.$pluginPath.'js/simplemde.min.js"></script>';

			// Hack for Bludit
			$html .= '<style>
					.editor-toolbar { background: #f1f1f1; border-radius: 0 !important; }
					.editor-toolbar::before { margin-bottom: 2px !important }
					.editor-toolbar::after { margin-top: 2px !important }
					.CodeMirror, .CodeMirror-scroll { min-height: 400px !important; border-radius: 0 !important; }
				</style>';

			return $html;
		}

		return false;
	}

	public function adminBodyEnd()
	{
		if (in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			// Autosave
			global $Page;
			global $Language;
			$autosaveID = $GLOBALS['ADMIN_CONTROLLER'];
			$autosaveEnable = $this->getDbField('autosave')?'true':'false';
			if (!empty($Page)) {
				$autosaveID = $Page->key();
			}

			// Spell Checker
			$spellCheckerEnable = $this->getDbField('spellChecker')?'true':'false';

			$pluginPath = $this->htmlPath();

			$html  = '<script>'.PHP_EOL;

			$html .= 'var simplemde = null;'.PHP_EOL;

			$html .= 'function addContentSimpleMDE(content) {
					var text = simplemde.value();
					simplemde.value(text + content + "\n");
				}'.PHP_EOL;

			// This function is necesary on each Editor, it is used by Bludit Images v8.
			$html .= 'function editorAddImage(filename) {
					addContentSimpleMDE("!['.$Language->get('Image description').']("+filename+")");
				}'.PHP_EOL;

			$html .= '$(document).ready(function() { '.PHP_EOL;
			$html .= 'simplemde = new SimpleMDE({
					element: document.getElementById("jscontent"),
					status: false,
					toolbarTips: true,
					toolbarGuideIcon: true,
					autofocus: false,
					placeholder: "Content here. Supports Markdown and HTML code.",
					lineWrapping: true,
					autoDownloadFontAwesome: false,
					indentWithTabs: true,
					tabSize: '.$this->getDbField('tabSize').',
					spellChecker: '.$spellCheckerEnable.',
					autosave: {
						enabled: '.$autosaveEnable.',
						uniqueId: "'.$autosaveID.'",
						delay: 1000,
					},
					toolbar: ['.Sanitize::htmlDecode($this->getDbField('toolbar')).']
			});';

			$html .= '}); </script>';
			return $html;
		}

		return false;
	}
}