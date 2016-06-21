<?php

class pluginMaintenanceMode extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'enable'=>0,
			'message'=>'Temporarily down for maintenance.'
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<input type="hidden" name="enable" value="0">';
		$html .= '<input name="enable" id="jsenable" type="checkbox" value="1" '.($this->getDbField('enable')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsenable">'.$Language->get('Enable maintenance mode').'</label>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Message').'</label>';
		$html .= '<input name="message" id="jsmessage" type="text" value="'.$this->getDbField('message').'">';
		$html .= '</div>';

		return $html;
	}

	public function beforeSiteLoad()
	{
		if($this->getDbField('enable')) {
			exit( $this->getDbField('message') );
		}
	}
}