<?php defined('BLUDIT') or die('Bludit CMS.');

class Security extends dbJSON
{
	private $dbFields = array(
		'minutesBlocked'=>5,
		'numberFailuresAllowed'=>10,
		'blackList'=>array('numberFailures', 'lastFailure')
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'security.php');
	}

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
		if($currentTime > $lastFailure + $this->db['minutesBlocked']) {
			return false;
		}

		// The IP has more failures than number of failures, then the IP is blocked.
		if($numberFailures >= $this->db['numberFailuresAllowed']) {
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

		if(isset($this->db['blackList'][$ip])) {
			$numberFailures = $userBlack['numberFailures'];
			$numberFailures = $numberFailures + 1;
		}

		$this->db['blackList'][$ip] = array('lastFailure'=>$currentTime, 'numberFailures'=>$numberFailures);

		// Save the database
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
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