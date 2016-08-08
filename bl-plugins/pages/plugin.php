<?php

class pluginPages extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'homeLink'=>true,
			'label'=>'Pages'
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Plugin label').'</label>';
		$html .= '<input name="label" id="jslabel" type="text" value="'.$this->getDbField('label').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input name="homeLink" id="jshomeLink" type="checkbox" value="true" '.($this->getDbField('homeLink')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jshomeLink">'.$Language->get('Show home link').'</label>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		global $Language;
		global $pagesParents;
		global $Site, $Url;

		$html  = '<div class="plugin plugin-pages">';

		// Print the label if not empty.
		$label = $this->getDbField('label');
		if( !empty($label) ) {
			$html .= '<h2>'.$label.'</h2>';
		}

		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// Show home link ?
		if($this->getDbField('homeLink')) {
			$html .= '<li>';
			$html .= '<a class="parent'.( ($Url->whereAmI()=='home')?' active':'').'" href="'.$Site->homeLink().'">'.$Language->get('Home').'</a>';
			$html .= '</li>';
		}

		$parents = $pagesParents[NO_PARENT_CHAR];
		foreach($parents as $parent)
		{
			// Check if the parent is published
			if( $parent->published() )
			{
				// Print the parent
				$html .= '<li>';
				$html .= '<a class="parent '.( ($parent->key()==$Url->slug())?' active':'').'" href="'.$parent->permalink().'">'.$parent->title().'</a>';

				// Check if the parent has children
				if(isset($pagesParents[$parent->key()]))
				{
					$children = $pagesParents[$parent->key()];

					// Print children
					$html .= '<ul class="children">';
					foreach($children as $child)
					{
						// Check if the child is published
						if( $child->published() )
						{
							$html .= '<li class="child">';
							$html .= '<a class="'.( ($child->key()==$Url->slug())?' active':'').'" href="'.$child->permalink().'">'.$child->title().'</a>';
							$html .= '</li>';
						}
					}
					$html .= '</ul>';
				}
			}
		}

		$html .= '</li></ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}