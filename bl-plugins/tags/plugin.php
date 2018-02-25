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
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$Language->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		global $Language;
		global $dbTags;
		global $Url;

		$filter = $Url->filters('tag');

		$html  = '<div class="plugin plugin-tags">';
		$html .= '<h2 class="plugin-label">'.$this->getDbField('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// By default the database of tags are alphanumeric sorted
		foreach( $dbTags->db as $key=>$fields ) {
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