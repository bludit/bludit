<?php

class pluginDisqus extends Plugin {

	private $enable;

	public function init()
	{
		$this->dbFields = array(
			'shortname'=>'',
			'enablePages'=>false,
			'enablePosts'=>false,
			'enableDefaultHomePage'=>false
		);
	}

	function __construct()
	{
		parent::__construct();

		global $Url;

		$this->enable = false;

		if( $this->getDbField('enablePosts') && ($Url->whereAmI()=='post') ) {
			$this->enable = true;
		}
		elseif( $this->getDbField('enablePages') && ($Url->whereAmI()=='page') ) {
			$this->enable = true;
		}
		elseif( $this->getDbField('enableDefaultHomePage') && ($Url->whereAmI()=='home') )
		{
			$this->enable = true;
		}
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Disqus shortname').'</label>';
		$html .= '<input name="shortname" id="jsshortname" type="text" value="'.$this->getDbField('shortname').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input name="enablePages" id="jsenablePages" type="checkbox" value="true" '.($this->getDbField('enablePages')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsenablePages">'.$Language->get('Enable Disqus on pages').'</label>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input name="enablePosts" id="jsenablePosts" type="checkbox" value="true" '.($this->getDbField('enablePosts')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsenablePosts">'.$Language->get('Enable Disqus on posts').'</label>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input name="enableDefaultHomePage" id="jsenableDefaultHomePage" type="checkbox" value="true" '.($this->getDbField('enableDefaultHomePage')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsenableDefaultHomePage">'.$Language->get('Enable Disqus on default home page').'</label>';
		$html .= '</div>';

		return $html;
	}

	public function postEnd()
	{
		if( $this->enable ) {
			return '<div id="disqus_thread"></div>';
		}

		return false;
	}

	public function pageEnd()
	{
		global $Url;

		// Bludit check not-found page after the plugin method construct.
		// It's necesary check here the page not-found.

		if( $this->enable && !$Url->notFound()) {
			return '<div id="disqus_thread"></div>';
		}

		return false;
	}

	public function siteHead()
	{
		if( $this->enable ) {
			return '<style>#disqus_thread { margin: 20px 0 }</style>';
		}

		return false;
	}

	public function siteBodyEnd()
	{
		if( $this->enable ) {

			$html = '
<script type="text/javascript">

	var disqus_shortname = "'.$this->getDbField('shortname').'";

	(function() {
	var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
	dsq.src = "//" + disqus_shortname + ".disqus.com/embed.js";
	(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
	})();

</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>';

			return $html;
		}

		return false;
	}
}