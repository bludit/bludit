<?php

class pluginFixedPages extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Fixed Pages',
			'homeLink'=>true
		);
	}

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$Language->get('Title of the plugin for the sidebar').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Home link').'</label>';
		$html .= '<select name="homeLink">';
		$html .= '<option value="true" '.($this->getValue('showCero')?'checked':'').'>Enabled</option>';
		$html .= '<option value="false" '.($this->getValue('showCero')?'checked':'').'>Disabled</option>';
		$html .= '</select>';
		$html .= '<span class="tip">'.$Language->get('Show the home link on the sidebar').'</span>';
		$html .= '</div>';

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $Language;
		global $Url;
		global $Site;
		global $dbPages;

		$pages = $dbPages->getFixedDB();

		// HTML for sidebar
		$html  = '<div class="plugin plugin-pages">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// Show Home page link
		if( $this->getValue('homeLink') ) {
			$html .= '<li>';
			$html .= '<a href="'.$Site->url().'">';
			$html .= $Language->get('Home page');
			$html .= '</a>';
			$html .= '</li>';
		}

		// Get keys of pages
		$keys = array_keys($pages);
		foreach($keys as $pageKey) {
			// Create the page object from the page key
			$page = buildPage($pageKey);
			$html .= '<li>';
			$html .= '<a href="'.$page->permalink().'">';
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