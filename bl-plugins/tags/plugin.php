<?php

class pluginTags extends Plugin
{

	public function init()
	{
		$this->dbFields = array(
			'label' => 'Tags',
			'tagCloud' => true
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Label') . '</label>';
		$html .= '<input id="jslabel" name="label" type="text" dir="auto" value="' . $this->getValue('label') . '">';
		$html .= '<span class="tip">' . $L->get('This title is almost always used in the sidebar of the site') . '</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Tag cloud') . '</label>';
		$html .= '<select name="tagCloud">';
		$html .= '<option value="true" ' . ($this->getValue('tagCloud') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('tagCloud') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '<span class="tip">' . $L->get('Tags with more pages are displayed bigger') . '</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		global $L;
		global $tags;
		global $url;

		$filter = $url->filters('tag');
		$tagCloud = $this->getValue('tagCloud');

		// Tag cloud, font size scaled between minSize and maxSize (em) by the amount of pages
		$minSize = 0.8;
		$maxSize = 1.8;
		if ($tagCloud && !empty($tags->db)) {
			$counts = array_map(function ($fields) {
				return count($fields['list']);
			}, $tags->db);
			$minCount = min($counts);
			$maxCount = max($counts);
		}

		$html  = '<div class="plugin plugin-tags">';
		$html .= '<h2 class="plugin-label">' . $this->getValue('label') . '</h2>';
		$html .= '<div class="plugin-content">';
		$html .= $tagCloud ? '<ul class="tag-cloud" style="list-style:none;padding-left:0">' : '<ul>';

		// By default, the database of tags is alphanumeric sorted
		foreach ($tags->db as $key => $fields) {
			if ($tagCloud) {
				$size = 1;
				if ($maxCount > $minCount) {
					$size = $minSize + (count($fields['list']) - $minCount) * ($maxSize - $minSize) / ($maxCount - $minCount);
				}
				$html .= '<li style="display:inline-block;margin-right:0.5em;font-size:' . round($size, 2) . 'em">';
			} else {
				$html .= '<li>';
			}
			$html .= '<a href="' . DOMAIN_TAGS . $key . '">';
			$html .= $fields['name'];
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
}
