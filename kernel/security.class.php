<?php defined('BLUDIT') or die('Bludit CMS.');

class Security extends dbJSON
{
	private $dbFields = array(
		'minutesBlocked'=>5,
		'numberFailures'=>10
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'security.php');
	}



	public function addLoginFail()
	{
		$ip = $this->getUserIp();

		// Save the database
		$this->db[$ip] = (int)$this->db[$ip] + 1;
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