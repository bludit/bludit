<?php defined('BLUDIT') or die('Bludit CMS.');

class dbUsers extends dbJSON
{
	public $dbFields = array(
		'firstName'=>		array('inFile'=>false, 'value'=>''),
		'lastName'=>		array('inFile'=>false, 'value'=>''),
		'username'=>		array('inFile'=>false, 'value'=>''),
		'role'=>		array('inFile'=>false, 'value'=>'editor'),
		'password'=>		array('inFile'=>false, 'value'=>''),
		'salt'=>		array('inFile'=>false, 'value'=>'!Pink Floyd!Welcome to the machine!'),
		'email'=>		array('inFile'=>false, 'value'=>''),
		'registered'=>		array('inFile'=>false, 'value'=>'1985-03-15 10:00'),
		'tokenRemember'=>	array('inFile'=>false, 'value'=>''),
		'tokenAuth'=>		array('inFile'=>false, 'value'=>''),
		'tokenAuthTTL'=>	array('inFile'=>false, 'value'=>'2009-03-15 14:00'),
		'twitter'=>		array('inFile'=>false, 'value'=>''),
		'facebook'=>		array('inFile'=>false, 'value'=>''),
		'codepen'=>		array('inFile'=>false, 'value'=>''),
		'googlePlus'=>		array('inFile'=>false, 'value'=>''),
		'instagram'=>		array('inFile'=>false, 'value'=>'')
	);

	function __construct()
	{
		parent::__construct(DB_USERS);
	}

	// Disable the user
	public function disableUser($username)
	{
		$args['username'] = $username;
		$args['password'] = '!';

		return $this->set($args);
	}

	// Return TRUE if the user exists, FALSE otherwise
	public function exists($username)
	{
		return isset($this->db[$username]);
	}

	// Create a new user
	public function add($args)
	{
		$dataForDb = array();

		// Verify arguments with the database fields
		foreach($this->dbFields as $field=>$options) {
			if( isset($args[$field]) ) {
				$value = Sanitize::html($args[$field]);
			}
			else {
				$value = $options['value'];
			}

			// Set type
			settype($value, gettype($options['value']));

			// Save on database
			$dataForDb[$field] = $value;
		}

		$dataForDb['registered'] = Date::current(DB_DATE_FORMAT);
		$dataForDb['salt'] = $this->generateSalt();
		$dataForDb['password'] = $this->generatePasswordHash($dataForDb['password'], $dataForDb['salt']);
		$dataForDb['tokenAuth'] = $this->generateAuthToken();

		// Save the database
		$this->db[$dataForDb['username']] = $dataForDb;
		return $this->save();
	}

	// Set the parameters of a user
	public function set($args)
	{
		// Current database of the user
		$user = $this->db[$args['username']];

		// Verify arguments with the database fields
		foreach ($args as $field=>$value) {
			if (isset($this->dbFields[$field])) {
				$value = Sanitize::html($value);
				settype($value, gettype($this->dbFields[$field]['value']));
				$user[$field] = $value;
			}
		}

		// Set a new password
		if (!empty($args['password'])) {
			$user['salt'] = $this->generateSalt();
			$user['password'] = $this->generatePasswordHash($args['password'], $user['salt']);
			$user['tokenAuth'] = $this->generateAuthToken();
		}

		// Save the database
		$this->db[$args['username']] = $user;
		return $this->save();
	}

	// Delete an user
	public function delete($username)
	{
		unset($this->db[$username]);
		return $this->save();
	}

	public function getUser($username)
	{
		if ($this->exists($username)) {
			$User = new User();
			$User->setField('username', $username);

			foreach ($this->db[$username] as $key=>$value) {
				$User->setField($key, $value);
			}
			return $User;
		}
		return false;
	}

	public function generateAuthToken()
	{
		return md5( uniqid().time().DOMAIN );
	}

	public function generateRememberToken()
	{
		return $this->generateAuthToken();
	}

	public function generateSalt()
	{
		return Text::randomText(SALT_LENGTH);
	}

	public function generatePasswordHash($password, $salt)
	{
		return sha1($password.$salt);
	}

	public function setRememberToken($username, $token)
	{
		$args['username']	= $username;
		$args['tokenRemember']	= $token;
		return $this->set($args);
	}

	public function setPassword($username, $password)
	{
		$args['username']	= $username;
		$args['password']	= $hash;

		return $this->set($args);
	}

	// Return the username associated to an email, FALSE otherwise
	public function getByEmail($email)
	{
		foreach ($this->db as $username=>$values) {
			if ($values['email']==$email) {
				return $username;
			}
		}
		return false;
	}

	// Returns the username with the authentication token assigned, FALSE otherwise
	public function getByAuthToken($token)
	{
		foreach ($this->db as $username=>$fields) {
			if ($fields['tokenAuth']==$token) {
				return $username;
			}
		}
		return false;
	}

	// Returns the username with the remember token assigned, FALSE otherwise
	public function getByRememberToken($token)
	{
		foreach ($this->db as $username=>$fields) {
			if (!empty($fields['tokenRemember'])) {
				if ($fields['tokenRemember']==$token) {
					return $username;
				}
			}
		}
		return false;
	}

	// This function clean all tokens for Remember me
	// This function is used when some hacker try to use an invalid remember token
	public function invalidateAllRememberTokens()
	{
		foreach ($this->db as $username=>$values) {
			$this->db[$username]['tokenRemember'] = '';
		}
		return $this->save();
	}

	// Returns array with the username databases filtered by username, FALSE otherwise
	public function getDB($username)
	{
		if ($this->exists($username)) {
			return $this->db[$username];
		}
		return false;
	}

	public function getAll()
	{
		return $this->db;
	}

	public function getAllUsers()
	{
		$tmp = array();
		foreach ($this->db as $username=>$fields) {
			$tmp[$username] = $this->getUser($username);
		}
		return $tmp;
	}
}
