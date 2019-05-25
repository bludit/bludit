<?php

class plugineasyMDE extends Plugin {

	// The plugin is going to be loaded in this views
	private $loadOnViews = array(
		'new-content',
		'edit-content'
	);

	public function init()
	{
		$this->dbFields = array(
			'tabSize'=>'2',
			'toolbar'=>'"bold", "italic", "heading", "|", "quote", "unordered-list", "|", "link", "image", "code", "horizontal-rule", "|", "preview", "side-by-side", "fullscreen"',
			'spellChecker'=>true
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div>';
		$html .= '<label>'.$L->get('toolbar').'</label>';
		$html .= '<input name="toolbar" id="jstoolbar" type="text" value="'.$this->getValue('toolbar').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('tab-size').'</label>';
		$html .= '<input name="tabSize" id="jstabSize" type="text" value="'.$this->getValue('tabSize').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('spell-checker').'</label>';
		$html .= '<select name="spellChecker">';
		$html .= '<option value="true" '.($this->getValue('spellChecker')===true?'selected':'').'>'.$L->get('enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('spellChecker')===false?'selected':'').'>'.$L->get('disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		if (!in_array($GLOBALS['ADMIN_VIEW'], $this->loadOnViews)) {
			return false;
		}

		// Include plugin's CSS files
		$html  = $this->includeCSS('easymde.min.css');
		$html .= $this->includeCSS('bludit.css');
		return $html;
	}

	public function adminBodyEnd()
	{
		if (!in_array($GLOBALS['ADMIN_VIEW'], $this->loadOnViews)) {
			return false;
		}

		// Language
		global $L;
		$langImage = $L->g('Image description');

		$spellCheckerEnable = $this->getValue('spellChecker')?'true':'false';
		$tabSize = $this->getValue('tabSize');
		$toolbar = Sanitize::htmlDecode($this->getValue('toolbar'));
		$pageBreak = PAGE_BREAK;

		// Javascript path and file
		$jsEasyMDE = $this->domainPath().'js/easymde.min.js?version='.BLUDIT_VERSION;

return <<<EOF
<script charset="utf-8" src="$jsEasyMDE"></script>
<script>
	var easymde = null;

	// Insert an image in the editor at the cursor position
	// Function required for Bludit
	function editorInsertMedia(filename) {
		var text = easymde.value();
		easymde.value(text + "![$langImage]("+filename+")" + "\\n");
		easymde.codemirror.refresh();
	}

	// Returns the content of the editor
	// Function required for Bludit
	function editorGetContent() {
		return easymde.value();
	}

	easymde = new EasyMDE({
		element: document.getElementById("jseditor"),
		status: false,
		toolbarTips: true,
		toolbarGuideIcon: true,
		autofocus: false,
		placeholder: "",
		lineWrapping: true,
		autoDownloadFontAwesome: false,
		indentWithTabs: true,
		tabSize: $tabSize,
		spellChecker: $spellCheckerEnable,
		toolbar: [$toolbar,
			"|",
			{
			name: "pageBreak",
			action: function addPageBreak(editor){
				var cm = editor.codemirror;
				output = "$pageBreak";
				cm.replaceSelection(output);
				},
			className: "fa fa-crop",
			title: "Page break",
			}]
	});

</script>
EOF;
	}
}