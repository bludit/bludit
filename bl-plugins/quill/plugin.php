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
		$this->dbFields = array(
			'tabSize'=>'2',
			'toolbar'=>'"bold", "italic", "heading", "|", "quote", "unordered-list", "|", "link", "image", "code", "horizontal-rule", "|", "preview", "side-by-side", "fullscreen", "guide"',
			'autosave'=>0,
			'spellChecker'=>0
		);
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

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Toolbar').'</label>';
		$html .= '<input name="toolbar" id="jstoolbar" type="text" value="'.$this->getDbField('toolbar').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Tab size').'</label>';
		$html .= '<input name="tabSize" id="jstabSize" type="text" value="'.$this->getDbField('tabSize').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input type="hidden" name="autosave" value="0">';
		$html .= '<input name="autosave" id="jsautosave" type="checkbox" value="1" '.($this->getDbField('autosave')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsautosave">'.$Language->get('Autosave').'</label>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input type="hidden" name="spellChecker" value="0">';
		$html .= '<input name="spellChecker" id="jsspellChecker" type="checkbox" value="1" '.($this->getDbField('spellChecker')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsspellChecker">'.$Language->get('spell-checker').'</label>';
		$html .= '</div>';

		return $html;
	}

	public function adminBodyEnd()
	{
		global $layout;
		global $Language;

		$html = '';
		// Load CSS and JS only on Controllers in array.
		if(in_array($layout['controller'], $this->loadWhenController))
		{
			$html .= '
			<script>

				var toolbarOptions = [
						["bold", "italic", "underline", "strike"],        // toggled buttons
						["blockquote", "code-block"],

						[{ "header": 1 }, { "header": 2 }],               // custom button values
						[{ "list": "ordered"}, { "list": "bullet" }],
						[{ "script": "sub"}, { "script": "super" }],      // superscript/subscript
						[{ "indent": "-1"}, { "indent": "+1" }],          // outdent/indent
						[{ "direction": "rtl" }],                         // text direction

						[{ "size": ["small", false, "large", "huge"] }],  // custom dropdown
						[{ "header": [1, 2, 3, 4, 5, 6, false] }],

						[{ "color": [] }, { "background": [] }],          // dropdown with defaults from theme
						[{ "font": [] }],
						[{ "align": [] }],

						["clean"]                                         // remove formatting button
				];

				var quill = new Quill("#jscontent", {
				  modules: {
				    toolbar: toolbarOptions
				  },
				  placeholder: "Compose an epic...",
				  theme: "snow"
				});
			</script>
			';
		}

		return $html;
	}
}
