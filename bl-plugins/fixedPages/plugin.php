<?php

class pluginFixedPages extends Plugin {

	public function init()
	{
		// JSON database
		$jsondb = json_encode(array(
			'about'=>'About'
		));

		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Fixed Pages',
			'jsondb'=>$jsondb
		);

		// Disable default Save and Cancel button
		$this->formButtons = false;
	}

	// Method called when a POST request is sent
	public function post()
	{
		global $dbPages;

		// Get current jsondb value from database
		// All data stored in the database is html encoded
		$jsondb = $this->db['jsondb'];
		$jsondb = Sanitize::htmlDecode($jsondb);

		// Convert JSON to Array
		$pagesFixed = json_decode($jsondb, true);

		// Check if the user click on the button delete or add
		if( isset($_POST['delete']) ) {
			// Values from $_POST
			$pageKey = $_POST['delete'];

			// Change the status of the page from fixed to published
			$dbPages->setStatus($pageKey, 'published');

			// Delete the link from the array
			unset($pagesFixed[$pageKey]);
		}
		elseif( isset($_POST['add']) ) {
			// Values from $_POST
			$pageTitle = $_POST['newPageTitle'];
			$pageKey = $_POST['newPageKey'];

			// Change the status of the page from fixed to published
			$dbPages->setStatus($pageKey, 'fixed');

			// Add the link
			$pagesFixed[$pageKey] = $pageTitle;
		}

		// Encode html to store the values on the database
		$this->db['label'] = Sanitize::html($_POST['label']);
		$this->db['jsondb'] = Sanitize::html(json_encode($pagesFixed));

		// Save the database
		return $this->save();
	}

	// Method called on plugin settings on the admin area
	public function form()
	{
		global $Language;
		global $dbPages;

		$options = array();
		foreach($dbPages->db as $key=>$fields) {
			$page = buildPage($key);
			if($page->published()) {
				$options[$key] = $page->title();
			}
		}

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$Language->get('Title of the plugin for the sidebar').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<button name="save" class="blue" type="submit">Save</button>';
		$html .= '</div>';

		// NEW PAGE
		$html .= '<legend>'.$Language->get('New fixed page').'</legend>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Title').'</label>';
		$html .= '<input name="newPageTitle" type="text" value="">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Page').'</label>';
		$html .= '<select name="newPageKey">';
		foreach($options as $key=>$title) {
			$html .= '<option value="'.$key.'">'.$title.'</option>';
		}
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<button name="add" class="blue" type="submit">Add</button>';
		$html .= '</div>';

		// LIST OF PAGES
		$html .= '<legend>'.$Language->get('Fixed pages').'</legend>';

		$jsondb = $this->getValue('jsondb', $unsanitized=false);
		$pagesFixed = json_decode($jsondb, true);
		foreach($pagesFixed as $pageKey=>$pageTitle) {
			$html .= '<div>';
			$html .= '<label>'.$Language->get('Title').'</label>';
			$html .= '<input type="text" value="'.$pageTitle.'" disabled>';
			$html .= '</div>';

			$page = buildPage($pageKey);
			if($page) {
				$title = $page->title();
			} else {
				$title = $Language->get('Error page deleted');
			}

			$html .= '<div>';
			$html .= '<label>'.$Language->get('Page linked').'</label>';
			$html .= '<input type="text" value="'.$title.'" disabled>';
			$html .= '</div>';

			$html .= '<div>';
			$html .= '<button name="delete" type="submit" value="'.$pageKey.'">Delete</button>';
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
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// Get the JSON DB, getValue() with the option unsanitized HTML code
		$jsondb = $this->getValue('jsondb', false);
		$pagesFixed = json_decode($jsondb);

		// By default the database of categories are alphanumeric sorted
		foreach($pagesFixed as $key=>$title) {
			$html .= '<li>';
			$html .= '<a href="'.DOMAIN_PAGES.$key.'">';
			$html .= $title;
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}