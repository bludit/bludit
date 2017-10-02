<?php

class pluginTinymce extends Plugin {

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function adminHead()
	{
		if (in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return '<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>';
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
				element_format : "html",
				entity_encoding : "raw",
				schema: "html5",
				statusbar: false,
				menubar:false,
				branding: false,
				browser_spellcheck: true,
				autoresize_bottom_margin: "50",
				pagebreak_separator: "'.PAGE_BREAK.'",
				paste_as_text: true,
    				document_base_url: "'.DOMAIN_UPLOADS.'",
				plugins: [
					"autoresize advlist autolink lists link image charmap print preview anchor",
					"searchreplace visualblocks code fullscreen",
					"insertdatetime media table contextmenu paste code pagebreak"
				],
				toolbar: "bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | styleselect | link forecolor backcolor removeformat image | pagebreak code fullscreen"
			});

			</script>';
		}
		return false;
	}
}