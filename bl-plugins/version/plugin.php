<?php

class pluginVersion extends Plugin {

	public function adminHead()
	{
		$html = '<style>
				#plugin-version {
					display: block;
					position: fixed;
					bottom: 0;
					right: 0;
					background: #eee;
					padding: 2px 10px;
					font-size: 0.9em;
					color: #555;
				}
				#plugin-version a {
					color: #777;
					margin-left: 8px;
				}
				#new-version,
				#current-version {
					display: none;
				}
			</style>';

		return $html;
	}

	public function adminBodyBegin()
	{
		global $Language;

		$html  = '<div id="plugin-version">';
		$html .= '<div id="new-version"><a target="_blank" href="https://www.bludit.com"><i class="fa fa-download" aria-hidden="true"></i> '.$Language->get('New version available').'</a></div>';

		if (defined('BLUDIT_PRO')) {
			$html .= '<div id="current-version">Bludit PRO v'.BLUDIT_VERSION.'</div>';
		} else {
			$html .= '<div id="current-version">Bludit v'.BLUDIT_VERSION.'<a target="_blank" href="https://pro.bludit.com">'.$Language->get('Upgrade to Bludit PRO').'</a></div>';
		}

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public function adminBodyEnd()
	{
		$jsPath = $this->phpPath() . 'js' . DS;
		$scripts  = '<script>' . file_get_contents($jsPath . 'version.js') . '</script>';
		return $scripts;
	}
}