<?php
class pluginPinterest extends Plugin {
	public function init()
	{
		$this->dbFields = array(
			'verification-code'=>''
		);
	}
	public function form()
	{
		global $Language;
		$html  = '<div>';
		$html .= '<label for="jsgoogle-site-verification">'.$Language->get('Google Webmasters tools').'</label>';
		$html .= '<input id="jsgoogle-site-verification" type="text" name="google-site-verification" value="'.$this->getDbField('google-site-verification').'">';
		$html .= '<div class="tip">'.$Language->get('complete-this-field-with-the-google-site-verification').'</div>';
		$html .= '</div>';
		return $html;
	}
	public function siteHead()
	{
		$html  = PHP_EOL.'<!-- Google Webmasters Tools -->'.PHP_EOL;
		$html .= '<meta name="google-site-verification" content="'.$this->getDbField('google-site-verification').'">'.PHP_EOL;
		if(Text::isEmpty($this->getDbField('google-site-verification'))) {
			return false;
		}
		return $html;
	}
	public function siteBodyEnd()
	{
		$html  = PHP_EOL.'<!-- Pinterest -->'.PHP_EOL;
		$html .= '<meta name="p:domain_verify" content=".$this->getDbField('verification-code')."/>';
		if(Text::isEmpty($this->getDbField('verification-code'))) {
			return false;
		}
		return $html;
	}
}
