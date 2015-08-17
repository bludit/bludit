<?php

class pluginDisqus extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'shortname'=>'',
			'enablePages'=>false,
			'enablePosts'=>true,
			'enableDefaultHomePage'=>false
		);
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
		$html = '';
		if( $this->getDbField('enablePosts') ) {
			$html  = '<div id="disqus_thread"></div>';
		}
		return $html;
	}

	public function pageEnd()
	{
		$html = '';
		if( $this->getDbField('enablePages') ) {
			$html  = '<div id="disqus_thread"></div>';
		}
		return $html;
	}

	public function siteHead()
	{
		$html = '<style>#disqus_thread { margin: 20px 0 }</style>';
		return $html;
	}

	public function siteBodyEnd()
	{
		global $Url;

		if( ($Url->whereAmI()!='post') && ($Url->whereAmI()!='page') ) {
			return '';
		}

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
}