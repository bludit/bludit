<?php

class pluginNavigation extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Navigation',
			'homeLink'=>true,
			'amountOfItems'=>5
		);
	}

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $language;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$language->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$language->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$language->get('Home link').'</label>';
		$html .= '<select name="homeLink">';
		$html .= '<option value="true" '.($this->getValue('homeLink')===true?'selected':'').'>'.$language->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('homeLink')===false?'selected':'').'>'.$language->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">'.$language->get('Show the home link on the sidebar').'</span>';
		$html .= '</div>';

		if (ORDER_BY=='date') {
			$html .= '<div>';
			$html .= '<label>'.$language->get('Amount of items').'</label>';
			$html .= '<input id="jsamountOfItems" name="amountOfItems" type="text" value="'.$this->getValue('amountOfItems').'">';
			$html .= '</div>';
		}

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $language;
		global $url;
		global $site;
		global $pages;

		// HTML for sidebar
		$html  = '<div class="plugin plugin-navigation">';

		// Print the label if not empty
		$label = $this->getValue('label');
		if (!empty($label)) {
			$html .= '<h2 class="plugin-label">'.$label.'</h2>';
		}

		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// Show Home page link
		if ($this->getValue('homeLink')) {
			$html .= '<li>';
			$html .= '<a href="' . $site->url() . '">' . $language->get('Home page') . '</a>';
			$html .= '</li>';
		}

		// Pages order by position
		if (ORDER_BY=='position') {
			// Get parents
			$parents = buildParentPages();
			foreach ($parents as $parent) {
				$html .= '<li class="parent">';
				$html .= '<a href="' . $parent->permalink() . '">' . $parent->title() . '</a>';

				if ($parent->hasChildren()) {
					// Get children
					$children = $parent->children();
					$html .= '<ul class="child">';
					foreach ($children as $child) {
						$html .= '<li class="child">';
						$html .= '<a class="child" href="' . $child->permalink() . '">' . $child->title() . '</a>';
						$html .= '</li>';
					}
					$html .= '</ul>';
				}
				$html .= '</li>';
			}
		}
		// Pages order by date
		else {
			// List of published pages
			$onlyPublished = true;
			$pageNumber = 1;
			$amountOfItems = $this->getValue('amountOfItems');
			$publishedPages = $pages->getList($pageNumber, $amountOfItems, $onlyPublished);

			foreach ($publishedPages as $pageKey) {
				try {
					$page = new Page($pageKey);
					$html .= '<li>';
					$html .= '<a href="' . $page->permalink() . '">' . $page->title() . '</a>';
					$html .= '</li>';
				} catch (Exception $e) {
					// Continue
				}
			}
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}