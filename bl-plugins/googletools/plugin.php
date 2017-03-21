<?php

class pluginGoogleTools extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'tracking-id'=>'',
			'google-site-verification'=>'',
			'google-tag-manager'=>''
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

		$html .= '<div>';
		$html .= '<label for="jstracking-id">'.$Language->get('Google Analytics Tracking ID').'</label>';
		$html .= '<input id="jstracking-id" type="text" name="tracking-id" value="'.$this->getDbField('tracking-id').'">';
		$html .= '<div class="tip">'.$Language->get('complete-this-field-with-the-tracking-id').'</div>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label for="jsgoogle-tag-manager">'.$Language->get('Google Tag Manager').'</label>';
		$html .= '<input id="jsgoogle-tag-manager" type="text" name="google-tag-manager" value="'.$this->getDbField('google-tag-manager').'">';
		$html .= '<div class="tip">'.$Language->get('complete-this-field-with-the-tracking-id-google-tag').'</div>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		global $Url;
		
		$html = '';
		
		if((!empty($this->getDbField('google-site-verification'))) && ($Url->whereAmI()=='home')) {
			$html .= PHP_EOL.'<!-- Google Webmasters Tools -->'.PHP_EOL;
			$html .= '<meta name="google-site-verification" content="'.$this->getDbField('google-site-verification').'">'.PHP_EOL;
		}
		
		if(!(Text::isEmpty($this->getDbField('google-tag-manager')))) {
			$html .= PHP_EOL."<!-- Google Tag Manager -->".PHP_EOL;
			$html .= "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':".PHP_EOL;
			$html .= "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],".PHP_EOL;
			$html .= "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=".PHP_EOL;
			$html .= "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);".PHP_EOL;
			$html .= "})(window,document,'script','dataLayer','".$this->getDbField('google-tag-manager')."');</script>".PHP_EOL;
			$html .= "<!-- End Google Tag Manager -->".PHP_EOL;
		}
		
		return $html;
	}

	public function siteBodyBegin()
	{
		if((Text::isEmpty($this->getDbField('google-tag-manager')))) {
			return false;
		}
		
		$html = '<!-- Google Tag Manager (noscript) -->'.PHP_EOL;
		$html .= '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$this->getDbField('google-tag-manager').'" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>'.PHP_EOL;
		$html .= '<!-- End Google Tag Manager (noscript) -->'.PHP_EOL;
		
		return $html;
	}
	
	public function siteBodyEnd()
	{
		$html  = PHP_EOL.'<!-- Google Analytics -->'.PHP_EOL;
		$html .= "<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', '".$this->getDbField('tracking-id')."', 'auto');
	ga('send', 'pageview');
</script>".PHP_EOL;

		if(Text::isEmpty($this->getDbField('tracking-id'))) {
			return false;
		}

		return $html;
	}
}
