<?php

HTML::title(array('title'=>$L->g('Edit user'), 'icon'=>'user'));

echo '<div class="uk-grid">';
echo '<div class="uk-width-7-10">';

HTML::formOpen(array('id'=>'edit-user-profile-form','class'=>'uk-form-horizontal'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'username',
		'value'=>$_User->username()
	));

	HTML::legend(array('value'=>$L->g('Profile'), 'class'=>'first-child'));

	HTML::formInputText(array(
		'name'=>'usernameDisable',
		'label'=>$L->g('Username'),
		'value'=>$_User->username(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'disabled'=>true,
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'firstName',
		'label'=>$L->g('First name'),
		'value'=>$_User->firstName(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'lastName',
		'label'=>$L->g('Last name'),
		'value'=>$_User->lastName(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	echo '<div class="uk-form-row">
		<label class="uk-form-label">Password</label>
		<div class="uk-form-controls">
		<a href="'.HTML_PATH_ADMIN_ROOT.'user-password/'.$_User->username().'">'.$L->g('Change password').'</a>
		</div>
	</div>';

if($Login->role()==='admin') {

	HTML::formSelect(array(
		'name'=>'role',
		'label'=>$L->g('Role'),
		'options'=>array('editor'=>$L->g('Editor'), 'admin'=>$L->g('Administrator')),
		'selected'=>$_User->role(),
		'tip'=>''
	));

}
	HTML::formInputText(array(
		'name'=>'email',
		'label'=>$L->g('Email'),
		'value'=>$_User->email(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('email-will-not-be-publicly-displayed')
	));

	HTML::legend(array('value'=>'Social networks'));

	HTML::formInputText(array(
		'name'=>'twitterUsername',
		'label'=>'Twitter username',
		'value'=>$_User->twitterUsername(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'facebookUsername',
		'label'=>'Facebook username',
		'value'=>$_User->facebookUsername(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'googleUsername',
		'label'=>'Google username',
		'value'=>$_User->googleUsername(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'instagramUsername',
		'label'=>'Instagram username',
		'value'=>$_User->instagramUsername(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<a href="'.HTML_PATH_ADMIN_ROOT.'users" class="uk-button">'.$L->g('Cancel').'</a>
		</div>
	</div>';

if( ($Login->role()==='admin') && ($_User->username()!='admin') ) {

	HTML::legend(array('value'=>$L->g('Delete')));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" id="jsdelete-user-associate" class="delete-button" name="delete-user-associate"><i class="uk-icon-ban"></i> '.$L->g('Delete the user and associate its posts to admin user').'</button>
		<button type="submit" id="jsdelete-user-all" class="delete-button" name="delete-user-all"><i class="uk-icon-ban"></i> '.$L->g('Delete the user and all its posts').'</button>
		</div>
	</div>';

}

HTML::formClose();

echo '</div>';

echo '<div class="uk-width-3-10" style="margin-top: 50px; text-align: center;">';

HTML::profileUploader($_User->username());

echo '</div>';
echo '</div>';

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