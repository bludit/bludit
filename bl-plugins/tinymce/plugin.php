<?php

class pluginTinymce extends Plugin {

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function adminHead()
	{
		if (in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return '<script src="'.$this->htmlPath().'tinymce/tinymce.min.js"></script>';
		}
		return false;
	}

	public function adminBodyEnd()
	{
		global $Language;

		if (in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return '<script>

			function editorAddImage(filename) {
				tinymce.activeEditor.insertContent("<img src=\""+filename+"\" alt=\"'.$Language->get('Image description').'\">" + "\n");
			}

			tinymce.init({
				selector: "#jscontent",
				theme: "modern",
				skin: "bludit",
				min_height: 500,
				max_height: 1000,
				element_format : "html",
				entity_encoding : "raw",
				schema: "html5",
				statusbar: false,
				menubar:false,
				branding: false,
				browser_spellcheck: true,
				pagebreak_separator: "'.PAGE_BREAK.'",
				paste_as_text: true,
    				document_base_url: "'.DOMAIN_UPLOADS.'",
				plugins: [
					"autosave",
					"searchreplace autolink directionality",
					"visualblocks visualchars",
					"fullscreen image link media template",
					"codesample table hr pagebreak",
					"advlist lists textcolor wordcount",
					"contextmenu colorpicker textpattern"
				],
				toolbar: "restoredraft | formatselect | bold italic strikethrough forecolor backcolor  | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat image table | pagebreak code fullscreen"
			});

			</script>';
		}
		return false;
	}
}