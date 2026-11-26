<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'tab-content')); ?>

<div class="form-header">
	<div class="form-header-actions">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$user->username() ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php echo Bootstrap::pageTitle(array('title'=>$L->g('Change password'), 'icon'=>'user')); ?>
</div>

<div class="form-card">

<?php
	// Token CSRF
	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	// Username
	echo Bootstrap::formInputHidden(array(
		'name'=>'username',
		'value'=>$user->username()
	));

	// Username disabled
	echo Bootstrap::formInputText(array(
		'name'=>'usernameDisabled',
		'label'=>$L->g('Username'),
		'value'=>$user->username(),
		'class'=>'',
		'placeholder'=>'',
		'disabled'=>true,
		'tip'=>''
	));

	// Current password, only to change your own password
	if ($login->username() === $user->username()) {
		echo Bootstrap::formInputText(array(
			'name'=>'currentPassword',
			'label'=>$L->g('Current password'),
			'type'=>'password',
			'value'=>'',
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));
	}

	// New password
	echo Bootstrap::formInputText(array(
		'name'=>'newPassword',
		'label'=>$L->g('New password'),
		'type'=>'password',
		'value'=>'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	// Confirm password
	echo Bootstrap::formInputText(array(
		'name'=>'confirmPassword',
		'label'=>$L->g('Confirm new password'),
		'type'=>'password',
		'value'=>'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));
?>

</div>
<?php echo Bootstrap::formClose(); ?>