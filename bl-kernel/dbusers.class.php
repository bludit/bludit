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
		'tokenEmail'=>		array('inFile'=>false, 'value'=>''),
		'tokenEmailTTL'=>	array('inFile'=>false, 'value'=>'2009-03-15 14:00'),
		'twitter'=>		array('inFile'=>false, 'value'=>''),
		'facebook'=>		array('inFile'=>false, 'value'=>''),
		'googlePlus'=>		array('inFile'=>false, 'value'=>''),
		'instagram'=>		array('inFile'=>false, 'value'=>'')
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'users.php');
	}

	public function getUser($username)
	{
		$User = new User();

		if($this->userExists($username))
		{
			$User->setField('username', $username);

			foreach($this->db[$username] as $key=>$value) {
				$User->setField($key, $value);
			}

			return $User;
		}

		return false;
	}

	public function getAll()
	{
		return $this->db;
	}

	// Return an array with the username databases, filtered by username.
	public function getDb($username)
	{
		if($this->userExists($username))
		{
			$user = $this->db[$username];

			return $user;
		}

		return false;
	}

	// Return the username associated to an email, if the email does not exists return FALSE.
	public function getByEmail($email)
	{
		foreach($this->db as $username=>$values) {
			if($values['email']==$email) {
				return $username;
			}
		}

		return false;
	}

	// Return TRUE if the user exists, FALSE otherwise.
	public function userExists($username)
	{
		return isset($this->db[$username]);
	}

	public function generateTokenEmail($username)
	{
		// Random hash
		$token = sha1(Text::randomText(SALT_LENGTH).time());
		$this->db[$username]['tokenEmail'] = $token;

		// Token time to live, defined by TOKEN_EMAIL_TTL
		$this->db[$username]['tokenEmailTTL'] = Date::currentOffset(DB_DATE_FORMAT, TOKEN_EMAIL_TTL);

		// Save the database
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return $token;
	}

	public function setPassword($username, $password)
	{
		$salt = Text::randomText(SALT_LENGTH);
		$hash = sha1($password.$salt);

		$args['username']	= $username;
		$args['salt']		= $salt;
		$args['password']	= $hash;

		return $this->set($args);
	}

	public function set($args)
	{
		$dataForDb = array();

		$user = $this->getDb($args['username']);

		if($user===false)
		{
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the username '.$args['username']);
			return false;
		}

		// Verify arguments with the database fields.
		foreach($args as $field=>$value)
		{
			if( isset($this->dbFields[$field]) )
			{
				// Sanitize.
				$tmpValue = Sanitize::html($value);

				// Set type.
				settype($tmpValue, gettype($this->dbFields[$field]['value']));

				$user[$field] = $tmpValue;
			}
		}

		// Save the database
		$this->db[$args['username']] = $user;
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

	public function delete($username)
	{
		unset($this->db[$username]);

		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

	public function add($args)
	{
		$dataForDb = array();

		// Verify arguments with the database fields.
		foreach($this->dbFields as $field=>$options)
		{
			// If the user send the field.
			if( isset($args[$field]) )
			{
				// Sanitize if will be saved on database.
				if( !$options['inFile'] ) {
					$tmpValue = Sanitize::html($args[$field]);
				}
				else {
					$tmpValue = $args[$field];
				}
			}
			// Uses a default value for the field.
			else
			{
				$tmpValue = $options['value'];
			}

			// Set type
			settype($tmpValue, gettype($options['value']));

			// Save on database
			$dataForDb[$field] = $tmpValue;
		}

		// Check if the user alredy exists.
		if( $this->userExists($dataForDb['username']) ) {
			return false;
		}

		// Current date.
		$dataForDb['registered'] = Date::current(DB_DATE_FORMAT);

		// Password
		$dataForDb['salt'] = Text::randomText(SALT_LENGTH);
		$dataForDb['password'] = sha1($dataForDb['password'].$dataForDb['salt']);

		// Save the database
		$this->db[$dataForDb['username']] = $dataForDb;
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

}
