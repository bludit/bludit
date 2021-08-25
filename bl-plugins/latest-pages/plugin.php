<?php

class pluginLatestPages extends Plugin {

	public function dashboard()
	{
		global $L;
		global $pages;

		$html = '<div class="pluginLatestPages mt-4 mb-4 pb-4 border-bottom">';
		$html .= '<h3 class="m-0 p-0 pb-3"><i class="bi bi-pencil-square"></i>Latest pages</h3>';
		$html .= '<div class="list-group">';
		$tmp = $pages->getList(1, 5);
		foreach ($tmp as $key) {
			$page = buildPage($key);
			$html .= '<a href="'.$page->permalink().'" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">';
			$html .= '<div class="d-flex gap-2 w-100 justify-content-between">';
			$html .= '<div>';
			$html .= '<h6 class="mb-0">'.($page->title() ? $page->title() : '<span class="text-muted">' . $L->g('Empty title') . '</span> ').'</h6>';
			$html .= '<p class="mb-0 opacity-75">Category: '.($page->category() ? $page->category() : $L->get('uncategorized')).'</p>';
			$html .= '</div>';
			$html .= '<small class="opacity-50 text-nowrap">'.$page->relativeTime().'</small>';
			$html .= '</div>';
			$html .= '</a>';
		}
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

}