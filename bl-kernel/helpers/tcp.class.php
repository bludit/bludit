<?php defined('BLUDIT') or die('Bludit CMS.');

class TCP {

	public static function http($url, $method='GET', $verifySSL=true, $timeOut=10, $followRedirections=true, $binary=true, $headers=false)
	{
		if (function_exists('curl_version')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirections);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, $binary);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
			if ($method=='POST') {
				curl_setopt($ch, CURLOPT_POST, true);
			}
			$output = curl_exec($ch);
			if ($output===false) {
				Log::set('Curl error: '.curl_error($ch));
			}
			curl_close($ch);
		} else {
			$options = array(
				'http'=>array(
					'method'=>$method,
					'timeout'=>$timeOut,
					'follow_location'=>$followRedirections
				),
				"ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false
				)
			);
			$stream = stream_context_create($options);
			$output = file_get_contents($url, false, $stream);
		}

		return $output;
	}

	public static function download($url, $destination)
	{
		$data = self::http($url, $method='GET', $verifySSL=true, $timeOut=30, $followRedirections=true, $binary=true, $headers=false);
		return file_put_contents($destination, $data);
	}

	public static function getIP()
	{
		if (getenv('HTTP_CLIENT_IP'))
			$ip = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ip = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ip = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ip = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ip = getenv('REMOTE_ADDR');
		else
		    return false;

		return $ip;
	}

}