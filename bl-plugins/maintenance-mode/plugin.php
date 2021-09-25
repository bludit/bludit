<?php

class pluginMaintenanceMode extends Plugin {

	public function init() {
		$this->dbFields = array(
			'enable'=>false,
			'message'=>'Temporarily down for maintenance.'
		);
	}

	public function form() {
		global $L;

		$html  = '<div class="mb-3">';
		$html .= '<label class="form-label" for="enable">'.$L->get('Enable maintenance mode').'</label>';
		$html .= '<select class="form-select" id="enable" name="enable">';
		$html .= '<option value="true" '.($this->getValue('enable')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('enable')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="message">'.$L->get('Message').'</label>';
		$html .= '<input class="form-control" id="message" name="message" type="text" value="'.$this->getValue('message').'">';
		$html .= '</div>';

		return $html;
	}

	public function beforeAll() {
		if ($this->getValue('enable')) {
			exit( $this->getValue('message') );
		}
	}
}