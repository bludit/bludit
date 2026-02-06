<?php

class pluginHTMLCode extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'head'=>'',
			'header'=>'',
			'footer'=>'',
			'adminHead'=>'',
			'adminHeader'=>'',
			'adminFooter'=>''
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<h2>'.$L->g('Website').'</h2>';

		$html .= '<div>';
		$html .= '<label>Head</label>';
		$html .= '<textarea name="head" id="jshead">'.$this->getValue('head').'</textarea>';
		$html .= '<span class="tip">'.$L->get('insert-code-in-the-theme-inside-the-tag-head').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Header</label>';
		$html .= '<textarea name="header" id="jsheader">'.$this->getValue('header').'</textarea>';
		$html .= '<span class="tip">'.$L->get('insert-code-in-the-theme-at-the-top').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Footer</label>';
		$html .= '<textarea name="footer" id="jsfooter">'.$this->getValue('footer').'</textarea>';
		$html .= '<span class="tip">'.$L->get('insert-code-in-the-theme-at-the-bottom').'</span>';
		$html .= '</div>';

		$html .= '<h2 class="mt-4">'.$L->g('Admin area').'</h2>';

		$html .= '<div>';
		$html .= '<label>Head</label>';
		$html .= '<textarea name="adminHead">'.$this->getValue('adminHead').'</textarea>';
		$html .= '<span class="tip">'.$L->get('insert-code-in-the-theme-inside-the-tag-head').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Header</label>';
		$html .= '<textarea name="adminHeader">'.$this->getValue('adminHeader').'</textarea>';
		$html .= '<span class="tip">'.$L->get('insert-code-in-the-theme-at-the-top').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>Footer</label>';
		$html .= '<textarea name="adminFooter">'.$this->getValue('adminFooter').'</textarea>';
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

	public function adminHead()
	{
		return html_entity_decode($this->getValue('adminHead'));
	}

	public function adminBodyBegin()
	{
		return html_entity_decode($this->getValue('adminHeader'));
	}

	public function adminBodyEnd()
	{
		return html_entity_decode($this->getValue('adminFooter'));
	}
}