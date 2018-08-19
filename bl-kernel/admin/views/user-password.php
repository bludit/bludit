<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Change password'), 'icon'=>'person'));

echo Bootstrap::formOpen(array());

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
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

	echo Bootstrap::formInputText(array(
		'name'=>'newPassword',
		'label'=>$L->g('New password'),
		'type'=>'password',
		'value'=>'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'confirmPassword',
		'label'=>$L->g('Confirm new password'),
		'type'=>'password',
		'value'=>'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo '
	<div class="form-group mt-4">
		<button type="submit" class="btn btn-primary mr-2" name="save">'.$L->g('Save').'</button>
		<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$user->username().'" role="button">'.$L->g('Cancel').'</a>
	</div>
	';

echo Bootstrap::formClose();

?>