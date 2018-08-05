<?php

class pluginCategories extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Categories',
			'hideCero'=>true
		);
	}

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Label').'</label>';
		$html .= '<input name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$L->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Hide Categories without content').'</label>';
		$html .= '<select name="hideCero">';
		$html .= '<option value="true" '.($this->getValue('hideCero')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('hideCero')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $L;
		global $categories;

		// HTML for sidebar
		$html  = '<div class="plugin plugin-categories">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// By default the database of categories are alphanumeric sorted
		foreach ($categories->db as $key=>$fields) {
			$count = count($fields['list']);
			if (!$this->getValue('hideCero') || $count>0) {
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