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
			'plugins'=>'autoresize, fullscreen, pagebreak, link, textcolor, code, image, paste',
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
		global $layout;

		if (in_array($layout['controller'], $this->loadWhenController)) {
			return '<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>';
		}
		return false;
	}

	public function adminBodyEnd()
	{
		global $layout;

		if (in_array($layout['controller'], $this->loadWhenController)) {
			return '<script>tinymce.init({ selector:"textarea" });</script>';
		}
		return false;
	}
}