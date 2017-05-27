<?php

class pluginPages extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Pages',
			'homeLink'=>true,
			'children'=>true
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
		$html .= '<input type="hidden" name="homeLink" value="0">';
		$html .= '<input id="jshomeLink" name="homeLink" type="checkbox" value="true" '.($this->getValue('homeLink')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jshomeLink">'.$Language->get('Show home link').'</label>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input type="hidden" name="children" value="0">';
		$html .= '<input id="jschildren" name="children" type="checkbox" value="true" '.($this->getValue('children')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jschildren">'.$Language->get('Show children').'</label>';
		$html .= '</div>';

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $Language;
		global $pages;
		global $Url;

		// URL base filter for categories
		$filter = $Url->filters('page');

		// HTML for sidebar
		$html  = '<div class="plugin plugin-pages">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// By default the database of categories are alphanumeric sorted
		foreach( $pages as $page ) {
			$html .= '<li>';
			$html .= '<a href="'.DOMAIN_BASE.$filter.'/'.$page->key().'">';
			$html .= $page->title();
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}