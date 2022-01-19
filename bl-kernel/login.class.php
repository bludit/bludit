<?php defined('BLUDIT') or die('Bludit CMS.');

class Login {

	protected $users;

	function __construct()
	{
		if (isset($GLOBALS['users'])) {
			$this->users = $GLOBALS['users'];
		} else {
			$this->users = new Users();
		}

        if (isset($GLOBALS['site'])) {
            $this->site = $GLOBALS['site'];
        } else {
            $this->site = new Site();
        }

		// Start the Session
		if (!Session::started()) {
			Session::start($this->site->urlPath(), $this->site->isHTTPS());
		}
	}

	// Returns the username of the user logged
	public function username()
	{
		return Session::get('username');
	}

	// Returns the role of the user logged
	public function role()
	{
		return Session::get('role');
	}

	// Returns TRUE if the user is logged, FALSE otherwise
	public function isLogged()
	{
		if (Session::get('fingerPrint')===$this->fingerPrint()) {
			$username = Session::get('username');
			if (!empty($username)) {
				return true;
			} else {
				Log::set(__METHOD__.LOG_SEP.'Session username empty, destroying the session.');
				Session::destroy();
				return false;
			}
		}

		Log::set(__METHOD__.LOG_SEP.'FingerPrints are different. ['.Session::get('fingerPrint').'] != ['.$this->fingerPrint().']');
		return false;
	}

	// Set the session for the user logged
	public function setLogin($username, $role)
	{
		Session::set('username',	$username);
		Session::set('role', 		$role);
		Session::set('fingerPrint',	$this->fingerPrint());
		Session::set('sessionTime',	time());

		Log::set(__METHOD__.LOG_SEP.'User logged, fingerprint ['.$this->fingerPrint().']');
	}

	public function setRememberMe($username)
	{
		$username = Sanitize::html($username);

		// Set the token on the users database
		$token = $this->users->generateRememberToken();
		$this->users->setRememberToken($username, $token);

		// Set the token on the cookies
		Cookie::set(REMEMBER_COOKIE_USERNAME, $username, REMEMBER_COOKIE_EXPIRE_IN_DAYS);
		Cookie::set(REMEMBER_COOKIE_TOKEN, $token, REMEMBER_COOKIE_EXPIRE_IN_DAYS);

		Log::set(__METHOD__.LOG_SEP.'Cookies set for Remember Me.');
	}

	public function invalidateRememberMe()
	{
		// Invalidate all tokens on the user databases
		$this->users->invalidateAllRememberTokens();

		// Destroy the cookies
		Cookie::set(REMEMBER_COOKIE_USERNAME, '', -1);
		Cookie::set(REMEMBER_COOKIE_TOKEN, '', -1);
		unset($_COOKIE[REMEMBER_COOKIE_USERNAME]);
		unset($_COOKIE[REMEMBER_COOKIE_TOKEN]);
	}

	// Check if the username and the password are valid
	// Returns TRUE if valid and set the session
	// Returns FALSE for invalid username or password
	public function verifyUser($username, $password)
	{
		$username = Sanitize::html($username);
		$username = trim($username);

		if (empty($username) || empty($password)) {
			Log::set(__METHOD__.LOG_SEP.'Username or password empty. Username: '.$username);
			return false;
		}

		if (Text::length($password)<PASSWORD_LENGTH) {
			Log::set(__METHOD__.LOG_SEP.'Password length is shorter than required.');
			return false;
		}

		try {
			$user = new User($username);
		} catch (Exception $e) {
			return false;
		}

		$passwordHash = $this->users->generatePasswordHash($password, $user->salt());
		if ($passwordHash===$user->password()) {
			$this->setLogin($username, $user->role());
			Log::set(__METHOD__.LOG_SEP.'Successful user login by username and password - Username ['.$username.']');
			return true;
		}

		Log::set(__METHOD__.LOG_SEP.'Password incorrect.');
		return false;
	}

	// Check if the user has the cookies and the correct token
	public function verifyUserByRemember()
	{
		if (Cookie::isEmpty(REMEMBER_COOKIE_USERNAME) || Cookie::isEmpty(REMEMBER_COOKIE_TOKEN)) {
			return false;
		}

		$username 	= Cookie::get(REMEMBER_COOKIE_USERNAME);
		$token 		= Cookie::get(REMEMBER_COOKIE_TOKEN);

		$username 	= Sanitize::html($username);
		$token 		= Sanitize::html($token);

		$username 	= trim($username);
		$token 		= trim($token);

		if (empty($username) || empty($token)) {
			$this->invalidateRememberMe();
			Log::set(__METHOD__.LOG_SEP.'Username or Token empty. Username: '.$username.' - Token: '.$token);
			return false;
		}

		if ($username !== $this->users->getByRememberToken($token)) {
			$this->invalidateRememberMe();
			Log::set(__METHOD__.LOG_SEP.'The user has different token or the token doesn\'t exist.');
			return false;
		}

		// Get user from database and login
		$user = $this->users->getUserDB($username);
		$this->setLogin($username, $user['role']);
		Log::set(__METHOD__.LOG_SEP.'User authenticated via Remember Me.');
		return true;
	}

	public function fingerPrint()
	{
		$agent = getenv('HTTP_USER_AGENT');
		if (empty($agent)) {
			$agent = 'Bludit/2.0 (Mr Nibbler Protocol)';
		}
		return sha1($agent);
	}

	public function logout()
	{
		$this->invalidateRememberMe();
		Session::destroy();
		return true;
	}
}