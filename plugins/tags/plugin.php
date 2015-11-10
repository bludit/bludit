<?php

class pluginTags extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'Tags'
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Plugin label').'</label>';
		$html .= '<input name="label" id="jslabel" type="text" value="'.$this->getDbField('label').'">';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		global $Language;
		global $dbTags;
		global $Url;

		$db = $dbTags->db['postsIndex'];
		$filter = $Url->filters('tag');

		$html  = '<div class="plugin plugin-tags">';
		$html .= '<h2>'.$this->getDbField('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		foreach($db as $tagKey=>$fields)
		{
			$count = $dbTags->countPostsByTag($tagKey);

			// Print the parent
			$html .= '<li><a href="'.HTML_PATH_ROOT.$filter.'/'.$tagKey.'">'.$fields['name'].' ('.$count.')</a></li>';
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}
