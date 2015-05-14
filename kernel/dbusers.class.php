<?php defined('BLUDIT') or die('Bludit CMS.');

class dbUsers extends dbJSON
{
	private $dbFields = array(
		'firstName'=>	array('inFile'=>false, 'value'=>''),
		'lastName'=>	array('inFile'=>false, 'value'=>''),
		'username'=>	array('inFile'=>false, 'value'=>''),
		'role'=>		array('inFile'=>false, 'value'=>'editor'),
		'password'=>	array('inFile'=>false, 'value'=>''),
		'salt'=>		array('inFile'=>false, 'value'=>'!Pink Floyd!Welcome to the machine!'),
		'email'=>		array('inFile'=>false, 'value'=>''),
		'registered'=>	array('inFile'=>false, 'value'=>0)
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'users.php');
	}

	// Return an array with the username databases
	public function get($username)
	{
		if($this->userExists($username))
		{
			$user = $this->db[$username];

			return $user;
		}

		return false;
	}

	// Return TRUE if the user exists, FALSE otherwise.
	public function userExists($username)
	{
		return isset($this->db[$username]);
	}

	public function getAll()
	{
		return $this->db;
	}

	public function set($args)
	{
		$dataForDb = array();

		$user = $this->get($args['username']);

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
				// Sanitize if will be saved on database.
				$tmpValue = Sanitize::html($value);

				// Set type
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

		// The current unix time stamp.
		$dataForDb['registered'] = Date::unixTime();

		// Password
		$dataForDb['salt'] = helperText::randomText(SALT_LENGTH);
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
