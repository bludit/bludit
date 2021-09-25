<?php

class pluginDisqus extends Plugin {

    public function init() {
        $this->dbFields = array(
            'shortname' => '',
            'enableStandard' => true,
            'enableStatic' => true,
            'enableSticky' => true,
            'enableUnlisted' => true
        );
    }

    public function form() {
        global $L;

        $html  = '<div class="mb-3">';
        $html .= '<label class="form-label" for="shortname">' . $L->get('disqus-shortname') . '</label>';
        $html .= '<input class="form-control" id="shortname" name="shortname" type="text" value="' . $this->getValue('shortname') . '">';
        $html .= '<div class="form-text">' . $L->get('Get the shortname from the Disqus general settings') . '</div>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="enableStandard">' . $L->get('Enable Disqus on standard pages') . '</label>';
        $html .= '<select class="form-select" id="enableStandard" name="enableStandard">';
        $html .= '<option value="true" ' . ($this->getValue('enableStandard') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
        $html .= '<option value="false" ' . ($this->getValue('enableStandard') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
        $html .= '</select>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="enableStatic">' . $L->get('Enable Disqus on static pages') . '</label>';
        $html .= '<select class="form-select" id="enableStatic" name="enableStatic">';
        $html .= '<option value="true" ' . ($this->getValue('enableStatic') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
        $html .= '<option value="false" ' . ($this->getValue('enableStatic') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
        $html .= '</select>';
        $html .= '</div>';


        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="enableSticky">' . $L->get('Enable Disqus on sticky pages') . '</label>';
        $html .= '<select class="form-select" id="enableSticky" name="enableSticky">';
        $html .= '<option value="true" ' . ($this->getValue('enableSticky') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
        $html .= '<option value="false" ' . ($this->getValue('enableSticky') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
        $html .= '</select>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="enableUnlisted">' . $L->get('Enable Disqus on unlisted pages') . '</label>';
        $html .= '<select class="form-select" id="enableUnlisted" name="enableUnlisted">';
        $html .= '<option value="true" ' . ($this->getValue('enableUnlisted') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
        $html .= '<option value="false" ' . ($this->getValue('enableUnlisted') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }

    public function pageEnd() {
        global $url;
        global $WHERE_AM_I;

        // Do not display Disqus on page not found
        if ($url->notFound()) {
            return false;
        }

        if ($WHERE_AM_I === 'page') {
            global $page;
            if ($page->published() && $this->getValue('enableStandard')) {
                return $this->javascript();
            }
            if ($page->isStatic() && $this->getValue('enableStatic')) {
                return $this->javascript();
            }
            if ($page->sticky() && $this->getValue('enableSticky')) {
                return $this->javascript();
            }
            if ($page->unlisted() && $this->getValue('enableUnlisted')) {
                return $this->javascript();
            }
        }

        return false;
    }

    private function javascript() {
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
