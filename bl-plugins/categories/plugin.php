<?php
class pluginCategories extends Plugin {

	public function init() {
		$this->dbFields = array(
			'label'=>'Categories',
			'hideCero'=>true
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
		$html .= '<label class="form-label" for="hideCero">'.$L->get('Hide Categories without content').'</label>';
		$html .= '<select class="form-select" id="hideCero" name="hideCero">';
		$html .= '<option value="true" '.($this->getValue('hideCero')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('hideCero')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar() {
		global $categories;

		$html  = '<div class="plugin plugin-categories">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// By default the categories database is alphanumeric sorted
		foreach ($categories->db as $key=>$fields) {
			$count = count($fields['list']);
			if (!$this->getValue('hideCero') || $count>0) {
				$html .= '<li>';
				$html .= '<a href="'.DOMAIN_CATEGORIES.$key.'">';
				$html .= $fields['name'];
				$html .= ' ('.count($fields['list']).')';
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