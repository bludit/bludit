<?php

class pluginTags extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'Tags',
			'sort'=>'date',
			'link'=>''
		);
                $this->dbTokens = array(
                        "[postUrl]",
                        "[tagName]",
                        "[tagCount]"
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
		$html .= $Language->get('Sort the tag list by').': <select name="sort">';

		foreach(array('alpha' => 'Alphabetical order',
		              'count' => 'Number of times each tag has been used',
		              'date'  => 'Date each tag was first used') as $key=>$value) {
			if ($key == $this->getDbField('sort')) {
				$html .= '<option value="'.$key.'" selected>'.$Language->get($value).'</option>';
			} else {
				$html .= '<option value="'.$key.'">'.$Language->get($value).'</option>';
			}
		}
                $html .= '</select>';
		$html .= '</div>';
                
                $html .= '<div>';
		$html .= '<label>'.$Language->get('Customize link').'</label>';
		$html .= '<input name="link" id="jslink" type="text" value="'.$this->getDbField('link').'">';
                $html .= '<pre>available tokens '.  implode(', ', $this->dbTokens).' <br/>'.
                          htmlspecialchars('<a href="[token1]#content" >[token2] [token3]</a>').'</pre>';
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
                        $link='<a href="'.HTML_PATH_ROOT.$filter.'/'.$fields['tagKey'].'">'.$fields['name'].' ('.$fields['count'].')</a>';
                        if(!empty($this->getDbField('link'))){
                            $replacments=array(
                                "[postUrl]"=>HTML_PATH_ROOT.$filter.'/'.$fields['tagKey'], html_entity_decode($this->getDbField('link')),
                                "[tagName]"=>$fields['name'],
                                "[tagCount]"=>$fields['count']
                            );
                            $link=  html_entity_decode($this->getDbField('link'));
                            foreach($this->dbTokens as $token){
                                $link=  str_replace($token,$replacments[$token],$link);
                            }
                        }
			$html .= "<li>$link</li>";
		}
		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}