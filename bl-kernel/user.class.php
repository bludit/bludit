<?php defined('BLUDIT') or die('Bludit CMS.');

class User {
	protected $vars;

	function __construct($username)
	{
		global $users;

		$this->vars['username'] = $username;

		if ($username===false) {
			$row = $users->getDefaultFields();
		} else {
			if (Text::isEmpty($username) || !$users->exists($username)) {
				$errorMessage = 'User not found in the database by username ['.$username.']';
				Log::set(__METHOD__.LOG_SEP.$errorMessage);
				throw new Exception($errorMessage);
			}
			$row = $users->getUserDB($username);
		}

		foreach ($row as $field=>$value) {
			$this->setField($field, $value);
		}
	}

	public function getValue($field)
	{
		if (isset($this->vars[$field])) {
			return $this->vars[$field];
		}
		return false;
	}

	public function setField($field, $value)
	{
		$this->vars[$field] = $value;
		return true;
	}

	public function getDB()
	{
		return $this->vars;
	}

	public function username()
	{
		return $this->getValue('username');
	}

	public function description()
	{
		return $this->getValue('description');
	}

	public function nickname()
	{
		return $this->getValue('nickname');
	}

	public function firstName()
	{
		return $this->getValue('firstName');
	}

	public function lastName()
	{
		return $this->getValue('lastName');
	}

	public function tokenAuth()
	{
		return $this->getValue('tokenAuth');
	}

	public function role()
	{
		return $this->getValue('role');
	}

	public function password()
	{
		return $this->getValue('password');
	}

	public function enabled()
	{
		$password = $this->getValue('password');
		return $password != '!';
	}

	public function salt()
	{
		return $this->getValue('salt');
	}

	public function email()
	{
		return $this->getValue('email');
	}

	public function registered()
	{
		return $this->getValue('registered');
	}

	public function twitter()
	{
		return $this->getValue('twitter');
	}

	public function facebook()
	{
		return $this->getValue('facebook');
	}

	public function codepen()
	{
		return $this->getValue('codepen');
	}

	public function instagram()
	{
		return $this->getValue('instagram');
	}

	public function github()
	{
		return $this->getValue('github');
	}

	public function gitlab()
	{
		return $this->getValue('gitlab');
	}

	public function linkedin()
	{
		return $this->getValue('linkedin');
	}

	public function xing()
	{
		return $this->getValue('xing');
	}

	public function mastodon()
	{
		return $this->getValue('mastodon');
	}

	public function vk()
	{
		return $this->getValue('vk');
	}

	public function profilePicture()
	{
		$filename = $this->getValue('username').'.png';
		if (!file_exists(PATH_UPLOADS_PROFILES.$filename)) {
			return false;
		}
		return DOMAIN_UPLOADS_PROFILES.$filename;
	}

	public function json($returnsArray=false)
	{
		$tmp['username'] 	= $this->username();
		$tmp['firstName'] 	= $this->firstName();
		$tmp['lastName'] 	= $this->lastName();
		$tmp['nickname'] 	= $this->nickname();
		$tmp['description'] 	= $this->description();
		$tmp['twitter'] 	= $this->twitter();
		$tmp['facebook'] 	= $this->facebook();
		$tmp['codepen'] 	= $this->codepen();
		$tmp['instagram'] 	= $this->instagram();
		$tmp['github'] 		= $this->github();
		$tmp['gitlab'] 		= $this->gitlab();
		$tmp['linkedin'] 	= $this->linkedin();
		$tmp['xing'] 		= $this->xing();
		$tmp['mastodon']	= $this->mastodon();
		$tmp['vk']		= $this->vk();
		$tmp['profilePicture']	= $this->profilePicture();

		if ($returnsArray) {
			return $tmp;
		}

		return json_encode($tmp);
	}

}