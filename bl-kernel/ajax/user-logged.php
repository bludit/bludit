<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
 *
 * This script check if the user is logged
 *
 */

// Check UUID
if ($login->isLogged()) {
	exit (json_encode(array(
		'status'=>1,
		'message'=>'The user is logged.'
	)));
}

exit (json_encode(array(
	'status'=>0,
	'message'=>'The user is NOT logged.'
)));


?>