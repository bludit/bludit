<?php

class pluginQuill extends Plugin {

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function adminHead()
	{
		// Load Quill only on the selected controllers to keep perfomance
		// For example, in the dashboard is not going to be included the Quill CSS and JS scripts.
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		$html  = '<link rel="stylesheet" type="text/css" href="'.$this->htmlPath().'quill/quill.snow.css?version='.BLUDIT_VERSION.'">'.PHP_EOL;
		$html .= '<link rel="stylesheet" type="text/css" href="'.$this->htmlPath().'quill/bludit.css?version='.BLUDIT_VERSION.'">'.PHP_EOL;
		$html .= '<script charset="utf-8" src="'.$this->htmlPath().'quill/quill.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
		return $html;
	}

	public function adminBodyEnd()
	{
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

$script = <<<EOF
<script>
var quill;

// Function required for Media Manager to insert a file on the editor
function editorInsertMedia(filename) {
	var Delta = Quill.import("delta");
	quill.updateContents(new Delta()
		.retain(quill.getSelection().index)
		.insert('<img alt="'+filename+'" src="'+DOMAIN_UPLOADS+filename+'" />')
	);
}

// Function required for Autosave function
// Returns the content of the editor
function editorGetContent() {
	return quill.container.firstChild.innerHTML;
}

$(document).ready(function() {

	quill = new Quill("#jscontent", {
		modules: {
			toolbar: [
				[{ header: [1, 2, false] }],
				['bold', 'italic', 'underline'],
				['image', 'code-block']
			]
		},
		placeholder: "Content, support Markdown and HTML.",
		theme: "snow"
	});

	// Change button images event handler to open the Media Manager
	quill.getModule("toolbar").addHandler("image", openMediaManager);

});
</script>
EOF;
		return $script;
	}
}
