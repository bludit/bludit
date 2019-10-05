<?php defined('BLUDIT') or die('Bludit Badass CMS.');

class Security extends dbJSON
{
	protected $dbFields = array(
		'minutesBlocked'=>5,
		'numberFailuresAllowed'=>10,
		'blackList'=>array()
	);

	function __construct()
	{
		parent::__construct(DB_SECURITY);
	}

	// ====================================================
	// TOKEN FOR CSRF
	// ====================================================

	// Generate and save the token in Session
	public function generateTokenCSRF()
	{
		$token = sha1( uniqid().time() );
		Session::set('tokenCSRF', $token);
		Log::set(__METHOD__.LOG_SEP.'New Token CSRF ['.$token.']');
	}

	// Validate the token
	public function validateTokenCSRF($token)
	{
		$sessionToken = $this->getTokenCSRF();
		return ( !empty($sessionToken) && ($sessionToken===$token) );
	}

	// Returns the token
	public function getTokenCSRF()
	{
		return Session::get('tokenCSRF');
	}

	// ====================================================
	// BRUTE FORCE PROTECTION
	// ====================================================

	public function isBlocked()
	{
		$ip = $this->getUserIp();

		if (!isset($this->db['blackList'][$ip])) {
			return false;
		}

		$currentTime = time();
		$userBlack = $this->db['blackList'][$ip];
		$numberFailures = $userBlack['numberFailures'];
		$lastFailure = $userBlack['lastFailure'];

		// Check if the IP is expired, then is not blocked
		if ($currentTime > $lastFailure + ($this->db['minutesBlocked']*60)) {
			return false;
		}

		// The IP has more failures than number of failures, then the IP is blocked
		if ($numberFailures >= $this->db['numberFailuresAllowed']) {
			Log::set(__METHOD__.LOG_SEP.'IP Blocked:'.$ip);
			return true;
		}

		// Otherwise the IP is not blocked
		return false;
	}

	// Add or update the current client IP on the blacklist
	public function addToBlacklist()
	{
		$ip = $this->getUserIp();
		$currentTime = time();
		$numberFailures = 1;

		if (isset($this->db['blackList'][$ip])) {
			$userBlack = $this->db['blackList'][$ip];
			$lastFailure = $userBlack['lastFailure'];

			// Check if the IP is expired, then renew the number of failures
			if($currentTime <= $lastFailure + ($this->db['minutesBlocked']*60)) {
				$numberFailures = $userBlack['numberFailures'];
				$numberFailures = $numberFailures + 1;
			}
		}

		$this->db['blackList'][$ip] = array('lastFailure'=>$currentTime, 'numberFailures'=>$numberFailures);
		Log::set(__METHOD__.LOG_SEP.'Blacklist, IP:'.$ip.', Number of failures:'.$numberFailures);
		return $this->save();
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
		return getenv('REMOTE_ADDR');
	}
}
