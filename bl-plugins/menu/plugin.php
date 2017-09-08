<?php

class pluginMenu extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Menu'
		);
	}

	// Method called on the settings of the plugin on the admin area
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

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $Language;
		global $dbCategories;
		global $Url;

		// HTML for sidebar
		$html  = '<div class="plugin plugin-menu">';

		// Print the label only if not empty
		if( $this->getValue('label') ) {
			$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		}

		$html .= '<div class="plugin-content">';
		$html .= '<ul class="menu">';

		// By default the database of categories are alphanumeric sorted
		foreach( $dbCategories->db as $key=>$fields ) {
			$pageList = $fields['list'];
			if( count($pageList) > 0 ) {
				$html .= '<li class="menu">';
				$html .= '<span class="category-name">'.$fields['name'].'</span>';
				$html .= '<ul class="submenu">';
				foreach( $pageList as $pageKey ) {
					// Create the page object from the page key
					$page = buildPage($pageKey);
					$html .= '<li class="submenu">';
					$html .= '<a href="'.$page->permalink().'">';
					$html .= $page->title();
					$html .= '</a>';
					$html .= '</li>';
				}
				$html .= '</ul>';
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