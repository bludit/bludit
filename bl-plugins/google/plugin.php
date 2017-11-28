<?php

class pluginGoogle extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'google-site-verification'=>'',
			'google-analytics-tracking-id'=>'',
			'google-tag-manager'=>''
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label for="jsgoogle-site-verification">'.$Language->get('Google Webmasters tools').'</label>';
		$html .= '<input id="jsgoogle-site-verification" type="text" name="google-site-verification" value="'.$this->getDbField('google-site-verification').'">';
		$html .= '<span class="tip">'.$Language->get('complete-this-field-with-the-google-site-verification').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label for="jstracking-id">'.$Language->get('Google Analytics Tracking ID').'</label>';
		$html .= '<input id="jstracking-id" type="text" name="google-analytics-tracking-id" value="'.$this->getDbField('google-analytics-tracking-id').'">';
		$html .= '<span class="tip">'.$Language->get('complete-this-field-with-the-tracking-id').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label for="jsgoogle-tag-manager">'.$Language->get('Google Tag Manager').'</label>';
		$html .= '<input id="jsgoogle-tag-manager" type="text" name="google-tag-manager" value="'.$this->getDbField('google-tag-manager').'">';
		$html .= '<span class="tip">'.$Language->get('complete-this-field-with-the-tracking-id-google-tag').'</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		global $Url;

		$html = '';

		// Google HTML tag
		if( $this->getValue('google-site-verification') && $Url->whereAmI()=='home' ) {
			$html .= PHP_EOL.'<!-- Google HTML tag -->'.PHP_EOL;
			$html .= '<meta name="google-site-verification" content="'.$this->getDbField('google-site-verification').'" />'.PHP_EOL;
		}

		// Google Tag Manager
		if( $this->getValue('google-tag-manager') ) {
			$html .= PHP_EOL."<!-- Google Tag Manager -->".PHP_EOL;
			$html .= "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':".PHP_EOL;
			$html .= "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],".PHP_EOL;
			$html .= "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=".PHP_EOL;
			$html .= "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);".PHP_EOL;
			$html .= "})(window,document,'script','dataLayer','".$this->getValue('google-tag-manager')."');</script>".PHP_EOL;
			$html .= "<!-- End Google Tag Manager -->".PHP_EOL;
		}

		// Google Analytics
		if( $this->getValue('google-analytics-tracking-id') ) {
			$html .= PHP_EOL.'<!-- Google Analytics -->'.PHP_EOL;
			$html .= '
			<script async src="https://www.googletagmanager.com/gtag/js?id='.$this->getValue('google-analytics-tracking-id').'"></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag("js", new Date());

				gtag("config", "'.$this->getValue('google-analytics-tracking-id').'");
			</script>'.PHP_EOL;
		}

		return $html;
	}

	public function siteBodyBegin()
	{
		// Google Tag Manager
		if ($this->getValue('google-tag-manager')) {
			$html  = '<!-- Google Tag Manager (noscript) -->'.PHP_EOL;
			$html .= '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$this->getValue('google-tag-manager').'" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>'.PHP_EOL;
			$html .= '<!-- End Google Tag Manager (noscript) -->'.PHP_EOL;
			return $html;
		}
		return false;
	}

}
