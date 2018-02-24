<?php

class pluginNavigation extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Navigation',
			'homeLink'=>true,
			'amountOfItems'=>5,
			'staticPages'=>true,
			'pages'=>true
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
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Show static pages').'</label>';
		$html .= '<select name="staticPages">';
		$html .= '<option value="true" '.($this->getValue('staticPages')===true?'selected':'').'>'.$Language->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('staticPages')===false?'selected':'').'>'.$Language->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Show pages').'</label>';
		$html .= '<select name="pages">';
		$html .= '<option value="true" '.($this->getValue('pages')===true?'selected':'').'>'.$Language->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('pages')===false?'selected':'').'>'.$Language->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		if (ORDER_BY=='date') {
			$html .= '<div>';
			$html .= '<label>'.$Language->get('Amount of items').'</label>';
			$html .= '<input id="jsamountOfItems" name="amountOfItems" type="text" value="'.$this->getValue('amountOfItems').'">';
			$html .= '</div>';
		}

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $Language;
		global $Url;
		global $Site;
		global $dbPages;

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
			$html .= '<a href="' . $Site->url() . '">' . $Language->get('Home page') . '</a>';
			$html .= '</li>';
		}

		// Show static pages
		if ($this->getValue('staticPages')) {
			$staticPages = buildStaticPages();
			foreach ($staticPages as $page) {
				$html .= '<li>';
				$html .= '<a href="' . $page->permalink() . '">' . $page->title() . '</a>';
				$html .= '</li>';
			}
		}

		// Show pages
		if ($this->getValue('pages')) {
			if (ORDER_BY=='position') {
				// Get parents
				$parents = buildParentPages();
				foreach ($parents as $parent) {
					$html .= '<li class="parent">';
					$html .= '<b><a href="' . $parent->permalink() . '">' . $parent->title() . '</a></b>';

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
			} else {
				// List of published pages
				$onlyPublished = true;
				$pageNumber = 1;
				$amountOfItems = $this->getValue('amountOfItems');
				$publishedPages = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);

				foreach ($publishedPages as $pageKey) {
					$page = buildPage($pageKey);
					$html .= '<li>';
					$html .= '<a href="' . $page->permalink() . '">' . $page->title() . '</a>';
					$html .= '</li>';
				}
			}
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}