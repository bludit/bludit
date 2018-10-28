<?php

class pluginHitCounter extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'Hit Counter',
			'showUniqueVisitors'=>false
		);
	}

	public function form()
	{
		global $L;

		// Check if the plugin Simple Stats is activated
		if (!pluginActivated('pluginSimpleStats')) {
			// Show an alert about the dependency of the plugin
			$html  = '<div class="alert alert-warning" role="alert">';
			$html .= $L->get('This plugin depends on the following plugins.');
			$html .= '<ul class="m-0"><li>Simple Stats</li></ul>';
			$html .= '</div>';

			// Hidden form buttons. Save and Cancel buttons.
			$this->formButtons = false;
			return $html;
		}

		// Show the description of the plugin in the settings
		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		// Label of the plugin to show in the sidebar
		$html .= '<div>';
		$html .= '<label>'.$L->get('Label').'</label>';
		$html .= '<input name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$L->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		// Form "select" element for enable or disable Unique visitors properties
		$html .= '<div>';
		$html .= '<label>'.$L->get('Show unique visitors').'</label>';
		$html .= '<select name="showUniqueVisitors">';
		$html .= '<option value="true" '.($this->getValue('showUniqueVisitors')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('showUniqueVisitors')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		// Init counter to 0
		$counter = 0;

		// Check if the plugin Simple Stats is activated
		if (pluginActivated('pluginSimpleStats')) {
			// Get the object of the plugin Simple Stats
			global $plugins;
			$simpleStats = $plugins['all']['pluginSimpleStats'];
			$currentDate = Date::current('Y-m-d');

			if ($this->getValue('showUniqueVisitors')) {
				// Get the unique visitors from today
				$counter = $simpleStats->uniqueVisitors($currentDate);
			} else {
				// Get the visits from today
				$counter = $simpleStats->visits($currentDate);
			}
		}

		// Show in the sidebar the number of visitors
		$html  = '<div class="plugin plugin-hit-counter">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<div class="counter">'.$counter.'</div>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}