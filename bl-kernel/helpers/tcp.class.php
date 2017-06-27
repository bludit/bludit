<?php defined('BLUDIT') or die('Bludit CMS.');

class TCP {

	public static function http($url, $method='GET', $verifySSL=true)
	{
		if( function_exists('curl_version') ) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			// TRUE to include the header in the output
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
			if($method=='POST') {
				curl_setopt($ch, CURLOPT_POST, true);
			}
			$output = curl_exec($ch);
			if($output===false) {
				Log::set('Curl error: '.curl_error($ch));
			}
			curl_close($ch);
		}
		else {
			$options = array(
				'http'=>array(
					'method'=>$method
				),
				"ssl"=>array(
					"verify_peer"=>$verifySSL,
					"verify_peer_name"=>$verifySSL
				)
			);
			$stream = stream_context_create($options);
			$output = file_get_contents($url, false, $stream);
		}

		return $output;
	}


}