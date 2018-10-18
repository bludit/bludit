<?php

class pluginQuilljs extends Plugin {

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function init()
	{
		$this->dbFields = array(
			'toolbar1'=>'formatselect bold italic bullist numlist | blockquote alignleft aligncenter alignright | link unlink pagebreak image removeformat code',
			'toolbar2'=>'',
			'plugins'=>'code autolink image link pagebreak advlist lists textcolor colorpicker textpattern'
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div>';
		$html .= '<label>'.$L->get('Toolbar top').'</label>';
		$html .= '<input name="toolbar1" id="jstoolbar1" type="text" value="'.$this->getValue('toolbar1').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Toolbar bottom').'</label>';
		$html .= '<input name="toolbar2" id="jstoolbar2" type="text" value="'.$this->getValue('toolbar2').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Plugins').'</label>';
		$html .= '<input name="plugins" id="jsplugins" type="text" value="'.$this->getValue('plugins').'">';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$html  = '<link rel="stylesheet" href="'.$this->htmlPath().'css/medium-editor.min.css">';
		$html .= '<link rel="stylesheet" href="'.$this->htmlPath().'css/themes/default.css">';

		return $html;
	}

	public function adminBodyEnd()
	{
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$mediumJavascript = $this->htmlPath().'js/medium-editor.min.js';

$script = <<<EOF

<script src="$mediumJavascript"></script>

<script>

// Function required for Media Manager
// Insert an image on the editor in the cursor position
function editorInsertMedia(filename) {
	tinymce.activeEditor.insertContent("<img src=\""+filename+"\" alt=\"\">");
}

// Function required for Autosave function
// Returns the content of the editor
function editorGetContent() {
	return tinymce.get('jseditor').getContent();
}

function resizeEditor() {
	var editor = tinymce.activeEditor;
	editor.theme.resizeTo("100%", "500px");
}

var editor = new MediumEditor('#jseditor', {
	toolbar: {
	    /* These are the default options for the toolbar,
	       if nothing is passed this is what is used */
	    allowMultiParagraphSelection: true,
	    buttons: ['bold', 'italic', 'underline', 'anchor', 'h2', 'h3', 'image'],
	    diffLeft: 0,
	    diffTop: -10,
	    firstButtonClass: 'medium-editor-button-first',
	    lastButtonClass: 'medium-editor-button-last',
	    relativeContainer: null,
	    standardizeSelectionStart: false,
	    static: false,
	    /* options which only apply when static is true */
	    align: 'center',
	    sticky: false,
	    updateOnEmptySelection: false
	}
    });

</script>
EOF;
		return $script;
	}

}