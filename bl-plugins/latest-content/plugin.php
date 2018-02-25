<?php

class pluginLatestContent extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Latest content',
			'homeLink'=>true,
			'amountOfItems'=>5
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

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Home link').'</label>';
		$html .= '<select name="homeLink">';
		$html .= '<option value="true" '.($this->getValue('homeLink')===true?'selected':'').'>'.$Language->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('homeLink')===false?'selected':'').'>'.$Language->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">'.$Language->get('Show the home link on the sidebar').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Amount of items').'</label>';
		$html .= '<input id="jsamountOfItems" name="amountOfItems" type="text" value="'.$this->getValue('amountOfItems').'">';
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
		global $pagesByParent;

		// Amount of pages to show
		$amountOfItems = $this->getValue('amountOfItems');

		// Page number the first one
		$pageNumber = 1;

		// Only published pages
		$onlyPublished = true;

		// Get the list of pages
		$pages = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished, true);

		// HTML for sidebar
		$html  = '<div class="plugin plugin-pages">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		if(ORDER_BY==='position') {
			foreach($pagesByParent[PARENT] as $Parent) {
				$html .= '<li class="parent">';
				$html .= '<h3>';
				$html .= $Parent->title();
				$html .= '</h3>';

				if(!empty($pagesByParent[$Parent->key()])) {
					$html .= '<ul class="child">';
					foreach($pagesByParent[$Parent->key()] as $child) {
						$html .= '<li class="child">';
						$html .= '<a class="child" href="'.$child->permalink().'">';
						$html .= $child->title();
						$html .= '</a>';
						$html .= '</li>';
					}
					$html .= '</ul>';
				}
				$html .= '</li>';
			}
		}
		else {
			// Show Home page link
			if( $this->getValue('homeLink') ) {
				$html .= '<li>';
				$html .= '<a href="'.$Site->url().'">';
				$html .= $Language->get('Home page');
				$html .= '</a>';
				$html .= '</li>';
			}

			// Get keys of pages
			foreach($pages as $pageKey) {
				// Create the page object from the page key
				$page = buildPage($pageKey);
				$html .= '<li>';
				$html .= '<a href="'.$page->permalink().'">';
				$html .= $page->title();
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