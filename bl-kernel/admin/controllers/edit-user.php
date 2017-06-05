<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================

function disableUser($username) {

	global $dbUsers;
	global $Language;
	global $Login;

	// The editors can't disable users
	if($Login->role()!=='admin') {
		return false;
	}

	if( $dbUsers->disableUser($username) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'user-disabled',
			'notes'=>$username
		));

		// Create an alert
		Alert::set($Language->g('The changes have been saved'));
	}

	return true;
}

function editUser($args)
{
	global $dbUsers;
	global $Language;

	if( $dbUsers->set($args) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'user-edited',
			'notes'=>$args['username']
		));

		// Create an alert
		Alert::set($Language->g('The changes have been saved'));
	}

	return true;
}

function deleteUser($args, $deleteContent=false)
{
	global $dbUsers;
	global $dbPosts;
	global $Language;
	global $Login;

	// The user admin cannot be deleted.
	if($args['username']=='admin') {
		return false;
	}

	// The editors cannot delete users.
	if($Login->role()!=='admin') {
		return false;
	}

	if($deleteContent) {
		$dbPosts->deletePostsByUser($args['username']);
	}
	else {
		$dbPosts->linkPostsToUser($args['username'], 'admin');
	}

	if( $dbUsers->delete($args['username']) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'user-deleted',
			'notes'=>$args['username']
		));

		// Create an alert
		Alert::set($Language->g('User deleted'));
	}

	return true;
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	// Prevent editors to administrate other users.
	if($Login->role()!=='admin')
	{
		$_POST['username'] = $Login->username();
		unset($_POST['role']);
	}

	if(isset($_POST['delete-user-all'])) {
		deleteUser($_POST, true);
	}
	elseif(isset($_POST['delete-user-associate'])) {
		deleteUser($_POST, false);
	}
	elseif(isset($_POST['disable-user'])) {
		disableUser($_POST['username']);
	}
	else {
		editUser($_POST);
	}
}

// ============================================================================
// Main after POST
// ============================================================================

if($Login->role()!=='admin') {
	$layout['parameters'] = $Login->username();
}

$_User = $dbUsers->getUser($layout['parameters']);

// If the user doesn't exist, redirect to the users list.
if($_User===false) {
	Redirect::page('users');
}
