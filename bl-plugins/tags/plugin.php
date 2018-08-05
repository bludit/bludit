<?php

class pluginTags extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'Tags'
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$L->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		global $L;
		global $tags;
		global $url;

		$filter = $url->filters('tag');

		$html  = '<div class="plugin plugin-tags">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// By default the database of tags are alphanumeric sorted
		foreach( $tags->db as $key=>$fields ) {
			$html .= '<li>';
			$html .= '<a href="'.DOMAIN_TAGS.$key.'">';
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