<?php

class pluginVersion extends Plugin {

	public function adminSidebar()
	{
		global $L;
		$html = '<a id="current-version" class="nav-link" href="'.HTML_PATH_ADMIN_ROOT.'about'.'">Version '.(defined('BLUDIT_PRO')?'<span class="fa fa-heart" style="color: #ffc107"></span>':'').'<span class="badge badge-warning badge-pill">'.BLUDIT_VERSION.'</span></a>';
		$html .= '<a id="new-version" style="display: none;" target="_blank" href="https://www.bludit.com">'.$L->get('New version available').' <span class="fa fa-bell" style="color: red"></span></a>';
		return $html;
	}

	public function adminBodyEnd()
	{
		// The follow Javascript get via AJAX the information about new versions
		// The script is on /bl-plugins/version/js/version.js
		$jsPath = $this->phpPath() . 'js' . DS;
		$scripts  = '<script>' . file_get_contents($jsPath . 'version.js') . '</script>';
		return $scripts;
	}
}