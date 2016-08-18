<?php

class pluginLatestPosts extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'Latest posts',
			'amount'=>5
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
		$html .= '<label>'.$Language->get('Amount of posts').'</label>';
		$html .= '<input name="amount" id="jsamount" type="text" value="'.$this->getDbField('amount').'">';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		// This function is declared in 70.posts.php
		$posts = buildPostsForPage(0, $this->getDbField('amount'), true, false);

		$html  = '<div class="plugin plugin-latest-posts">';

		// Print the label if not empty.
		$label = $this->getDbField('label');
		if( !empty($label) ) {
			$html .= '<h2 class="plugin-title">'.$label.'</h2>';
		}

		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		foreach($posts as $Post)
		{
			$html .= '<li>';
			$html .= '<a href="'.$Post->permalink().'">'.$Post->title().'</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}