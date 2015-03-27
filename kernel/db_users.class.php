<?php defined('BLUDIT') or die('Bludit CMS.');

class dbUsers extends DB_SERIALIZE
{
	function __construct()
	{
		parent::__construct(PATH_DATABASES.'users.php');
	}

	// Return an array with the username databases
	function get($username)
	{
		if($this->validUsername($username)) {
			return $this->vars['users'][$username];
		}

		return false;
	}

	// Return TRUE if the post exists, FALSE otherwise.
	public function validUsername($username)
	{
		return isset($this->vars['users'][$username]);
	}

}
