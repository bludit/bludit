<?php

class pluginVersion extends Plugin
{

	public function init()
	{
		$this->dbFields = array(
			'showCurrentVersion' => true,
			'newVersionAlert' => true
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div>';
		$html .= '<label>' . $L->get('Show current version in the sidebar') . '</label>';
		$html .= '<select name="showCurrentVersion">';
		$html .= '<option value="true" ' . ($this->getValue('showCurrentVersion') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('showCurrentVersion') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Show alert when there is a new version in the sidebar') . '</label>';
		$html .= '<select name="newVersionAlert">';
		$html .= '<option value="true" ' . ($this->getValue('newVersionAlert') === true ? 'selected' : '') . '>' . $L->get('Enabled') . '</option>';
		$html .= '<option value="false" ' . ($this->getValue('newVersionAlert') === false ? 'selected' : '') . '>' . $L->get('Disabled') . '</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function adminSidebar()
	{
		global $L;
		$html = '';
		if ($this->getValue('showCurrentVersion')) {
			$html = '<a id="current-version" class="nav-link" href="' . HTML_PATH_ADMIN_ROOT . 'about' . '">' . $L->get('Version') . ' ' . (defined('BLUDIT_PRO') ? '<span class="bi-heart" style="color: #ffc107"></span>' : '') . '<span class="badge bg-warning rounded-pill">' . BLUDIT_VERSION . '</span></a>';
		}
		if ($this->getValue('newVersionAlert')) {
			$html .= '<a id="new-version" style="display: none;" target="_blank" href="https://www.bludit.com">' . $L->get('New version available') . ' <span class="bi-bell" style="color: red"></span></a>';
		}
		return $html;
	}

	public function adminBodyEnd()
	{
		if ($this->getValue('newVersionAlert')) {
			// The follow Javascript get via AJAX the information about new versions
			// The script is on /bl-plugins/version/js/version.js
			$jsPath = $this->phpPath() . 'js' . DS;
			$scripts  = '<script>' . file_get_contents($jsPath . 'version.js') . '</script>';
			return $scripts;
		}
		return false;
	}
}
