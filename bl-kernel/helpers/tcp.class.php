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
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Bludit/'.BLUDIT_VERSION);
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
					"verify_peer"=>$verifySSL,
					"verify_peer_name"=>$verifySSL
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

	/*
	| Download a file checking the HTTP response code and the size of the file
	| Returns the amount of bytes written, FALSE when the download fail
	|
	| @url			string	URL to download
	| @destination		string	Absolute path where to store the file
	| @maxBytes		int	Maximum amount of bytes allowed, 0 for unlimited
	| @timeOut		int	Seconds
	|
	| @return		int|false
	*/
	public static function downloadFile($url, $destination, $maxBytes = 0, $timeOut = 30)
	{
		if (!function_exists('curl_version')) {
			Log::set('TCP::downloadFile' . LOG_SEP . 'The extension cURL is required to download files.', LOG_TYPE_ERROR);
			return false;
		}

		$handler = fopen($destination, 'w');
		if ($handler === false) {
			Log::set('TCP::downloadFile' . LOG_SEP . 'Unable to write the file, ' . $destination, LOG_TYPE_ERROR);
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FILE, $handler);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Bludit/'.BLUDIT_VERSION);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);

		// Stop the transfer as soon as the file is bigger than the maximum allowed
		if ($maxBytes > 0) {
			curl_setopt($ch, CURLOPT_NOPROGRESS, false);
			curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $downloadSize, $downloaded, $uploadSize, $uploaded) use ($maxBytes) {
				return (($downloadSize > $maxBytes) || ($downloaded > $maxBytes)) ? 1 : 0;
			});
		}

		$success = curl_exec($ch);
		$errorMessage = curl_error($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		curl_close($ch);
		fclose($handler);

		if ($success === false) {
			Log::set('TCP::downloadFile' . LOG_SEP . 'Error downloading the file, ' . $url . LOG_SEP . $errorMessage, LOG_TYPE_ERROR);
			unlink($destination);
			return false;
		}

		if ($responseCode >= 400) {
			Log::set('TCP::downloadFile' . LOG_SEP . 'The server returned the HTTP code ' . $responseCode . ', ' . $url, LOG_TYPE_ERROR);
			unlink($destination);
			return false;
		}

		$bytes = filesize($destination);
		if (($maxBytes > 0) && ($bytes > $maxBytes)) {
			Log::set('TCP::downloadFile' . LOG_SEP . 'The file is bigger than the maximum allowed, ' . $url, LOG_TYPE_ERROR);
			unlink($destination);
			return false;
		}

		return $bytes;
	}

}
