<?php defined('BLUDIT') or die('Bludit CMS.');

class Security extends dbJSON
{
	private $dbFields = array(
		'key1'=>'Where we go we dont need roads',
		'minutesBlocked'=>5,
		'numberFailuresAllowed'=>10,
		'blackList'=>array()
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'security.php');
	}

	// ====================================================
	// TOKEN FOR CSRF
	// ====================================================

	// Generate and save the token in Session.
	public function generateTokenCSRF()
	{
		$token = Text::randomText(8);
		$token = sha1($token);

		Log::set(__METHOD__.LOG_SEP.'New tokenCSRF was generated '.$token);

		Session::set('tokenCSRF', $token);
	}

	// Validate the token.
	public function validateTokenCSRF($token)
	{
		$sessionToken = Session::get('tokenCSRF');

		return ( !empty($sessionToken) && ($sessionToken===$token) );
	}

	// Returns the token.
	public function getTokenCSRF()
	{
		return Session::get('tokenCSRF');
	}

	public function printTokenCSRF()
	{
		echo Session::get('tokenCSRF');
	}

	// ====================================================
	// BRUTE FORCE PROTECTION
	// ====================================================

	public function isBlocked()
	{
		$ip = $this->getUserIp();

		if(!isset($this->db['blackList'][$ip])) {
			return false;
		}

		$currentTime = time();
		$userBlack = $this->db['blackList'][$ip];
		$numberFailures = $userBlack['numberFailures'];
		$lastFailure = $userBlack['lastFailure'];

		// Check if the IP is expired, then is not blocked.
		if($currentTime > $lastFailure + ($this->db['minutesBlocked']*60)) {
			return false;
		}

		// The IP has more failures than number of failures, then the IP is blocked.
		if($numberFailures >= $this->db['numberFailuresAllowed']) {
			Log::set(__METHOD__.LOG_SEP.'IP Blocked:'.$ip);
			return true;
		}

		// Otherwise the IP is not blocked.
		return false;
	}

	public function addLoginFail()
	{
		$ip = $this->getUserIp();
		$currentTime = time();
		$numberFailures = 1;

		if(isset($this->db['blackList'][$ip]))
		{
			$userBlack = $this->db['blackList'][$ip];
			$lastFailure = $userBlack['lastFailure'];

			// Check if the IP is expired, then renew the number of failures.
			if($currentTime <= $lastFailure + ($this->db['minutesBlocked']*60))
			{
				$numberFailures = $userBlack['numberFailures'];
				$numberFailures = $numberFailures + 1;
			}
		}

		$this->db['blackList'][$ip] = array('lastFailure'=>$currentTime, 'numberFailures'=>$numberFailures);

		Log::set(__METHOD__.LOG_SEP.'Blacklist, IP:'.$ip.', Number of failures:'.$numberFailures);

		// Save the database
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

	public function getNumberFailures($ip=null)
	{
		if(empty($ip)) {
			$ip = $this->getUserIp();
		}

		if(isset($this->db['blackList'][$ip])) {
			$userBlack = $this->db['blackList'][$ip];
			return $userBlack['numberFailures'];
		}
	}

	public function getUserIp()
	{
		// User IP
		if(getenv('HTTP_X_FORWARDED_FOR'))
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		elseif(getenv('HTTP_CLIENT_IP'))
			$ip = getenv('HTTP_CLIENT_IP');
		else
			$ip = getenv('REMOTE_ADDR');

		return $ip;
	}
}
