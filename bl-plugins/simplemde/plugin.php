<?php

class pluginsimpleMDE extends Plugin {

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function init()
	{
		$this->dbFields = array(
			'tabSize'=>'2',
			'toolbar'=>'"bold", "italic", "heading", "|", "quote", "unordered-list", "|", "link", "image", "code", "horizontal-rule", "|", "preview", "side-by-side", "fullscreen"',
			'autosave'=>false,
			'spellChecker'=>true
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('toolbar').'</label>';
		$html .= '<input name="toolbar" id="jstoolbar" type="text" value="'.$this->getDbField('toolbar').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('tab-size').'</label>';
		$html .= '<input name="tabSize" id="jstabSize" type="text" value="'.$this->getDbField('tabSize').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('spell-checker').'</label>';
		$html .= '<select name="spellChecker">';
		$html .= '<option value="true" '.($this->getValue('spellChecker')===true?'selected':'').'>'.$Language->get('enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('spellChecker')===false?'selected':'').'>'.$Language->get('disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$html = '';

		// Path plugin.
		$pluginPath = $this->htmlPath();

		// SimpleMDE css
		$html .= '<link rel="stylesheet" href="'.$pluginPath.'css/simplemde.min.css">';

		// Hack for Bludit
		$html .= '<style>
				.editor-toolbar { background: #f1f1f1; border-radius: 0 !important; }
				.editor-toolbar::before { margin-bottom: 2px !important }
				.editor-toolbar::after { margin-top: 2px !important }
				.CodeMirror, .CodeMirror-scroll { min-height: 450px !important; border-radius: 0 !important; }
			</style>';

		return $html;
	}

	public function adminBodyEnd()
	{
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

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

		// SimpleMDE js
		$html  = '<script src="'.$pluginPath.'js/simplemde.min.js"></script>';

		$html .= '<script>'.PHP_EOL;

		$html .= 'var simplemde = null;'.PHP_EOL;

		$html .= 'function addContentSimpleMDE(content) {
				var text = simplemde.value();
				simplemde.value(text + content + "\n");
				simplemde.codemirror.refresh();
			}'.PHP_EOL;

		// Function required for Autosave function
		// Returns the content of the editor
		$html .= 'function editorGetContent(content) {
			return simplemde.value();
		}'.PHP_EOL;

		// Function required for Media Manager
		// Insert an image on the editor in the cursor position
		$html .= 'function editorInsertMedia(filename) {
				addContentSimpleMDE("!['.$Language->get('Image description').']("+filename+")");
			}'.PHP_EOL;

		$html .= '$(document).ready(function() { '.PHP_EOL;

		$html .= '
		simplemde = new SimpleMDE({
				element: document.getElementById("jseditor"),
				status: false,
				toolbarTips: true,
				toolbarGuideIcon: true,
				autofocus: false,
				placeholder: "'.$Language->get('content-here-supports-markdown-and-html-code').'",
				lineWrapping: true,
				autoDownloadFontAwesome: false,
				indentWithTabs: true,
				tabSize: '.$this->getDbField('tabSize').',
				spellChecker: '.$spellCheckerEnable.',
				toolbar: ['.Sanitize::htmlDecode($this->getDbField('toolbar')).',
					"|",
					{
					name: "pageBreak",
					action: function addPageBreak(editor){
						var cm = editor.codemirror;
						output = "\n'.PAGE_BREAK.'\n";
						cm.replaceSelection(output);
						},
					className: "oi oi-crop",
					title: "'.$Language->get('Pagebreak').'",
					}]
		});';

		$html .= '}); </script>';
		return $html;
	}
}
