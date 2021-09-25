<?php

class pluginNavigation extends Plugin {

	public function init() {
		$this->dbFields = array(
			'label'=>'Navigation',
			'homeLink'=>true,
			'numberOfItems'=>5
		);
	}

	public function form() {
		global $L;

        $html  = '<div class="mb-3">';
        $html .= '<label class="form-label" for="label">'.$L->get('Label').'</label>';
        $html .= '<input class="form-control" id="label" name="label" type="text" value="'.$this->getValue('label').'">';
        $html .= '<div class="form-text">'.$L->get('This title is almost always used in the sidebar of the site').'</div>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="homeLink">'.$L->get('Home link').'</label>';
        $html .= '<select class="form-select" id="homeLink" name="homeLink">';
        $html .= '<option value="true" '.($this->getValue('homeLink')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
        $html .= '<option value="false" '.($this->getValue('homeLink')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
        $html .= '</select>';
        $html .= '<div class="form-text">'.$L->get('Show the home link on the sidebar').'</div>';
        $html .= '</div>';

		if (ORDER_BY=='date') {
            $html .= '<div class="mb-3">';
            $html .= '<label class="form-label" for="numberOfItems">'.$L->get('Amount of items').'</label>';
            $html .= '<input class="form-control" id="numberOfItems" name="numberOfItems" type="text" value="'.$this->getValue('numberOfItems').'">';
            $html .= '</div>';
		}

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $L;
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
			$html .= '<a href="' . $site->url() . '">' . $L->get('Home page') . '</a>';
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
			$numberOfItems = $this->getValue('numberOfItems');
			$publishedPages = $pages->getList($pageNumber, $numberOfItems, $onlyPublished);

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