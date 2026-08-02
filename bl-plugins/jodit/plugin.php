<?php

class pluginJodit extends Plugin
{

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function init()
	{
		$this->dbFields = array(
			'buttons' => 'bold,italic,underline,strikethrough,|,ul,ol,|,outdent,indent,|,font,fontsize,brush,paragraph,|,image,link,table,|,align,|,hr,eraser,|,source,fullsize'
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('toolbar') . '</label>';
		$html .= '<input name="buttons" id="jsbuttons" type="text" dir="auto" value="' . htmlspecialchars($this->getValue('buttons'), ENT_QUOTES) . '">';
		$html .= '<span class="tip">' . $L->get('jodit-buttons-tip') . '</span>';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$html  = $this->includeCSS('jodit.min.css');
		$html .= $this->includeJS('jodit.min.js');
		return $html;
	}

	public function adminBodyEnd()
	{
		global $L;

		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$buttons = $this->getValue('buttons');
		// Convert comma-separated buttons to a JSON-encoded array
		$buttonsJson = json_encode(array_values(array_filter(explode(',', $buttons))));
		$version = $this->version();
		$imageAltText = htmlspecialchars($L->g('Image description'), ENT_QUOTES);

		$html = <<<EOF
<script>

	var joditEditor = null;

	// Insert an image in the editor at the cursor position
	// Function required for Bludit
	function editorInsertMedia(filename) {
		joditEditor.selection.insertHTML("<img src=\""+filename+"\" alt=\"$imageAltText\">");
	}

	// Insert a linked image in the editor at the cursor position
	// Function required for Bludit
	function editorInsertLinkedMedia(filename, link) {
		joditEditor.selection.insertHTML("<a href=\""+link+"\"><img src=\""+filename+"\" alt=\"$imageAltText\"></a>");
	}

	// Returns the content of the editor
	// Function required for Bludit
	function editorGetContent() {
		return joditEditor.value;
	}

	joditEditor = Jodit.make('#jseditor', {
		height: '100%',
		buttons: $buttonsJson,
		buttonsMD: $buttonsJson,
		buttonsSM: $buttonsJson,
		buttonsXS: $buttonsJson,
		toolbarAdaptive: false,
		license: '',
		defaultActionOnPaste: 'insert_as_html',
		askBeforePasteHTML: false,
		askBeforePasteFromWord: false,
		cache_suffix: "?version=$version"
	});

</script>
EOF;
		return $html;
	}
}
