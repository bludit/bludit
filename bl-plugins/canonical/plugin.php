<?php

class pluginCanonical extends Plugin {

	public function siteHead() {

		$html = '<!-- Plugin Canonical -->' . PHP_EOL;

		if ($GLOBALS['WHERE_AM_I'] === 'home')
		{
			$html .= '<link rel="canonical" href="'.DOMAIN_BASE.'">'.PHP_EOL;

		} elseif ($GLOBALS['WHERE_AM_I'] === 'page')
		{
			global $page;
			$html .= '<link rel="canonical" href="'.$page->permalink().'">'.PHP_EOL;

		} elseif ($GLOBALS['WHERE_AM_I'] === 'category')
		{
			global $url;
			$categoryKey = $url->slug();
			$category = new Category( $categoryKey );

			$html .= '<link rel="canonical" href="' . $category->permalink() . '">'.PHP_EOL;

		} elseif ($GLOBALS['WHERE_AM_I'] === 'tag')
		{
			global $url;
			$tagKey = $url->slug();
			$tag = new Tag( $tagKey );

			$html .= '<link rel="canonical" href="'.$tag->permalink().'">'.PHP_EOL;

		}
		$html .= '<!-- /Plugin Canonical -->' . PHP_EOL;
		return $html;
	}

	public function form()
	{
		global $L;

		return '<div class="alert alert-info">' . $L->g('This plugin has no settings') . '</div>';
	}
}
