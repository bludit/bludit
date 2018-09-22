<?php

class pluginHTMLCode extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'head'=>'',
			'header'=>'',
			'footer'=>''
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Site Head</label>';
		$html .= '<textarea name="head" id="jshead">'.$this->getValue('head').'</textarea>';
		$html .= '<span class="tip">'.$L->get('insert-code-in-the-theme-inside-the-tag-head').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Site Header</label>';
		$html .= '<textarea name="header" id="jsheader">'.$this->getValue('header').'</textarea>';
		$html .= '<span class="tip">'.$L->get('insert-code-in-the-theme-at-the-top').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Site Footer</label>';
		$html .= '<textarea name="footer" id="jsfooter">'.$this->getValue('footer').'</textarea>';
		$html .= '<span class="tip">'.$L->get('insert-code-in-the-theme-at-the-bottom').'</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		return html_entity_decode($this->getValue('head'));
	}

	public function siteBodyBegin()
	{
		return html_entity_decode($this->getValue('header'));
	}

	public function siteBodyEnd()
	{
		return html_entity_decode($this->getValue('footer'));
	}
}