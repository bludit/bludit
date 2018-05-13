<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Edit user'), 'icon'=>'person'));

echo Bootstrap::formOpen(array());

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	echo Bootstrap::formInputHidden(array(
		'name'=>'username',
		'value'=>$user->username()
	));

	echo Bootstrap::formInputText(array(
		'name'=>'usernameDisabled',
		'label'=>$L->g('Username'),
		'value'=>$user->username(),
		'class'=>'',
		'placeholder'=>'',
		'disabled'=>true,
		'tip'=>''
	));

	if ($Login->role()==='admin') {
		echo Bootstrap::formSelect(array(
			'name'=>'role',
			'label'=>$L->g('Role'),
			'options'=>array('editor'=>$L->g('Editor'), 'admin'=>$L->g('Administrator')),
			'selected'=>$user->role(),
			'class'=>'',
			'tip'=>''
		));
	}

	echo Bootstrap::formInputText(array(
		'name'=>'email',
		'label'=>$L->g('Email'),
		'value'=>$user->email(),
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formTitle(array('title'=>$L->g('Authentication Token')));

	echo Bootstrap::formInputText(array(
		'name'=>'tokenAuth',
		'label'=>$L->g('Token'),
		'value'=>$user->tokenAuth(),
		'class'=>'',
		'tip'=>$L->g('this-token-is-similar-to-a-password-it-should-not-be-shared')
	));

	echo Bootstrap::formTitle(array('title'=>$L->g('Status')));

	echo Bootstrap::formInputText(array(
		'name'=>'status',
		'label'=>$L->g('Current status'),
		'value'=>$user->enabled()?$L->g('Enabled'):$L->g('Disabled'),
		'class'=>'',
		'disabled'=>true,
		'tip'=>$user->enabled()?'':$L->g('To enable the user you must set a new password')
	));

	if ($user->enabled()) {
		echo '
		<div class="form-group row">
		<div class="col-sm-2"></div>
		<div class="col-sm-10">
			<button type="submit" class="btn btn-primary mr-2" id="jsdisableUser" name="disableUser">'.$L->g('Disable user').'</button>
			<button type="submit" class="btn btn-danger mr-2" id="jsdeleteUserAndKeepContent" name="deleteUserAndKeepContent">'.$L->g('Delete user and keep content').'</button>
			<button type="submit" class="btn btn-danger mr-2" id="jsdeleteUserAndDeleteContent" name="deleteUserAndDeleteContent">'.$L->g('Delete user and delete content !!!').'</button>
		</div>
		</div>
		';
	}

	echo Bootstrap::formTitle(array('title'=>$L->g('Social Networks')));

	echo Bootstrap::formInputText(array(
		'name'=>'twitter',
		'label'=>'Twitter',
		'value'=>$user->twitter(),
		'class'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'facebook',
		'label'=>'Facebook',
		'value'=>$user->facebook(),
		'class'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'codepen',
		'label'=>'Codepen',
		'value'=>$user->codepen(),
		'class'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'googlePlus',
		'label'=>'Google+',
		'value'=>$user->googlePlus(),
		'class'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'instagram',
		'label'=>'Instagram',
		'value'=>$user->instagram(),
		'class'=>'',
		'tip'=>''
	));

	echo '
	<div class="form-group mt-4">
		<button type="submit" class="btn btn-primary mr-2" name="save">'.$L->g('Save').'</button>
		<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'users" role="button">'.$L->g('Cancel').'</a>
	</div>
	';

echo Bootstrap::formClose();

?>

<script>
$(document).ready(function() {

	$("#jsdeleteUserAndDeleteContent").click(function() {
		if(confirm("<?php $Language->p('Confirm delete this action cannot be undone') ?>")==false) {
			return false;
		}
	});

	$("#jsdeleteUserAndKeepContent").click(function() {
		if(confirm("<?php $Language->p('Confirm delete this action cannot be undone') ?>")==false) {
			return false;
		}
	});

});
</script>