<?php

class popeye extends Plugin
{

	public function init()
	{
		$this->dbFields = array(
			'googleFonts' => true,
			'darkMode' => true,
			'dateFormat' => 'relative',
			'showTags' => true
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="mb-3">';
		$html .= '<label class="form-label" for="darkMode">' . $L->get('Dark Mode') . '</label>';
		$html .= '<select class="form-select" id="darkMode" name="darkMode">';
		$html .= '<option value="true" ' . ($this->getValue('darkMode') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('darkMode') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '<div class="form-text">' . $L->get('Enable or disable dark mode.') . '</div>';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="googleFonts">' . $L->get('Google Fonts') . '</label>';
		$html .= '<select class="form-select" id="googleFonts" name="googleFonts">';
		$html .= '<option value="true" ' . ($this->getValue('googleFonts') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('googleFonts') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '<div class="form-text">' . $L->get('Enable or disable Google fonts.') . '</div>';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="dateFormat">' . $L->get('Date format') . '</label>';
		$html .= '<select class="form-select" id="dateFormat" name="dateFormat">';
		$html .= '<option value="relative" ' . ($this->getValue('dateFormat') == 'relative' ? 'selected' : '') . '>' . $L->get('Relative') . '</option>';
		$html .= '<option value="absolute" ' . ($this->getValue('dateFormat') == 'absolute' ? 'selected' : '') . '>' . $L->get('Absolute') . '</option>';
		$html .= '</select>';
		$html .= '<div class="form-text">' . $L->get('Change the date format for the main page.') . '</div>';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="showTags">' . $L->get('Show tags') . '</label>';
		$html .= '<select class="form-select" id="showTags" name="showTags">';
		$html .= '<option value="true" ' . ($this->getValue('showTags') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('showTags') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '<div class="form-text">' . $L->get('Show tags in the main page for each article.') . '</div>';
		$html .= '</div>';

		return $html;
	}

	public function darkMode()
	{
		return $this->getValue('darkMode');
	}

	public function googleFonts()
	{
		return $this->getValue('googleFonts');
	}

	public function dateFormat()
	{
		return $this->getValue('dateFormat');
	}

	public function showTags()
	{
		return $this->getValue('showTags');
	}
}
