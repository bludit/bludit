<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Net {

	public static function get_user_ip()
	{
		if(getenv('HTTP_X_FORWARDED_FOR'))
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		elseif(getenv('HTTP_CLIENT_IP'))
			$ip = getenv('HTTP_CLIENT_IP');
		else
			$ip = getenv('REMOTE_ADDR');

		if(filter_var($ip, FILTER_VALIDATE_IP))
			return $ip;

		return getenv('REMOTE_ADDR');
	}

	public static function get_user_agent()
	{
		return getenv('HTTP_USER_AGENT');
	}

	public static function check_http_code($url, $code)
	{
		if(in_array('curl', get_loaded_extensions()))
		{
			$curl = curl_init();
			curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER=>true, CURLOPT_URL=>$url));
			curl_exec($curl);
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close( $curl );

			return($http_code==$code);
		}

		// If curl is not installed, use get_headers
		$headers = get_headers($url);

		if(strpos($headers[0], (string)$code) == false)
			return false;

		return true;
	}

}

?>
