<?php

class pluginMaintenanceMode extends Plugin
{

	public function init()
	{
		$this->dbFields = array(
			'enable' => false,
			'message' => 'Temporarily down for maintenance.'
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Enable maintenance mode') . '</label>';
		$html .= '<select name="enable">';
		$html .= '<option value="true" ' . ($this->getValue('enable') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('enable') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Message') . '</label>';
		$html .= '<input name="message" id="jsmessage" type="text" dir="auto" value="' . $this->getValue('message') . '">';
		$html .= '</div>';

		return $html;
	}

	public function beforeAll()
	{
		if ($this->getValue('enable')) {
			// Tell search engines the site is temporarily down, so they keep the pages indexed
			http_response_code(503);
			header('Retry-After: 3600');
			exit($this->getValue('message'));
		}
	}
}
