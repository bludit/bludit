<?php

class pluginHitCounter extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'sidebarLabel'=>'Hit Counter',
			'showRunningTotalValue'=>true,
			'showDailyValue'=>true,
			'showDailyUniqueValue'=>true,
			'showWeeklyValue'=>true,
			'showWeeklyUniqueValue'=>true,
			'showMonthlyValue'=>true,
			'showMonthlyUniqueValue'=>true
		);
	}

	public function form()
	{
		global $L;

		// Check if the plugin Simple Stats is activated
		if (!pluginActivated('pluginSimpleStats')) {
			// Show an alert about the dependency of the plugin
			$html  = '<div class="alert alert-warning" role="alert">';
			$html .= $L->get('plugin-depends-on-warning');
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
		$html .= '<label>'.$L->get('sidebar-label').'</label>';
		$html .= '<input name="sidebarLabel" type="text" value="'.$this->getValue('sidebarLabel').'">';
		$html .= '<span class="sidebar-label-tip">'.$L->get('sidebar-label-tip').'</span>';
		$html .= '</div>';

		// Show ongoing running total
		$html .= '<div>';
		$html .= '<label>'.$L->get('show-running-total-lable').'</label>';
		$html .= '<select name="showRunningTotalValue">';
		$html .= '<option value="true" '.($this->getValue('showRunningTotalValue')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('showRunningTotalValue')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		// Show daily page hits/visits
		$html .= '<div>';
		$html .= '<label>'.$L->get('show-daily-value-label').'</label>';
		$html .= '<select name="showDailyValue">';
		$html .= '<option value="true" '.($this->getValue('showDailyValue')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('showDailyValue')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('show-daily-unique-value-label').'</label>';
		$html .= '<select name="showDailyUniqueValue">';
		$html .= '<option value="true" '.($this->getValue('showDailyUniqueValue')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('showDailyUniqueValue')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		// Show weekly page hits/visits
		$html .= '<div>';
		$html .= '<label>'.$L->get('show-weekly-value-label').'</label>';
		$html .= '<select name="showWeeklyValue">';
		$html .= '<option value="true" '.($this->getValue('showWeeklyValue')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('showWeeklyValue')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('show-weekly-unique-value-label').'</label>';
		$html .= '<select name="showWeeklyUniqueValue">';
		$html .= '<option value="true" '.($this->getValue('showWeeklyUniqueValue')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('showWeeklyUniqueValue')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		// Show monthly page hits/visits
		$html .= '<div>';
		$html .= '<label>'.$L->get('show-monthly-value-label').'</label>';
		$html .= '<select name="showMonthlyValue">';
		$html .= '<option value="true" '.($this->getValue('showMonthlyValue')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('showMonthlyValue')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('show-Monthly-unique-value-label').'</label>';
		$html .= '<select name="showMonthlyUniqueValue">';
		$html .= '<option value="true" '.($this->getValue('showMonthlyUniqueValue')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('showMonthlyUniqueValue')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';
		
		return $html;
	}

	public function siteSidebar()
	{
		global $L;
		$formatStyle=NumberFormatter::TYPE_INT32;
		$formatter= new NumberFormatter($locale, $formatStyle);

		// Check if the plugin Simple Stats is activated
		if (pluginActivated('pluginSimpleStats')) {
			// Get the object of the plugin Simple Stats
			global $plugins;

			IF (	($this->getValue('showRunningTotalValue') )
					||	($this->getValue('showDailyValue'))
					||	($this->getValue('showDailyUniqueValue'))
					||	($this->getValue('showWeeklyValue'))
					||	($this->getValue('showWeeklyUniqueValue'))
					||	($this->getValue('showMonthlyValue'))
					||	($this->getValue('showMonthlyUniqueValue'))	)
			{

				$simpleStats = $plugins['all']['pluginSimpleStats'];

				$html  = '<div class="plugin plugin-hit-counter">';
				$html .= '<h2 class="plugin-label">'.$this->getValue('sidebarLabel').'</h2>';

				$html .= '<ul class="child">';

				// Get all the needed values 
				if ($this->getValue('showRunningTotalValue') ) {
					$runningTotals = json_decode(file_get_contents($simpleStats->workspace().'running-totals.json'),TRUE);
					$runningTotal = $runningTotals['runningTotals']['pageCounter'];
					$html .= '<li class="counter">'.($L->get('show-running-total-lable')).' '.$formatter->format($runningTotal).'</li>';
				}
				if ( ($this->getValue('showDailyValue')) || ($this->getValue('showDailyUniqueValue')) ) {
					$currentDate = Date::current('Y-m-d');
					
					if ($this->getValue('showDailyValue')) {
						$pageViewsToday = 0;
						$pageViewsToday = $simpleStats->getPageViewCount($currentDate, 'Daily');	
						$html .= '<li class="counter">'.($L->get('show-daily-value-label')).' '.$formatter->format($pageViewsToday).'</li>';
					}
					if ($this->getValue('showDailyUniqueValue')) {
						$uniqueVisitorsToday = 0;
						$uniqueVisitorsToday = $simpleStats->getUniqueVisitorCount($currentDate, 'Daily');	
						$html .= '<li class="counter">'.($L->get('show-daily-unique-value-label')).' '.$formatter->format($uniqueVisitorsToday).'</li>';
					}
				}

				if ( ($this->getValue('showWeeklyValue')) || ($this->getValue('showWeeklyUniqueValue')) ) {
					$mondayDateThisWeek = date("Y-m-d", strtotime('monday this week'));	

					if ($this->getValue('showWeeklyValue')) {
						$pageViewsThisWeek = 0;					
						$pageViewsThisWeek = $simpleStats->getPageViewCount($mondayDateThisWeek, 'Weekly');
						$html .= '<li class="counter">'.($L->get('show-weekly-value-label')).' '.$formatter->format($pageViewsThisWeek).'</li>';
					}
					if ($this->getValue('showWeeklyUniqueValue')) {
						$uniqueVisitorsThisWeek = 0;
						$uniqueVisitorsThisWeek = $simpleStats->getUniqueVisitorCount($mondayDateThisWeek, 'Weekly');
						$html .= '<li class="counter">'.($L->get('show-weekly-unique-value-label')).' '.$formatter->format($uniqueVisitorsThisWeek).'</li>';
					}
				}

				if ( ($this->getValue('showMonthlyValue')) || ($this->getValue('showMonthlyUniqueValue')) ) {
					$firstDateOfThisThisMonth = date("Y-m-d", strtotime('first day of this month'));
					
					if ($this->getValue('showMonthlyValue')) {
						$pageViewsThisMonth = 0;
						$pageViewsThisMonth = $simpleStats->getPageViewCount($firstDateOfThisThisMonth, 'Monthly');
						$html .= '<li class="counter">'.($L->get('show-monthly-value-label')).' '.$formatter->format($pageViewsThisMonth).'</li>';
						
					}
					if ($this->getValue('showMonthlyUniqueValue') ) {
						$uniqueVisitorsThisMonth = 0;
						$uniqueVisitorsThisMonth = $simpleStats->getUniqueVisitorCount($firstDateOfThisThisMonth, 'Monthly');
						$html .= '<li class="counter">'.($L->get('show-monthly-unique-value-label')).' '.$formatter->format($uniqueVisitorsThisMonth).'</li>';
					}
				}

				$html .= '</ul>';
			}
		}

 		$html .= '</div>';
		return $html;
	}
}