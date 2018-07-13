<?php

class pluginRobots extends Plugin {

	public function siteHead()
	{
		global $WHERE_AM_I;
		global $page;

		$html = PHP_EOL.'<!-- Robots plugin -->'.PHP_EOL;

		if ($WHERE_AM_I=='page') {
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
				$robots = implode(' ', $robots);
				$html .= '<meta name="robots" content="'.$robots.'">'.PHP_EOL;
			}
		}

		return $html;
	}

}