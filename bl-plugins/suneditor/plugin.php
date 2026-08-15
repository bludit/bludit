<?php

class pluginSuneditor extends Plugin
{

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function init()
	{
		$this->dbFields = array(
			'buttons' => 'undo,redo,|,bold,underline,italic,strike,|,fontColor,backgroundColor,removeFormat,|,list,align,outdent,indent,|,link,image,table,hr,|,fullScreen,codeView,showBlocks'
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
		$html .= '<span class="tip">' . $L->get('suneditor-buttons-tip') . '</span>';
		$html .= '</div>';

		return $html;
	}

	// Converts a flat "a,b,|,c,d" string into SunEditor's nested buttonList array, eg [['a','b'],['c','d']]
	private function buildButtonList($buttons)
	{
		$groups = array();
		$current = array();
		foreach (array_filter(explode(',', $buttons)) as $button) {
			if ($button === '|') {
				if (!empty($current)) {
					$groups[] = $current;
					$current = array();
				}
				continue;
			}
			$current[] = $button;
		}
		if (!empty($current)) {
			$groups[] = $current;
		}
		return $groups;
	}

	public function adminHead()
	{
		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$html  = $this->includeCSS('suneditor.min.css');
		$html .= $this->includeJS('suneditor.min.js');
		return $html;
	}

	public function adminBodyEnd()
	{
		global $L;

		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$buttonList = json_encode($this->buildButtonList($this->getValue('buttons')));
		$imageAltText = htmlspecialchars($L->g('Image description'), ENT_QUOTES);

		$html = <<<EOF
<script>

	var sunEditor = null;

	// Insert an image in the editor at the cursor position
	// Function required for Bludit
	function editorInsertMedia(filename) {
		sunEditor.insertHTML("<img src=\""+filename+"\" alt=\"$imageAltText\">");
	}

	// Insert a linked image in the editor at the cursor position
	// Function required for Bludit
	function editorInsertLinkedMedia(filename, link) {
		sunEditor.insertHTML("<a href=\""+link+"\"><img src=\""+filename+"\" alt=\"$imageAltText\"></a>");
	}

	// Returns the content of the editor
	// Function required for Bludit
	function editorGetContent() {
		return sunEditor.getContents();
	}

	sunEditor = SUNEDITOR.create(document.getElementById('jseditor'), {
		plugins: SUNEDITOR.plugins,
		buttonList: $buttonList,
		height: '100%',
		resizingBar: false,
		charCounter: true
	});

</script>
EOF;
		return $html;
	}
}
