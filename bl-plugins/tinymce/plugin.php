<?php

class pluginTinymce extends Plugin {

	private $loadOnViews = array(
		'editor' // Load this plugin only in the Editor view
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

		$html  = '<div class="mb-3">';
		$html .= '<label class="form-label" for="toolbar1">'.$L->get('Toolbar top').'</label>';
		$html .= '<input class="form-control" name="toolbar1" id="toolbar1" type="text" value="'.$this->getValue('toolbar1').'">';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="toolbar2">'.$L->get('Toolbar bottom').'</label>';
		$html .= '<input class="form-control" name="toolbar2" id="toolbar2" type="text" value="'.$this->getValue('toolbar2').'">';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="plugins">'.$L->get('Plugins').'</label>';
		$html .= '<input class="form-control" name="plugins" id="plugins" type="text" value="'.$this->getValue('plugins').'">';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		global $site;
		// Load the plugin only in the controllers setted in $this->loadOnViews
		if (!in_array($GLOBALS['ADMIN_VIEW'], $this->loadOnViews)) {
			return false;
		}

		$html = '<link rel="stylesheet" type="text/css" href="'.$this->htmlPath().'css/lightmode-toolbar.css">'.PHP_EOL;
		if ($site->darkModeAdmin()) {
			$html = '<link rel="stylesheet" type="text/css" href="'.$this->htmlPath().'css/darkmode-toolbar.css">'.PHP_EOL;
		}
		$html .= '<script src="'.$this->htmlPath().'tinymce/tinymce.min.js?version='.$this->version().'"></script>';
		return $html;
	}

	public function adminBodyEnd()
	{
		global $L;
		global $site;

		// Load the plugin only in the controllers setted in $this->loadOnViews
		if (!in_array($GLOBALS['ADMIN_VIEW'], $this->loadOnViews)) {
			return false;
		}

		$toolbar1 = $this->getValue('toolbar1');
		$toolbar2 = $this->getValue('toolbar2');
		$content_css = $this->htmlPath().'css/lightmode-content.css'.','.$this->htmlPath().'css/default-content.css';
		if ($site->darkModeAdmin()) {
			$content_css = $this->htmlPath().'css/darkmode-content.css'.','.$this->htmlPath().'css/default-content.css';
		}
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

		$skin = 'oxide';
		if ($site->darkModeAdmin()) {
			$skin = 'oxide-dark';
		}

		return <<<EOF
		<script>

			// Function required for Bludit
			// Returns the content of the editor
			function editorGetContent() {
				return tinymce.get('editor').getContent();
			}

			// Function required for Bludit
			// Insert HTML content at the cursor position
			function editorInsertContent(content, type='') {
				if (type == 'image') {
					var html = '<img src="' + content + '" alt="" />';
				} else {
					var html = content;
				}
				tinymce.activeEditor.insertContent(html);
			}

			tinymce.init({
				selector: "#editor",
				auto_focus: "editor",
				element_format : "html",
				entity_encoding : "raw",
				skin: "$skin",
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
				content_css: "$content_css",
				init_instance_callback: function(editor) {
					editor.on("keydown", function(event) {
						keypress(event);
					});
				}
			});

		</script>
		EOF;
	}

}