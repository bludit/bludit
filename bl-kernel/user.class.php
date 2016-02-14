<?php defined('BLUDIT') or die('Bludit CMS.');

class User
{
	public $db;

	public function setField($field, $value)
	{
		$this->db[$field] = $value;

		return true;
	}

	public function getField($field)
	{
		if(isset($this->db[$field])) {
			return $this->db[$field];
		}

		return false;
	}

	// Returns username
	public function username()
	{
		return $this->getField('username');
	}

	public function firstName()
	{
		return $this->getField('firstName');
	}

	public function lastName()
	{
		return $this->getField('lastName');
	}

	public function role()
	{
		return $this->getField('role');
	}

	public function password()
	{
		return $this->getField('password');
	}

	public function salt()
	{
		return $this->getField('salt');
	}

	public function email()
	{
		return $this->getField('email');
	}

	public function registered()
	{
		return $this->getField('registered');
	}

	public function twitter()
	{
		return $this->getField('twitter');
	}

	public function facebook()
	{
		return $this->getField('facebook');
	}

	public function googlePlus()
	{
		return $this->getField('googlePlus');
	}

	public function instagram()
	{
		return $this->getField('instagram');
	}

	public function profilePicture($absolute=true)
	{
		$filename = $this->getField('username').'.png';

		if($absolute) {
			return HTML_PATH_UPLOADS_PROFILES.$filename;
		}

		return $filename;
	}

}