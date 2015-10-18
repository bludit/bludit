<?php

HTML::title(array('title'=>$L->g('Edit user'), 'icon'=>'user'));

HTML::formOpen(array('class'=>'uk-form-horizontal'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'token',
		'value'=>$Security->getToken()
	));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'username',
		'value'=>$_user['username']
	));

	HTML::legend(array('value'=>$L->g('Profile')));

	HTML::formInputText(array(
		'name'=>'firstName',
		'label'=>$L->g('First name'),
		'value'=>$_user['firstName'],
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'lastName',
		'label'=>$L->g('Last name'),
		'value'=>$_user['lastName'],
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

if($Login->username()==='admin') {

	HTML::formSelect(array(
		'name'=>'role',
		'label'=>$L->g('Role'),
		'options'=>array('editor'=>$L->g('Editor'), 'admin'=>$L->g('Administrator')),
		'selected'=>$_user['role'],
		'tip'=>''
	));

}

	HTML::formInputText(array(
		'name'=>'email',
		'label'=>$L->g('Email'),
		'value'=>$_user['email'],
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('email-will-not-be-publicly-displayed')
	));

	HTML::legend(array('value'=>$L->g('Change password')));

	HTML::formInputPassword(array(
		'name'=>'new-password',
		'label'=>$L->g('New password'),
		'value'=>'',
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputPassword(array(
		'name'=>'confirm-password',
		'label'=>$L->g('Confirm Password'),
		'value'=>'',
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<a href="'.HTML_PATH_ADMIN_ROOT.'users" class="uk-button">'.$L->g('Cancel').'</a>
		</div>
	</div>';

if($Login->username()!=='admin') {

	HTML::legend(array('value'=>$L->g('Delete')));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" id="jsdelete-user-associate" class="delete-button" name="delete-user-associate">'.$L->g('Delete the user and associate its posts to admin user').'</button>
		<button type="submit" id="jsdelete-user-all" class="delete-button" name="delete-user-all">'.$L->g('Delete the user and all its posts').'</button>
		</div>
	</div>';

}

HTML::formClose();

?>

<script>

$(document).ready(function() {

	$("#jsdelete-user-associate").click(function() {
		if(confirm("<?php $Language->p('confirm-delete-this-action-cannot-be-undone') ?>")==false) {
			return false;
		}
	});

	$("#jsdelete-user-all").click(function() {
		if(confirm("<?php $Language->p('confirm-delete-this-action-cannot-be-undone') ?>")==false) {
			return false;
		}
	});

});

</script>