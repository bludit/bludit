<?php

class pluginLinks extends Plugin {

	public function init()
	{
		// JSON database
		$jsondb = json_encode(array(
			'Bludit'=>'https://www.bludit.com',
			'Bludit PRO'=>'https://pro.bludit.com'
		));

		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Links',
			'jsondb'=>$jsondb
		);

		// Disable default Save and Cancel button
		$this->formButtons = false;
	}

	// Method called when a POST request is sent
	public function post()
	{
		// Get current jsondb value from database
		// All data stored in the database is html encoded
		$jsondb = $this->db['jsondb'];
		$jsondb = Sanitize::htmlDecode($jsondb);

		// Convert JSON to Array
		$links = json_decode($jsondb, true);

		// Check if the user click on the button delete or add
		if( isset($_POST['deleteLink']) ) {
			// Values from $_POST
			$name = $_POST['deleteLink'];

			// Delete the link from the array
			unset($links[$name]);
		}
		elseif( isset($_POST['addLink']) ) {
			// Values from $_POST
			$name = $_POST['linkName'];
			$url = $_POST['linkURL'];

			// Check empty string
			if( empty($name) ) { return false; }

			// Add the link
			$links[$name] = $url;
		}

		// Encode html to store the values on the database
		$this->db['label'] = Sanitize::html($_POST['label']);
		$this->db['jsondb'] = Sanitize::html(json_encode($links));

		// Save the database
		return $this->save();
	}

	// Method called on plugin settings on the admin area
	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$Language->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<button name="save" class="blue" type="submit">'.$Language->get('Save').'</button>';
		$html .= '</div>';

		// New link, when the user click on save button this call the method post()
		// and the new link is added to the database
		$html .= '<legend>'.$Language->get('Add a new link').'</legend>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Name').'</label>';
		$html .= '<input name="linkName" type="text" value="">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Url').'</label>';
		$html .= '<input name="linkURL" type="text" value="">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<button name="addLink" class="blue" type="submit">'.$Language->get('Add').'</button>';
		$html .= '</div>';

		$html .= '<legend>'.$Language->get('Links').'</legend>';

		// Get the JSON DB, getValue() with the option unsanitized HTML code
		$jsondb = $this->getValue('jsondb', $unsanitized=false);
		$links = json_decode($jsondb, true);
		foreach($links as $name=>$url) {
			$html .= '<div>';
			$html .= '<label>'.$Language->get('Name').'</label>';
			$html .= '<input type="text" value="'.$name.'" disabled>';
			$html .= '</div>';

			$html .= '<div>';
			$html .= '<label>'.$Language->get('Url').'</label>';
			$html .= '<input type="text" value="'.$url.'" disabled>';
			$html .= '</div>';

			$html .= '<div>';
			$html .= '<button name="deleteLink" type="submit" value="'.$name.'">'.$Language->get('Delete').'</button>';
			$html .= '</div>';

			$html .= '</br>';
		}

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $Language;

		// HTML for sidebar
		$html  = '<div class="plugin plugin-pages">';
		if ($this->getValue('label')) {
			$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		}
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