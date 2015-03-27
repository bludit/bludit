<?php defined('BLUDIT') or die('Bludit CMS.');

class Login {

	private $dbUsers;

	function __construct($dbUsers)
	{
		$this->dbUsers = $dbUsers;
	}

	public function setLogin($username)
	{
		Session::set('username', $username);
		Session::set('fingerPrint', $this->fingerPrint());
		Session::set('sessionTime', time());
	}

	public function isLogged()
	{
		if(Session::get('fingerPrint')===$this->fingerPrint())
		{
			if(!empty(Session::get('username'))) {
				return true;
			}
		}

		return false;
	}

	public function verifyUser($username, $password)
	{
		if(empty(trim($username)) || empty(trim($password)))
			return false;

		$user = $this->dbUsers->get($username);
		if($user==false)
			return false;

		$passwordHash = sha1($password.$user['salt']);
		if($passwordHash === $user['password'])
		{
			$this->setLogin($username);

			return true;
		}

		return false;
	}

	private function fingerPrint($random=false)
	{
		// User agent
		$agent = getenv('HTTP_USER_AGENT');
		if(empty($agent))
			$agent = 'Bludit/1.0 (Mr Nibbler Protocol)';

		// User IP
		if(getenv('HTTP_X_FORWARDED_FOR'))
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		elseif(getenv('HTTP_CLIENT_IP'))
			$ip = getenv('HTTP_CLIENT_IP');
		else
			$ip = getenv('REMOTE_ADDR');

		if($random)
			return sha1(mt_rand().$agent.$ip);

		return sha1($agent.$ip);
	}

}
