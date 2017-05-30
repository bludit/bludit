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
		$html .= '<label>'.$Language->get('Disqus shortname').'</label>';
		$html .= '<input name="shortname" id="jsshortname" type="text" value="'.$this->getValue('shortname').'">';
		$html .= '</div>';

		return $html;
	}

	public function pageEnd()
	{
		global $page;

		if( ($page->key()!='error') && ($page->allowComments()) ) {
			$html  = '<div id="disqus_thread"></div>';
			$html .= '<script type="text/javascript">
					var disqus_config = function () {
						this.page.url = "'.$page->permalink().'";
						this.page.identifier = "'.$page->uuid().'";
					};

					(function() {
						var d = document, s = d.createElement("script");
						s.src = "https://'.$this->getValue('shortname').'.disqus.com/embed.js";
						s.setAttribute("data-timestamp", +new Date());
						(d.head || d.body).appendChild(s);
					})();
				</script>

				<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
			';
			return $html;
		}

		return false;
	}

}