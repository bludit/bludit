<?php

class pluginHitCounter extends Plugin {

	public function init() {
		$this->dbFields = array(
			'label'=>'Hit Counter',
			'showUniqueVisitors'=>false
		);
	}

	public function form() {
		global $L;

		// Check if the plugin Visits Stats is activated
		if (!isPluginActive('pluginVisitsStats')) {
			$html  = '<div class="alert alert-warning" role="alert">';
			$html .= $L->get('This plugin depends on the following plugins.');
			$html .= '<ul class="m-0"><li>Visits Stats</li></ul>';
			$html .= '</div>';

			// Hidden form buttons. Save and Cancel buttons.
			$this->formButtons = false;
			return $html;
		}

		$html  = '<div class="mb-3">';
		$html .= '<label class="form-label" for="label">'.$L->get('Label').'</label>';
		$html .= '<input class="form-control" id="label" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<div class="form-text">'.$L->get('This title is almost always used in the sidebar of the site').'</div>';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="showUniqueVisitors">'.$L->get('Show unique visitors').'</label>';
		$html .= '<select class="form-select" id="showUniqueVisitors" name="showUniqueVisitors">';
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
		if (isPluginActive('pluginVisitsStats')) {
			// Get the object of the plugin Simple Stats
			global $plugins;
			$visitsStats = $plugins['all']['pluginVisitsStats'];
			$currentDate = Date::current('Y-m-d');

			if ($this->getValue('showUniqueVisitors')) {
				// Get the unique visitors from today
				$counter = $visitsStats->uniqueVisitors($currentDate);
			} else {
				// Get the visits from today
				$counter = $visitsStats->visits($currentDate);
			}
		}

		$html  = '<div class="plugin plugin-hit-counter">';
		$html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<div class="counter">'.$counter.'</div>';
 		$html .= '</div>';
 		$html .= '</div>';

		return $html;
	}
}