<?php

class pluginEasyMDE extends Plugin {

	private $loadOnViews = array(
		'editor' // Load this plugin only in the Editor view
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

		$html  = '<div class="mb-3">';
		$html .= '<label class="form-label" for="toolbar">'.$L->get('toolbar').'</label>';
		$html .= '<input class="form-control" name="toolbar" id="toolbar" type="text" value="'.$this->getValue('toolbar').'">';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="tabSize">'.$L->get('tab-size').'</label>';
		$html .= '<input class="form-control" name="tabSize" id="tabSize" type="text" value="'.$this->getValue('tabSize').'">';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="spellChecker">'.$L->get('spell-checker').'</label>';
		$html .= '<select class="form-control" name="spellChecker">';
		$html .= '<option value="true" '.($this->getValue('spellChecker')===true?'selected':'').'>'.$L->get('enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('spellChecker')===false?'selected':'').'>'.$L->get('disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_VIEW'], $this->loadOnViews)) {
			return false;
		}

		$html  = $this->includeCSS('easymde.min.css');
		$html .= $this->includeCSS('bludit.css');
		$html .= $this->includeJS('easymde.min.js');
		return $html;
	}

	public function adminBodyEnd()
	{
		global $L;

		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_VIEW'], $this->loadOnViews)) {
			return false;
		}

		$langImage = $L->g('Image description');
		$spellCheckerEnable = $this->getValue('spellChecker')?'true':'false';
		$tabSize = $this->getValue('tabSize');
		$toolbar = Sanitize::htmlDecode($this->getValue('toolbar'));
		$pageBreak = PAGE_BREAK;

return <<<EOF
<script>
	// Function required for Bludit
	// Returns the content of the editor
	function editorGetContent() {
		return easymde.value();
	}

	// Function required for Bludit
	// Insert HTML content at the cursor position
	function editorInsertContent(html, type='') {
		var text = easymde.value();
		if (type == 'image') {
			easymde.value(text + "![$langImage]("+filename+")" + "\\n");
		} else {
			easymde.value(html + "\\n");
		}
		easymde.codemirror.refresh();
	}

	var easymde = new EasyMDE({
		element: document.getElementById("editor"),
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
			className: "bi-file-earmark-break",
			title: "Page break",
			}]
	});

</script>
EOF;
	}
}