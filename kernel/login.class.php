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
		Session::set('username', $username);
		Session::set('role', $role);
		Session::set('fingerPrint', $this->fingerPrint());
		Session::set('sessionTime', time());
	}

	public function isLogged()
	{
		if(Session::get('fingerPrint')===$this->fingerPrint())
		{
			$username = Session::get('username');

			return (!empty($username)) {
				return true;
			}
		}

		return false;
	}

	public function verifyUser($username, $password)
	{
		$username = trim($username);
		$password = trim($password);

		if(empty($username) || empty($password)) {
			return false;
		}

		$user = $this->dbUsers->get($username);
		if($user==false) {
			return false;
		}

		$passwordHash = sha1($password.$user['salt']);

		if($passwordHash === $user['password'])
		{
			$this->setLogin($username, $user['role']);

			return true;
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

}
