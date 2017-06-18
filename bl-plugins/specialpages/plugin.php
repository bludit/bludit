<?php

class pluginSpecialPages extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Pages',
			'homeLink'=>true,
			'pageAboutLabel'=>'About',
			'pageAbout'=>''
		);
	}

	public function post()
	{

	}

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $Language;
		global $dbPages;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input type="hidden" name="homeLink" value="false">';
		$html .= '<input id="jshomeLink" name="homeLink" type="checkbox" value="true" '.($this->getValue('homeLink')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jshomeLink">'.$Language->get('Show home link').'</label>';
		$html .= '</div>';

		$options = array();
		foreach($dbPages->db as $key=>$fields) {
			$page = buildPage($key);
			$options[$key] = $page->title();
		}

		HTML::formOpen(array('class'=>'uk-form-horizontal'));

			HTML::legend(array('value'=>$Language->g('About page')));

			HTML::formInputText(array(
				'name'=>'title',
				'label'=>$Language->g('Site title'),
				'value'=>'test',
				'class'=>'uk-width-1-2 uk-form-medium',
				'tip'=>$Language->g('use-this-field-to-name-your-site')
			));

		HTML::formClose();

		$html .= '<legend>About page</legend>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input id="jspageAboutLabel" name="pageAboutLabel" type="text" value="'.$this->getValue('pageAboutLabel').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Select a page').'</label>';
		$html .= '<select id="jspageAbout" name="pageAbout">';
		$html .= '<option value=""></option>';
		foreach($options as $key=>$title) {
			$html .= '<option value="'.$key.'" '.($this->getValue('pageAbout')==$key?'selected':'').'>'.$title.'</option>';
		}
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input type="hidden" name="pageAboutHide" value="false">';
		$html .= '<input id="jshomeLink" name="homeLink" type="checkbox" value="true" '.($this->getValue('homeLink')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jshomeLink">'.$Language->get('Show the page on the main').'</label>';
		$html .= '</div>';

		$html .= '<legend>Contact page</legend>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Label').'</label>';
		$html .= '<input id="jspageContactLabel" name="pageContactLabel" type="text" value="'.$this->getValue('pageContactLabel').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Select a page').'</label>';
		$html .= '<select id="jspageContact" name="pageContact">';
		$html .= '<option value=""></option>';
		foreach($options as $key=>$title) {
			$html .= '<option value="'.$key.'" '.($this->getValue('pageContact')==$key?'selected':'').'>'.$title.'</option>';
		}
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $Language;

		// HTML for sidebar
		$html  = '<div class="plugin plugin-special-pages">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<ul>';

		// Show Home page link
		if( $this->getValue('homeLink') ) {
			$html .= '<li>';
			$html .= '<a href="'.$Site->url().'">';
			$html .= $Language->get('Home page');
			$html .= '</a>';
			$html .= '</li>';
		}

		if( $this->getValue('pageAboutLabel') ) {
			$html .= '<li>';
			$html .= '<a href="'.$this->getValue('pageAbout').'">';
			$html .= $this->getValue('pageAboutLabel');
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}