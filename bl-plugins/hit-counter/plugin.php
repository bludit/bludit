<?php

class pluginHitCounter extends Plugin
{

	public function init()
	{
		$this->dbFields = array(
			'label' => 'Hit Counter',
			'showUniqueVisitors' => false
		);
	}

	public function form()
	{
		global $L;

		// Check if the plugin Visits Stats is activated
		if (!pluginActivated('pluginVisitsStats')) {
			$html  = '<div class="alert alert-warning" role="alert">';
			$html .= $L->get('This plugin depends on the following plugins.');
			$html .= '<ul class="m-0"><li>Visits Stats</li></ul>';
			$html .= '</div>';

			$this->formButtons = false;
			return $html;
		}

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Label') . '</label>';
		$html .= '<input name="label" type="text" dir="auto" value="' . $this->getValue('label') . '">';
		$html .= '<span class="tip">' . $L->get('This title is almost always used in the sidebar of the site') . '</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Show unique visitors') . '</label>';
		$html .= '<select name="showUniqueVisitors">';
		$html .= '<option value="true" ' . ($this->getValue('showUniqueVisitors') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('showUniqueVisitors') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function siteSidebar()
	{
		$counter = 0;

		if (pluginActivated('pluginVisitsStats')) {
			global $plugins;
			$visitsStats = $plugins['all']['pluginVisitsStats'];
			$currentDate = Date::current('Y-m-d');

			if ($this->getValue('showUniqueVisitors')) {
				$counter = $visitsStats->uniqueVisitors($currentDate);
			} else {
				$counter = $visitsStats->visits($currentDate);
			}
		}

		$html  = '<div class="plugin plugin-hit-counter">';
		$html .= '<h2 class="plugin-label">' . $this->getValue('label') . '</h2>';
		$html .= '<div class="plugin-content">';
		$html .= '<div class="counter">' . $counter . '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
}
