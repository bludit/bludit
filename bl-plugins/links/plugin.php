<?php

class pluginLinks extends Plugin {

	public function init()
	{
		$jsondb = json_encode(array(
			'Bludit'=>'https://bludit.com',
			'Donate'=>'https://paypal.me/bludit'
		));

		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Links',
			'jsondb'=>$jsondb
		);
	}

	public function post()
	{
		$jsondb = $this->getValue('jsondb', $unsanitized=false);
		$links = json_decode($jsondb, true);

		$name = $_POST['linkName'];
		$url = $_POST['linkURL'];

		$links[$url] = $name;

		$this->db['jsondb'] = json_encode($links);
		$this->save();
	}

	// Method called on plugin settings on the admin area
	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '</div>';

		// Get the JSON DB, getValue() with the option unsanitized HTML code
		$jsondb = $this->getValue('jsondb', $unsanitized=false);
		$links = json_decode($jsondb, true);
		foreach($links as $name=>$url) {
			$html .= '<div>';
			$html .= '<input name="'.$name.'" type="text" value="'.$name.'">';
			$html .= '<input name="'.$url.'" type="text" value="'.$url.'">';
			$html .= '</div>';
		}

		$html .= '<div>';
		$html .= 'Nombre <input name="linkName" type="text" value="">';
		$html .= '<br>URL <input name="linkURL" type="text" value="">';
		$html .= '</div>';

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $Language;

		// HTML for sidebar
		$html  = '<div class="plugin plugin-pages">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// Get the JSON DB, getValue() with the option unsanitized HTML code
		$jsondb = $this->getValue('jsondb', false);
		$links = json_decode($jsondb);

		// By default the database of categories are alphanumeric sorted
		foreach( $links as $name=>$url ) {
			$html .= '<li>';
			$html .= '<a href="'.$url.'">';
			$html .= $name;
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}