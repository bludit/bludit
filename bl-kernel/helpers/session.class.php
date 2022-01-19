<?php defined('BLUDIT') or die('Bludit CMS.');

class Session {

	private static $started = false;
	private static $sessionName = 'BLUDIT-KEY';

	public static function start($path, $secure)
	{
		// Try to set the session timeout on server side, 1 hour of timeout
		ini_set('session.gc_maxlifetime', SESSION_GC_MAXLIFETIME);

		// If set to TRUE then PHP will attempt to send the httponly flag when setting the session cookie.
		$httponly = true;

		// Gets current cookies params.
		$cookieParams = session_get_cookie_params();

        if (empty($path)) {
			$httponly = true;
            $path = '/';
        }

        session_set_cookie_params([
            'lifetime' => $cookieParams["lifetime"],
            'path' => $path,
            'domain' => $cookieParams["domain"],
            'secure' => $secure,
            'httponly' => true
        ]);

		// Sets the session name to the one set above.
		session_name(self::$sessionName);

		// Start session.
		self::$started = session_start();

		// Regenerated the session, delete the old one. There are problems with AJAX.
		//session_regenerate_id(true);

		if (!self::$started) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to start the session.');
		}
	}

	public static function started()
	{
		return self::$started;
	}

	public static function destroy()
	{
		session_destroy();
		unset($_SESSION);
		unset($_COOKIE[self::$sessionName]);
		Cookie::set(self::$sessionName, '', -1);
		self::$started = false;
		Log::set(__METHOD__.LOG_SEP.'Session destroyed.');
		return !isset($_SESSION);
	}

	public static function set($key, $value)
	{
		$key = 's_'.$key;

		$_SESSION[$key] = $value;
	}

	public static function get($key)
	{
		$key = 's_'.$key;

		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		return false;
	}

	public static function remove($key)
	{
		$key = 's_'.$key;

		unset($_SESSION[$key]);
	}
}
