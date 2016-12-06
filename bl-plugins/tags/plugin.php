<?php

class pluginTags extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'Tags',
			'sort'=>'date'
		);
	}

	public function form()
	{
		$html  = '<div>';
		$html .= '<label>'.$this->L('Plugin label').'</label>';
		$html .= '<input name="label" id="jslabel" type="text" value="'.$this->getDbField('label').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= $this->L('Sort the tag list by').': <select name="sort">';

		foreach(array('alpha' => 'Alphabetical order',
		              'count' => 'Number of times each tag has been used',
		              'date'  => 'Date each tag was first used') as $key=>$value) {
			if ($key == $this->getDbField('sort')) {
				$html .= '<option value="'.$key.'" selected>'.$this->L($value).'</option>';
			} else {
				$html .= '<option value="'.$key.'">'.$this->L($value).'</option>';
			}
		}
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		global $dbTags;
		global $Url;

		$db = $dbTags->db['postsIndex'];
		$filter = $Url->filters('tag');

		$html  = '<div class="plugin plugin-tags">';
		$html .= '<h2>'.$this->getDbField('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		$tagArray = array();

		foreach($db as $tagKey=>$fields)
		{
			$tagArray[] = array('tagKey'=>$tagKey, 'count'=>$dbTags->countPostsByTag($tagKey), 'name'=>$fields['name']);
		}

		// Sort the array based on options
		if ($this->getDbField('sort') == "count")
		{
			usort($tagArray, function($a, $b) {
				return $b['count'] - $a['count'];
			});
		}
		elseif ($this->getDbField('sort') == "alpha")
		{
			usort($tagArray, function($a, $b) {
				return strcmp($a['tagKey'], $b['tagKey']);
			});
		}

		foreach($tagArray as $tagKey=>$fields)
		{
			// Print the parent
			$html .= '<li><a href="'.HTML_PATH_ROOT.$filter.'/'.$fields['tagKey'].'">'.$fields['name'].' ('.$fields['count'].')</a></li>';
		}
		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}