<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Add a new user'), 'icon'=>'person'));

echo Bootstrap::formOpen(array());

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo Bootstrap::formInputText(array(
		'name'=>'new_username',
		'label'=>$L->g('Username'),
		'value'=>(isset($_POST['new_username'])?$_POST['new_username']:''),
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'new_password',
		'type'=>'password',
		'label'=>$L->g('Password'),
		'value'=>'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'confirm_password',
		'type'=>'password',
		'label'=>$L->g('Confirm Password'),
		'value'=>'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formSelect(array(
		'name'=>'role',
		'label'=>$L->g('Role'),
		'options'=>array('editor'=>$L->g('Editor'), 'admin'=>$L->g('Administrator')),
		'selected'=>'Editor',
		'class'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'email',
		'label'=>$L->g('Email'),
		'value'=>(isset($_POST['email'])?$_POST['email']:''),
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo '
	<div class="form-group mt-4">
		<button type="submit" class="btn btn-primary mr-2" name="save">'.$L->g('Save').'</button>
		<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'users" role="button">'.$L->g('Cancel').'</a>
	</div>
	';

echo Bootstrap::formClose();