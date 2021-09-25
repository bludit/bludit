<?php

class pluginRobots extends Plugin {

	public function init() {
		$this->dbFields = array(
			'robotstxt'=>'User-agent: *'.PHP_EOL.'Allow: /'
		);
	}

	public function form() {
        global $L;

        $html  = '<div class="mb-3">';
        $html .= '<label class="form-label" for="robotstxt">'.$L->get('Configure robots.txt file').'</label>';
        $html .= '<textarea class="form-control" rows="3" name="robotstxt" id="robotstxt">'.$this->getValue('robotstxt').'</textarea>';
        $html .= '<div class="form-text">'.$L->get('This plugin generates the file').' '.DOMAIN_BASE.'/robots.txt</div>';
        $html .= '</div>';

		return $html;
	}

	public function siteHead() {
		global $WHERE_AM_I;

		$html = PHP_EOL.'<!-- Robots plugin -->'.PHP_EOL;
		if ($WHERE_AM_I=='page') {
			global $page;
			$robots = array();

			if ($page->noindex()) {
				$robots['noindex'] = 'noindex';
			}

			if ($page->nofollow()) {
				$robots['nofollow'] = 'nofollow';
			}

			if ($page->noarchive()) {
				$robots['noarchive'] = 'noarchive';
			}

			if (!empty($robots)) {
				$robots = implode(',', $robots);
				$html .= '<meta name="robots" content="'.$robots.'">'.PHP_EOL;
			}
		}

		return $html;
	}

	public function beforeAll() {
		$webhook = 'robots.txt';
		if ($this->webhook($webhook)) {
			header('Content-type: text/plain');
			// Include link to sitemap in robots.txt if the plugin is enabled
			if (isPluginActive('pluginSitemap')) {
				echo 'Sitemap: '.DOMAIN_BASE.'sitemap.xml'.PHP_EOL;
			}
			echo $this->getValue('robotstxt');
			exit(0);
		}
	}

}
