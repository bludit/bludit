<?php defined('BLUDIT') or die('Bludit CMS.');

class dbUsers extends dbJSON
{
	private $dbFields = array(
		'firstName'=>	array('inFile'=>false, 'value'=>''),
		'lastName'=>	array('inFile'=>false, 'value'=>''),
		'username'=>	array('inFile'=>false, 'value'=>''),
		'role'=>		array('inFile'=>false, 'value'=>''),
		'password'=>	array('inFile'=>false, 'value'=>''),
		'salt'=>		array('inFile'=>false, 'value'=>''),
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
			$user['username'] = $username;

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
		$username = Sanitize::html($args['username']);

		foreach($args as $field=>$value)
		{
			if( isset($this->dbFields[$field]) )
			{
				// Sanitize or not.
				if($this->dbFields[$field]['sanitize']=='html') {
					$tmpValue = Sanitize::html($value);
				}
				else {
					$tmpValue = $value;
				}

				$this->db[$username][$field] = $tmpValue;
			}
		}

		$this->save();

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
				// Sanitize or not.
				if($options['sanitize']=='html') {
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

			$dataForDb[$field] = $tmpValue;
		}

		// Check if the user alredy exists.
		if( $this->userExists($dataForDb['username']) ) {
			return false;
		}

		// The current unix time stamp.
		$dataForDb['registered'] = Date::unixTime();

		// Password
		$dataForDb['salt'] = helperText::randomText(8);
		$dataForDb['password'] = sha1($dataForDb['password'].$dataForDb['salt']);

		// Save the database
		$this->db[$dataForDb['username']] = $dataForDb;
		$this->save();

		return true;
	}

}
