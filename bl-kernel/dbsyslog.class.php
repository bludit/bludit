<?php defined('BLUDIT') or die('Bludit CMS.');

class dbSyslog extends dbJSON
{
	public $dbFields = array(
		'date'=>		array('inFile'=>false, 'value'=>''),
		'dictionaryKey'=>	array('inFile'=>false, 'value'=>''),
		'notes'=>		array('inFile'=>false, 'value'=>''),
		'username'=>		array('inFile'=>false, 'value'=>''),
		'idExecution'=>		array('inFile'=>false, 'value'=>''),
		'method'=>		array('inFile'=>false, 'value'=>'')
	);

	function __construct()
	{
		parent::__construct(DB_SYSLOG);
	}

	// Returns TRUE if the ID of execution exists, FALSE otherwise
	public function exists($idExecution)
	{
		foreach($this->db as $field) {
			if( $field['idExecution']==$idExecution ) {
				return true;
			}
		}
		return false;
	}

	public function get($idExecution)
	{
		foreach($this->db as $field) {
			if( $field['idExecution']==$idExecution ) {
				return $field;
			}
		}
		return false;
	}

	public function add($args)
	{
		global $Language;

		$data = array();
		$data['date'] = Date::current(DB_DATE_FORMAT);
		$data['dictionaryKey'] = $args['dictionaryKey'];
		$data['notes'] = Sanitize::html($args['notes']);
		$data['idExecution'] = $GLOBALS['ID_EXECUTION'];
		$data['method'] = $_SERVER['REQUEST_METHOD'];

		// Username
		$data['username'] = Session::get('username');
		if( Text::isEmpty($data['username']) ) {
			return false;
		}

		// Insert at beggining of the database
		array_unshift($this->db, $data);

		// Keep just NOTIFICATIONS_AMOUNT notifications
		$this->db = array_slice($this->db, 0, NOTIFICATIONS_AMOUNT);

		// Save
		return $this->save();
	}
}