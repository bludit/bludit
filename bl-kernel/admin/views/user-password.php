<?php

HTML::title(array('title'=>$L->g('Change password'), 'icon'=>'key'));

HTML::formOpen(array('id'=>'edit-user-profile-form','class'=>'uk-form-horizontal'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	// Hidden field username
	HTML::formInputHidden(array(
		'name'=>'username',
		'value'=>$_user['username']
	));

	HTML::legend(array('value'=>$L->g('New password')));

	HTML::formInputText(array(
		'name'=>'usernameDisable',
		'label'=>$L->g('Username'),
		'value'=>$_user['username'],
		'class'=>'uk-width-1-2 uk-form-medium',
		'disabled'=>true,
		'tip'=>''
	));

	HTML::formInputPassword(array(
		'name'=>'new_password',
		'label'=>$L->g('New password'),
		'value'=>'',
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	HTML::formInputPassword(array(
		'name'=>'confirm_password',
		'label'=>$L->g('Confirm password'),
		'value'=>'',
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>''
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<a href="'.HTML_PATH_ADMIN_ROOT.'edit-user/'.$_user['username'].'" class="uk-button">'.$L->g('Cancel').'</a>
		</div>
	</div>';

HTML::formClose();

?>
