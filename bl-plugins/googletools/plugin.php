<?php

class pluginGoogleTools extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'tracking-id'=>'',
			'google-site-verification'=>''
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