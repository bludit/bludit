<?php

class pluginPages extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'test'=>''
		);
	}

	public function onSiteSidebar()
	{
		global $Language;
		global $pagesParents;

		$html  = '<div class="plugin plugin-pages">';
		$html .= '<h2>'.$Language->get('Pages').'</h2>';
		$html .= '<div class="plugin-content">';

		$parents = $pagesParents[NO_PARENT_CHAR];

		$html .= '<ul>';

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
