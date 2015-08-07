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
		$html .= '<label>Plugin label</label>';
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
		global $Site;

		$html  = '<div class="plugin plugin-pages">';
		$html .= '<h2>'.$this->getDbField('label').'</h2>';
		$html .= '<div class="plugin-content">';

		$parents = $pagesParents[NO_PARENT_CHAR];

		$html .= '<ul>';

		if($this->getDbField('homeLink')) {
			$html .= '<li><a class="parent" href="'.$Site->homeLink().'">'.$Language->get('Home').'</a></li>';
		}

		foreach($parents as $parent)
		{
			// Print the parent
			$html .= '<li><a class="parent" href="'.$parent->permalink().'">'.$parent->title().'</a></li>';

			// Check if the parent hash children
			if(isset($pagesParents[$parent->key()]))
			{
				$children = $pagesParents[$parent->key()];

				// Print the children
				$html .= '<li><ul>';
				foreach($children as $child)
				{
					$html .= '<li><a class="children" href="'.$child->permalink().'">— '.$child->title().'</a></li>';
				}
				$html .= '</ul></li>';
			}
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}
