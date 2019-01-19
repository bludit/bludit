<?php

class pluginCanonical extends Plugin {

	public function siteHead()
	{
		if ($GLOBALS['WHERE_AM_I'] === 'home') {
			return '<link rel="canonical" href="'.DOMAIN_BASE.'"/>'.PHP_EOL;
		} elseif ($GLOBALS['WHERE_AM_I'] === 'page') {
			global $page;
			return '<link rel="canonical" href="'.$page->permalink().'"/>'.PHP_EOL;
		}
	}

}