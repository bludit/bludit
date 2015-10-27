<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================

function editUser($args)
{
	global $dbUsers;
	global $Language;

	if( $dbUsers->set($args) ) {
		Alert::set($Language->g('The changes have been saved'));
	}
	else {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to edit the user.');
	}
}

function setPassword($username, $new_password, $confirm_password)
{
	global $dbUsers;
	global $Language;

	if( ($new_password===$confirm_password) && !Text::isEmpty($new_password) )
	{
		if( $dbUsers->setPassword($username, $new_password) ) {
			Alert::set($Language->g('The changes have been saved'));
		}
		else {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to change the user password.');
		}
	}
	else {
		Alert::set($Language->g('The password and confirmation password do not match'));
		return false;
	}
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
		Alert::set($Language->g('User deleted'));
	}
	else {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the user.');
	}
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
	elseif( !empty($_POST['new-password']) && !empty($_POST['confirm-password']) ) {
		setPassword($_POST['username'], $_POST['new-password'], $_POST['confirm-password']);
		editUser($_POST);
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

$_user = $dbUsers->getDb($layout['parameters']);

// If the user doesn't exist, redirect to the users list.
if($_user===false) {
	Redirect::page('admin', 'users');
}

$_user['username'] = $layout['parameters'];
