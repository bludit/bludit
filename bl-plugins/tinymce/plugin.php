<?php

class pluginTinymce extends Plugin {

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function init()
	{
		$this->dbFields = array(
			'toolbar1'=>'formatselect bold italic forecolor backcolor removeformat | bullist numlist table | blockquote alignleft aligncenter alignright | link unlink pagebreak image code',
			'toolbar2'=>'',
			'plugins'=>'code autolink image link pagebreak advlist lists textpattern table'
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
		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}
		$html  = '<link rel="stylesheet" type="text/css" href="'.$this->htmlPath().'css/tinymce_toolbar.css">'.PHP_EOL;
		$html .= '<script src="'.$this->htmlPath().'tinymce/tinymce.min.js?version='.$this->version().'"></script>';
		return $html;
	}

	public function adminBodyEnd()
	{
		global $L;

		// Load the plugin only in the controllers setted in $this->loadOnController
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$toolbar1 = $this->getValue('toolbar1');
		$toolbar2 = $this->getValue('toolbar2');
		$content_css = $this->htmlPath().'css/tinymce_content.css';
		$plugins = $this->getValue('plugins');
		$version = $this->version();

		$lang = 'en';
		if (file_exists($this->phpPath().'tinymce'.DS.'langs'.DS.$L->currentLanguage().'.js')) {
			$lang = $L->currentLanguage();
		} elseif (file_exists($this->phpPath().'tinymce'.DS.'langs'.DS.$L->currentLanguageShortVersion().'.js')) {
			$lang = $L->currentLanguageShortVersion();
		}

		if (IMAGE_RELATIVE_TO_ABSOLUTE) {
			$document_base_url = 'document_base_url: "'.DOMAIN_UPLOADS.'",';
		} else {
			$document_base_url = '';
		}

$html = <<<EOF
<script>

	// Insert an image in the editor at the cursor position
	// Function required for Bludit
	function editorInsertMedia(filename) {
		tinymce.activeEditor.insertContent("<img src=\""+filename+"\" alt=\"\">");
	}

	// Returns the content of the editor
	// Function required for Bludit
	function editorGetContent() {
		return tinymce.get('jseditor').getContent();
	}

	tinymce.init({
		selector: "#jseditor",
		auto_focus: "jseditor",
		element_format : "html",
		entity_encoding : "raw",
		skin: "oxide",
		schema: "html5",
		statusbar: false,
		menubar:false,
		branding: false,
		browser_spellcheck: true,
		pagebreak_separator: PAGE_BREAK,
		paste_as_text: true,
		remove_script_host: false,
		convert_urls: true,
		relative_urls: false,
		valid_elements: "*[*]",
		cache_suffix: "?version=$version",
		$document_base_url
		plugins: ["$plugins"],
		toolbar1: "$toolbar1",
		toolbar2: "$toolbar2",
		language: "$lang",
		content_css: "$content_css"
	});

</script>
EOF;
		return $html;
	}

}