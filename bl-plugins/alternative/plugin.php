<?php

class alternative extends Plugin
{

	public function init()
	{
		$this->dbFields = array(
			'googleFonts' => false,
			'showPostInformation' => false,
			'dateFormat' => 'relative'
		);
	}

	public function form()
	{
		global $L;

		$html = '';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="googleFonts">' . $L->get('Google Fonts') . '</label>';
		$html .= '<select class="form-select" id="googleFonts" name="googleFonts">';
		$html .= '<option value="false" ' . ($this->getValue('googleFonts') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '<option value="true" ' . ($this->getValue('googleFonts') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '</select>';
		$html .= '<div class="form-text">' . $L->get('Enable or disable Google fonts.') . '</div>';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="showPostInformation">' . $L->get('Show Post Information') . '</label>';
		$html .= '<select class="form-select" id="showPostInformation" name="showPostInformation">';
		$html .= '<option value="false" ' . ($this->getValue('showPostInformation') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '<option value="true" ' . ($this->getValue('showPostInformation') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div class="mb-3">';
		$html .= '<label class="form-label" for="dateFormat">' . $L->get('Date format') . '</label>';
		$html .= '<select class="form-select" id="dateFormat" name="dateFormat">';
		$html .= '<option value="noshow" ' . ($this->getValue('dateFormat') == 'noshow' ? 'selected' : '') . '>' . $L->get('No show') . '</option>';
		$html .= '<option value="relative" ' . ($this->getValue('dateFormat') == 'relative' ? 'selected' : '') . '>' . $L->get('Relative') . '</option>';
		$html .= '<option value="absolute" ' . ($this->getValue('dateFormat') == 'absolute' ? 'selected' : '') . '>' . $L->get('Absolute') . '</option>';
		$html .= '</select>';
		$html .= '<div class="form-text">' . $L->get('Change the date format for the main page.') . '</div>';
		$html .= '</div>';

		return $html;
	}

	public function showPostInformation()
	{
		return $this->getValue('showPostInformation');
	}

	public function googleFonts()
	{
		return $this->getValue('googleFonts');
	}

	public function dateFormat()
	{
		return $this->getValue('dateFormat');
	}
}
