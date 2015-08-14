<?php

class pluginDisqus extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'shortname'=>''
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>Disqus shortname</label>';
		$html .= '<input name="shortname" id="jsshortname" type="text" value="'.$this->getDbField('shortname').'">';
		$html .= '</div>';

		return $html;
	}

	public function postEnd()
	{
		$html  = '<div id="disqus_thread"></div>';
		return $html;
	}

	public function pageEnd()
	{
		return $this->postEnd();
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