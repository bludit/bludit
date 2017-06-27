<?php

class pluginVersion extends Plugin {

	private function getVersion()
	{
		$url = 'https://version.bludit.com';

		$output = TCP::http($url);

		$json = json_decode($output, true);
		if(empty($json)) {
			return array('version'=>'');
		}

		return $json;
	}
}