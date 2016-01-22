<?php

HTML::title(array('title'=>$L->g('Add a new user'), 'icon'=>'user-plus'));

HTML::formOpen(array('id'=>'add-user-form', 'class'=>'uk-form-horizontal'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	HTML::formInputText(array(
		'name'=>'new_username',
		'label'=>$L->g('Username'),
		'value'=>(isset($_POST['new_username'])?$_POST['new_username']:''),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputPassword(array(
		'name'=>'new_password',
		'label'=>$L->g('Password'),
		'value'=>'',
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputPassword(array(
		'name'=>'confirm_password',
		'label'=>$L->g('Confirm Password'),
		'value'=>'',
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formSelect(array(
		'name'=>'role',
		'label'=>$L->g('Role'),
		'options'=>array('editor'=>$L->g('Editor'), 'admin'=>$L->g('Administrator')),
		'selected'=>'editor',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'email',
		'label'=>$L->g('Email'),
		'value'=>(isset($_POST['email'])?$_POST['email']:''),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<a href="'.HTML_PATH_ADMIN_ROOT.'users" class="uk-button">'.$L->g('Cancel').'</a>
		</div>
	</div>';

HTML::formClose();
