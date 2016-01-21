<?php

class pluginAbout extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'About',
			'text'=>''
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Plugin label').'</label>';
		$html .= '<input name="label" id="jslabel" type="text" value="'.$this->getDbField('label').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('About').'</label>';
		$html .= '<textarea name="text" id="jstext">'.$this->getDbField('text').'</textarea>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		$html  = '<div class="plugin plugin-about">';
		$html .= '<h2>'.$this->getDbField('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= nl2br($this->getDbField('text'));
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}