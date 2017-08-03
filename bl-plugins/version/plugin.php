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
			</style>';

		return $html;
	}

	public function adminBodyEnd()
	{
		global $ADMIN_CONTROLLER;

		$timeToCheck = Session::get('timeToCheck') + 10*60;
		if( ($ADMIN_CONTROLLER=='dashboard') && ($timeToCheck<time()) ) {
			$versions = $this->getVersion();
			Session::set('timeToCheck', time());
			Session::set('stableVersion', $versions['stableVersion']);
		}

		if( version_compare(Session::get('stableVersion'), BLUDIT_VERSION, '>') ) {
			$html = '<div id="plugin-version"><a href="https://www.bludit.com">New version available</a></div>';
		} else {
			if(defined('BLUDIT_PRO')) {
				$html = '<div id="plugin-version">Bludit PRO v'.BLUDIT_VERSION.'</div>';
			} else {
				$html = '<div id="plugin-version">Bludit v'.BLUDIT_VERSION.'<a href="">Upgrade to Bludit PRO</a></div>';
			}
		}

		return $html;
	}

	private function getVersion()
	{
		$url = 'https://version.bludit.com';
		$output = TCP::http($url);

		$json = json_decode($output, true);
		if(empty($json)) {
			return false;
		}

		return $json;
	}
}