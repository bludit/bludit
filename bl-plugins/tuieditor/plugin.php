<?php

class pluginTuieditor extends Plugin
{

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function init()
	{
		$this->dbFields = array(
			'previewStyle' => 'vertical'
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('preview-style') . '</label>';
		$html .= '<select name="previewStyle">';
		$html .= '<option value="vertical" ' . ($this->getValue('previewStyle') === 'vertical' ? 'selected' : '') . '>' . $L->get('vertical') . '</option>';
		$html .= '<option value="tab" ' . ($this->getValue('previewStyle') === 'tab' ? 'selected' : '') . '>' . $L->get('tab') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$html  = $this->includeCSS('codemirror.min.css');
		$html .= $this->includeCSS('toastui-editor.min.css');
		return $html;
	}

	public function adminBodyEnd()
	{
		global $L;

		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$previewStyle = $this->getValue('previewStyle');
		$imageAltText = $L->g('Image description');

		$html  = $this->includeJS('codemirror.min.js');
		$html .= $this->includeJS('toastui-editor.min.js');
		$html .= <<<EOF
<script>

	var tuiEditor = null;

	(function() {
		// TOAST UI Editor mounts on a div, so the original textarea is hidden and kept only
		// to carry the initial raw content and to stay a valid, harmless form field.
		var jseditorTextarea = document.getElementById('jseditor');
		var tuiContainer = document.createElement('div');
		tuiContainer.id = 'jseditor_tui';
		tuiContainer.className = jseditorTextarea.className;
		tuiContainer.style.height = '100%';
		jseditorTextarea.style.display = 'none';
		jseditorTextarea.parentNode.insertBefore(tuiContainer, jseditorTextarea);

		tuiEditor = new toastui.Editor({
			el: tuiContainer,
			height: '100%',
			initialEditType: 'markdown',
			previewStyle: '$previewStyle',
			usageStatistics: false,
			initialValue: jseditorTextarea.value
		});
	})();

	// Insert an image in the editor at the cursor position
	// Function required for Bludit
	function editorInsertMedia(filename) {
		tuiEditor.insertText("![$imageAltText](" + filename + ")");
	}

	// Insert a linked image in the editor at the cursor position
	// Function required for Bludit
	function editorInsertLinkedMedia(filename, link) {
		tuiEditor.insertText("<a href=\""+link+"\"><img src=\""+filename+"\"></a>");
	}

	// Returns the content of the editor
	// Function required for Bludit
	function editorGetContent() {
		return tuiEditor.getMarkdown();
	}

</script>
EOF;
		return $html;
	}
}
