<?php

class pluginTinymce extends Plugin {

	private $loadWhenController = array(
		'new-post',
		'new-page',
		'edit-post',
		'edit-page'
	);

	public function init()
	{
		$this->dbFields = array(
			'plugins'=>'autoresize, fullscreen, pagebreak, link, textcolor, code, image',
			'toolbar'=>'bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | styleselect | link forecolor backcolor removeformat image | pagebreak code fullscreen'
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>Tinymce plugins</label>';
		$html .= '<input name="plugins" id="jsplugins" type="text" value="'.$this->getDbField('plugins').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Tinymce toolbar</label>';
		$html .= '<input name="toolbar" id="jstoolbar" type="text" value="'.$this->getDbField('toolbar').'">';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		global $Language;
		global $Site;
		global $layout;

		$html = '';

		// Load CSS and JS only on Controllers in array.
		if(in_array($layout['controller'], $this->loadWhenController))
		{
			$language = $Site->shortLanguage();
			$pluginPath = $this->htmlPath();

			$html  = '<script src="'.$pluginPath.'tinymce/tinymce.min.js"></script>';
		}

		return $html;
	}

	public function adminBodyEnd()
	{
		global $Language;
		global $Site;
		global $layout;

		$html = '';

		// Load CSS and JS only on Controllers in array.
		if(in_array($layout['controller'], $this->loadWhenController))
		{
			$pluginPath = $this->htmlPath();

			$language = '';
			if($Site->shortLanguage()!=='en') {
				$language = 'language_url:"'.$pluginPath.'tinymce/langs/'.$Site->shortLanguage().'.js",';
			}

			$html  = '<script>$(document).ready(function() { ';
			$html .= 'tinymce.init({
				selector: "#jscontent",
				plugins: "'.$this->getDbField('plugins').'",
				toolbar: "'.$this->getDbField('toolbar').'",
				content_css: "'.$pluginPath.'css/editor.css?version='.$this->version().'",
				theme: "modern",
				height:"400px",
				width:"100%",
				statusbar: false,
				menubar:false,
				'.$language.'
				browser_spellcheck: true,
				autoresize_bottom_margin: "50",
				pagebreak_separator: "'.PAGE_BREAK.'",
				paste_as_text: true,
    				document_base_url: "'.HTML_PATH_UPLOADS.'"
			});';

			$html .= '$("#jsaddImage").on("click", function() {

					var filename = $("#jsimageList option:selected" ).text();

					if(!filename.trim()) {
						return false;
					}

					tinymce.activeEditor.insertContent("<img src=\""+filename+"\" alt=\"\">" + "\n");
			});';

			$html .= '}); </script>';
		}

		return $html;
	}
}