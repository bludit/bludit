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
		global $pages;
		global $Url;

		$page = $pages[0];
		if (empty($page)) {
			return false;
		}

		if ( (!$Url->notFound()) && ($Url->whereAmI()=='page') && ($page->allowComments()) ) {
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
			';
			return $html;
		}

		return false;
	}

}