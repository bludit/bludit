<?php

class pluginPing extends Plugin {

	private function ping()
	{
		$url = 'https://ping.bludit.com?url='.DOMAIN_BASE;

		if( function_exists('curl_version') ) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			// TRUE to include the header in the output
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			$out = curl_exec($ch);
			if($out===false) {
				Log::set('Plugin Ping: Curl error: '.curl_error($ch));
			}
			curl_close($ch);
		}
		else {
			$options = array(
				"ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false
				)
			);
			$stream = stream_context_create($options);
			$out = file_get_contents($url, false, $stream);
		}
	}
}