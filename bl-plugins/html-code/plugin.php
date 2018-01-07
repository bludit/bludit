<?php

class pluginHTMLCode extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'header'=>'',
			'footer'=>''
		);
	}

	public function form()
	{
		$html  = '<div>';
		$html .= '<label>Header</label>';
		$html .= '<textarea name="header" id="jsheader">'.$this->getValue('header').'</textarea>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Footer</label>';
		$html .= '<textarea name="footer" id="jsfooter">'.$this->getValue('footer').'</textarea>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		return html_entity_decode($this->getValue('header'));
	}

	public function siteBodyEnd()
	{
		return html_entity_decode($this->getValue('footer'));
	}
}