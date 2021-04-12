<?php

class pluginDisqus extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'shortname'=>'',
			'enableLinks'=>'',
                        'enablePages'=>true,
			'enableStatic'=>true,
			'enableSticky'=>true
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('disqus-shortname').'</label>';
		$html .= '<input name="shortname" id="jsshortname" type="text" value="'.$this->getValue('shortname').'">';
		$html .= '<span class="tip">'.$L->get('get-the-shortname-from-the-disqus-general-settings').'</span>';

		$html .= '<label>'.$L->get('enable-disqus-on-links').'</label>';
		$html .= '<input name="enableLinks" id="jsenablelinks" type="text" value="'.$this->getValue('enableLinks').'">';
		$html .= '<span class="tip">'.$L->get('tip-disqus-on-links').'</span>';
		
		$html .= '</div>';

                $html .= '<div>';
                $html .= '<label>'.$L->get('enable-disqus-on-pages').'</label>';
                $html .= '<select name="enablePages">';
                $html .= '<option value="true" '.($this->getValue('enablePages')===true?'selected':'').'>'.$L->get('enabled').'</option>';
                $html .= '<option value="false" '.($this->getValue('enablePages')===false?'selected':'').'>'.$L->get('disabled').'</option>';
                $html .= '</select>';
		$html .= '</div>';

                $html .= '<div>';
                $html .= '<label>'.$L->get('enable-disqus-on-static-pages').'</label>';
                $html .= '<select name="enableStatic">';
                $html .= '<option value="true" '.($this->getValue('enableStatic')===true?'selected':'').'>'.$L->get('enabled').'</option>';
                $html .= '<option value="false" '.($this->getValue('enableStatic')===false?'selected':'').'>'.$L->get('disabled').'</option>';
                $html .= '</select>';
		$html .= '</div>';

                $html .= '<div>';
                $html .= '<label>'.$L->get('enable-disqus-on-sticky-pages').'</label>';
                $html .= '<select name="enableSticky">';
                $html .= '<option value="true" '.($this->getValue('enableSticky')===true?'selected':'').'>'.$L->get('enabled').'</option>';
                $html .= '<option value="false" '.($this->getValue('enableSticky')===false?'selected':'').'>'.$L->get('disabled').'</option>';
                $html .= '</select>';
                $html .= '</div>';

		return $html;
	}

	public function pageEnd()
	{
		global $url;
		global $WHERE_AM_I;

		// Do not shows disqus on page not found
		if ($url->notFound()) {
			return false;
		}

		if ($WHERE_AM_I==='page') {
			global $page;

			$enableLinks = $this->getValue('enableLinks');
			if(strlen(trim($enableLinks)) > 0 && strpos($enableLinks, $page->key()) !== false){
				return $this->javascript();
			}
			else {
				if ($page->published() && $this->getValue('enablePages')) {
					return $this->javascript();
				}
				if ($page->isStatic() && $this->getValue('enableStatic')) {
					return $this->javascript();
				}
				if ($page->sticky() && $this->getValue('enableSticky')) {
					return $this->javascript();
				}
			}
		}

		return false;
	}

	private function javascript()
	{
		global $page;
		$pageURL = $page->permalink();
		$pageID = $page->uuid();
		$shortname = $this->getValue('shortname');

$code = <<<EOF
<!-- Disqus plugin -->
<div id="disqus_thread"></div>
<script>

	var disqus_config = function () {
		this.page.url = '$pageURL';
		this.page.identifier = '$pageID';
	};

	(function() { // DON'T EDIT BELOW THIS LINE
		var d = document, s = d.createElement('script');
		s.src = 'https://$shortname.disqus.com/embed.js';
		s.setAttribute('data-timestamp', +new Date());
		(d.head || d.body).appendChild(s);
	})();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<!-- /Disqus plugin -->
EOF;
		return $code;
	}

}
