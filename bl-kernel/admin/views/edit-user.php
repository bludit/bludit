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
		'value'=>$User->username()
	));

	HTML::legend(array('value'=>$L->g('Profile'), 'class'=>'first-child'));

	HTML::formInputText(array(
		'name'=>'usernameDisable',
		'label'=>$L->g('Username'),
		'value'=>$User->username(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'disabled'=>true,
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'firstName',
		'label'=>$L->g('First name'),
		'value'=>$User->firstName(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'lastName',
		'label'=>$L->g('Last name'),
		'value'=>$User->lastName(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	echo '<div class="uk-form-row">
		<label class="uk-form-label">'.$L->g('password').'</label>
		<div class="uk-form-controls">
		<a href="'.HTML_PATH_ADMIN_ROOT.'user-password/'.$User->username().'">'.$L->g('Change password').'</a>
		</div>
	</div>';

if($Login->role()==='admin') {

	HTML::formSelect(array(
		'name'=>'role',
		'label'=>$L->g('Role'),
		'options'=>array('editor'=>$L->g('Editor'), 'admin'=>$L->g('Administrator')),
		'selected'=>$User->role(),
		'tip'=>''
	));

}

	HTML::formInputText(array(
		'name'=>'email',
		'label'=>$L->g('Email'),
		'value'=>$User->email(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('Recommended for recovery password and notifications')
	));

	HTML::legend(array('value'=>$L->g('Social networks links')));

	HTML::formInputText(array(
		'name'=>'twitter',
		'label'=>'Twitter',
		'value'=>$User->twitter(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'facebook',
		'label'=>'Facebook',
		'value'=>$User->facebook(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'codepen',
		'label'=>'Codepen',
		'value'=>$User->codepen(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'googlePlus',
		'label'=>'Google+',
		'value'=>$User->googlePlus(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'instagram',
		'label'=>'Instagram',
		'value'=>$User->instagram(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<a href="'.HTML_PATH_ADMIN_ROOT.'users" class="uk-button">'.$L->g('Cancel').'</a>
		</div>
	</div>';

	HTML::legend(array('value'=>$L->g('Authentication Token')));

	HTML::formInputText(array(
		'name'=>'tokenAuth',
		'label'=>$L->g('Token'),
		'value'=>$User->tokenAuth(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('this-token-is-similar-to-a-password-it-should-not-be-shared')
	));

	HTML::legend(array('value'=>$L->g('Status')));

	HTML::formInputText(array(
		'name'=>'status',
		'label'=>$L->g('Current status'),
		'value'=>$User->enabled()?$L->g('Enabled'):$L->g('Disabled'),
		'class'=>'uk-width-1-2 uk-form-medium',
		'disabled'=>true,
		'tip'=>$User->enabled()?'':$L->g('To enable the user you must set a new password')
	));

if( $User->enabled() ) {
	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" id="jsdisable-user" class="delete-button" name="disable-user"><i class="uk-icon-ban"></i> '.$L->g('Disable the user').'</button>
		</div>
	</div>';
}

if( ($Login->role()==='admin') && ($User->username()!='admin') ) {

	HTML::legend(array('value'=>$L->g('Delete')));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" id="jsdelete-user-associate" class="delete-button" name="delete-user-associate"><i class="uk-icon-ban"></i> '.$L->g('Delete the user and associate his pages to admin user').'</button>
		<button type="submit" id="jsdelete-user-all" class="delete-button" name="delete-user-all"><i class="uk-icon-ban"></i> '.$L->g('Delete the user and all his pages').'</button>
		</div>
	</div>';

}

HTML::formClose();

echo '</div>';

echo '<div class="uk-width-3-10" style="margin-top: 50px; text-align: center;">';

HTML::profileUploader($User->username());

echo '</div>';
echo '</div>';

?>

<script>

$(document).ready(function() {

	$("#jsdelete-user-associate").click(function() {
		if(confirm("<?php $Language->p('Confirm delete this action cannot be undone') ?>")==false) {
			return false;
		}
	});

	$("#jsdelete-user-all").click(function() {
		if(confirm("<?php $Language->p('Confirm delete this action cannot be undone') ?>")==false) {
			return false;
		}
	});

	$("#jsdisable-user").click(function() {
		if(confirm("<?php $Language->p('Do you want to disable the user') ?>")==false) {
			return false;
		}
	});

});

</script>