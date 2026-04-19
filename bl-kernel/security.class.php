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
		$token = bin2hex( openssl_random_pseudo_bytes(64) );
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

	// Single source of truth for the client IP across all of Bludit (core + plugins).
	// Reads REMOTE_ADDR only: proxy headers (X-Forwarded-For, HTTP_CLIENT_IP, etc.)
	// are client-controlled and forgeable. Deployments behind a reverse proxy
	// (Cloudflare, nginx, Apache) should rewrite REMOTE_ADDR at the web server
	// (mod_remoteip, real_ip_module, etc.), not in PHP.
	// Returns '' when REMOTE_ADDR is missing or not a valid IP, so callers can
	// safely use the result as an array key or hash input without null checks.
	public function getUserIp()
	{
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		if (filter_var($ip, FILTER_VALIDATE_IP)) {
			return $ip;
		}
		return '';
	}
}
