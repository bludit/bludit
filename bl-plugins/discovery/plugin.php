<?php

class pluginDiscovery extends Plugin {

	public function init()
	{
		$this->formButtons = false;
	}

	public function form()
	{
		global $Language;

		if (!pluginEnabled('API')) {
			$html = '<div style="width: 50%" class="uk-alert">Enable the plugin API to use this plugin.</div>';
		} else {
			$this->pingBludit();
			$html = '<div style="width: 50%" class="uk-alert uk-alert-success">Your Site is in sync with Bludit Discovery.</div>';
		}

		return $html;
	}

	private function pingBludit()
	{
		global $plugins;

		$API = $plugins['all']['pluginAPI'];
		$APIToken = $API->getValue('token');

		$url = 'https://ping.bludit.com?site='.urlencode(DOMAIN_BASE).'&token='.urlencode($APIToken);
		TCP::http($url, $method='GET', $verifySSL=true, $timeOut=4, $followRedirections=false, $binary=false, $headers=false);
	}

}