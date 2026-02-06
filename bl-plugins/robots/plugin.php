<?php

class pluginRobots extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'robotstxt'=>'User-agent: *'.PHP_EOL.'Allow: /'
		);
	}

	public function form()
	{
		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.DOMAIN.'/robots.txt</label>';
		$html .= '<textarea name="robotstxt" id="jsrobotstxt">'.$this->getValue('robotstxt').'</textarea>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
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

	public function beforeAll()
	{
		$webhook = 'robots.txt';
		if ($this->webhook($webhook)) {
			header('Content-type: text/plain');
			// Include link to sitemap in robots.txt if the plugin is enabled
			if (pluginActivated('pluginSitemap')) {
				echo 'Sitemap: '.DOMAIN_BASE.'sitemap.xml'.PHP_EOL;
			}
			echo $this->getValue('robotstxt');
			exit(0);
		}
	}

}
