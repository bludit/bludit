<?php

class pluginCategories extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Categories',
			'showCero'=>false
		);
	}

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input type="hidden" name="showCero" value="false">';
		$html .= '<input id="jsshowCero" name="showCero" type="checkbox" value="true" '.($this->getValue('showCero')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsshowCero">'.$Language->get('Show categories without content').'</label>';
		$html .= '</div>';

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $Language;
		global $dbCategories;

		// HTML for sidebar
		$html  = '<div class="plugin plugin-categories">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// By default the database of categories are alphanumeric sorted
		foreach( $dbCategories->db as $key=>$fields ) {
			$count = count($fields['list']);
			if($this->getValue('showCero') || $count>0) {
				$html .= '<li>';
				$html .= '<a href="'.DOMAIN_CATEGORIES.$key.'">';
				$html .= $fields['name'];
				$html .= ' ('.count($fields['list']).')';
				$html .= '</a>';
				$html .= '</li>';
			}
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}