<?php

class pluginQuill extends Plugin {

	private $loadWhenController = array(
		'new-post',
		'new-page',
		'edit-post',
		'edit-page'
	);

	public function init()
	{
		return false;
	}

	// Returns true if the plugins is loaded on the controller defined
	private function enabled()
	{
		global $layout;
		return in_array($layout['controller'], $this->loadWhenController);
	}

	public function adminHead()
	{
		$html = '';
		if( $this->enabled() ) {
			$html .= '<link href="https://cdn.quilljs.com/1.2.4/quill.snow.css" rel="stylesheet">';
			$html .= '<script src="https://cdn.quilljs.com/1.2.4/quill.js"></script>';
		}

		return $html;
	}

	public function adminBodyEnd()
	{
		global $layout;
		global $Language;

		$html = '';
		if( $this->enabled() ) {
			$html .= '
<script>

$("#jscontent").hide().after("<div id=\"quillcontent\"></div>");

var quill = new Quill("#quillcontent", {
  modules: {
    toolbar: [
      [{ header: [1, 2, false] }],
      ["bold", "italic", "underline"],
      ["image"]
    ]
  },
  theme: "snow"
});

$(".uk-form").submit(function( event ) {
	$("#jscontent").val(quill.root.innerHTML);
	return true;
});

</script>
			';
		}

		return $html;
	}
}