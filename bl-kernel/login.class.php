<?php defined('BLUDIT') or die('Bludit CMS.');

class Login {

	private $dbUsers;

	function __construct($dbUsers)
	{
		$this->dbUsers = $dbUsers;
	}

	public function username()
	{
		return Session::get('username');
	}

	public function role()
	{
		return Session::get('role');
	}

	public function setLogin($username, $role)
	{
		Session::set('username',	$username);
		Session::set('role', 		$role);
		Session::set('fingerPrint',	$this->fingerPrint());
		Session::set('sessionTime',	time());

		Log::set(__METHOD__.LOG_SEP.'Set fingerPrint: '.$this->fingerPrint());
	}

	public function isLogged()
	{
		if(Session::get('fingerPrint')===$this->fingerPrint())
		{
			$username = Session::get('username');

			if(!empty($username)) {
				return true;
			}
			else {
				Log::set(__METHOD__.LOG_SEP.'Session username empty: '.$username);
			}
		}
		else
		{
			Log::set(__METHOD__.LOG_SEP.'FingerPrint are differents. Session fingerPrint: '.Session::get('fingerPrint').' !== Current fingerPrint: '.$this->fingerPrint());
		}

		return false;
	}

	public function verifyUser($username, $password)
	{
		$username = Sanitize::html($username);
		$password = Sanitize::html($password);

		$username = trim($username);
		$password = trim($password);

		if(empty($username) || empty($password)) {
			Log::set(__METHOD__.LOG_SEP.'Username or password empty. Username: '.$username.' - Password: '.$password);
			return false;
		}

		$user = $this->dbUsers->getDb($username);
		if($user==false) {
			Log::set(__METHOD__.LOG_SEP.'Username does not exist: '.$username);
			return false;
		}

		$passwordHash = sha1($password.$user['salt']);

		if($passwordHash === $user['password'])
		{
			$this->setLogin($username, $user['role']);

			Log::set(__METHOD__.LOG_SEP.'User logged succeeded by username and password - Username: '.$username);

			return true;
		}
		else {
			Log::set(__METHOD__.LOG_SEP.'Password incorrect.');
		}

		return false;
	}

	public function verifyUserByToken($username, $token)
	{
		$username = Sanitize::html($username);
		$token = Sanitize::html($token);

		$username = trim($username);
		$token = trim($token);

		if(empty($username) || empty($token)) {
			Log::set(__METHOD__.LOG_SEP.'Username or Token-email empty. Username: '.$username.' - Token-email: '.$token);
			return false;
		}

		$user = $this->dbUsers->getDb($username);
		if($user==false) {
			Log::set(__METHOD__.LOG_SEP.'Username does not exist: '.$username);
			return false;
		}

		$currentTime = Date::current(DB_DATE_FORMAT);
		if($user['tokenEmailTTL']<$currentTime) {
			Log::set(__METHOD__.LOG_SEP.'Token-email expired: '.$username);
			return false;
		}

		if($token === $user['tokenEmail'])
		{
			// Set the user loggued.
			$this->setLogin($username, $user['role']);

			// Invalidate the current token.
			$this->dbUsers->generateTokenEmail($username);

			Log::set(__METHOD__.LOG_SEP.'User logged succeeded by Token-email - Username: '.$username);

			return true;
		}
		else {
			Log::set(__METHOD__.LOG_SEP.'Token-email incorrect.');
		}

		return false;
	}

	public function fingerPrint($random=false)
	{
		// User agent
		$agent = getenv('HTTP_USER_AGENT');
		if(empty($agent)) {
			$agent = 'Bludit/1.0 (Mr Nibbler Protocol)';
		}

		// User IP
		if(getenv('HTTP_X_FORWARDED_FOR'))
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		elseif(getenv('HTTP_CLIENT_IP'))
			$ip = getenv('HTTP_CLIENT_IP');
		else
			$ip = getenv('REMOTE_ADDR');

		if($random) {
			return sha1(mt_rand().$agent.$ip);
		}

		// DEBUG: Ver CLIENT IP, hay veces que retorna la ip ::1 y otras 127.0.0.1
		return sha1($agent);
	}

	public function logout()
	{
		return Session::destroy();
	}

}
