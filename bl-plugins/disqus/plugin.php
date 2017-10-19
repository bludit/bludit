<?php

class pluginDisqus extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'shortname'=>'',
                        'enablePages'=>false,
                        'enablePosts'=>true
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('disqus-shortname').'</label>';
		$html .= '<input name="shortname" id="jsshortname" type="text" value="'.$this->getValue('shortname').'">';
		$html .= '</div>';

                $html .= '<div>';
                $html .= '<label>'.$Language->get('enable-disqus-on-pages').'</label>';
                $html .= '<select name="enablePages">';
                $html .= '<option value="true" '.($this->getValue('enablePages')===true?'selected':'').'>'.$Language->get('enabled').'</option>';
                $html .= '<option value="false" '.($this->getValue('enablePages')===false?'selected':'').'>'.$Language->get('disabled').'</option>';
                $html .= '</select>';
                $html .= '</div>';
                $html .= '<div>';
                $html .= '<label>'.$Language->get('enable-disqus-on-posts').'</label>';
                $html .= '<select name="enablePosts">';
                $html .= '<option value="true" '.($this->getValue('enablePosts')===true?'selected':'').'>'.$Language->get('enabled').'</option>';
                $html .= '<option value="false" '.($this->getValue('enablePosts')===false?'selected':'').'>'.$Language->get('disabled').'</option>';
                $html .= '</select>';
                $html .= '</div>';

		return $html;
	}

	public function pageEnd()
	{
		global $pages;
		global $Url, $Page;

		$page = $pages[0];
		if (empty($page)) {
			return false;
		}

		if ( !$Url->notFound() && 
		     ( $Url->whereAmI()=='page' &&
			(($this->getDbField('enablePosts') && $Page->status()=='published') || 
			($this->getDbField('enablePages') && $Page->status()=='static'))
		     ) && 
		     $page->allowComments() ) {
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
