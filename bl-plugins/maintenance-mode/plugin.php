<?php

class pluginMaintenanceMode extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'enable'=>true,
			'message'=>'Temporarily down for maintenance.'
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Enable maintenance mode').'</label>';
		$html .= '<select name="enable">';
		$html .= '<option value="true" '.($this->getValue('enable')===true?'selected':'').'>Enabled</option>';
		$html .= '<option value="false" '.($this->getValue('enable')===false?'selected':'').'>Disabled</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Message').'</label>';
		$html .= '<input name="message" id="jsmessage" type="text" value="'.$this->getDbField('message').'">';
		$html .= '</div>';

		return $html;
	}

	public function beforeAll()
	{
		if ($this->getDbField('enable')) {
			exit( $this->getDbField('message') );
		}
	}
}