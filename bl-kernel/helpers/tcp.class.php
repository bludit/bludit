<?php defined('BLUDIT') or die('Bludit CMS.');

class TCP {

	public static function http($url, $method='GET', $verifySSL=true, $timeOut=1)
	{
		if( function_exists('curl_version') ) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			// TRUE to include the header in the output
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
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
					'method'=>$method,
					'timeout'=>$timeOut
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