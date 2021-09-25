<?php

class pluginAbout extends Plugin {

	public function init() {
		$this->dbFields = array(
			'label'=>'About',
			'text'=>''
		);
	}

	public function form() {
		global $L;

		$html  = '<div class="mb-3">';
		$html .= '<label class="form-label" for="label">'.$L->get('Label').'</label>';
		$html .= '<input class="form-control" id="label" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<div class="form-text">'.$L->get('This title is almost always used in the sidebar of the site').'</div>';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="text">'.$L->get('About').'</label>';
		$html .= '<textarea class="form-control" rows="3" name="text" id="text">'.$this->getValue('text').'</textarea>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar() {
		$html  = '<div class="plugin plugin-about">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= html_entity_decode(nl2br($this->getValue('text')));
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}