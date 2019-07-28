<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'tab-content')); ?>

<div class="align-middle">
	<div class="float-right mt-1">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php echo Bootstrap::pageTitle(array('title'=>$L->g('Add a new user'), 'icon'=>'user')); ?>
</div>

<?php
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
		'options'=>array('author'=>$L->g('Author'), 'editor'=>$L->g('Editor'), 'admin'=>$L->g('Administrator')),
		'selected'=>'Author',
		'class'=>'',
		'tip'=>$L->g('author-can-write-and-edit-their-own-content')
	));

	echo Bootstrap::formInputText(array(
		'name'=>'email',
		'label'=>$L->g('Email'),
		'value'=>(isset($_POST['email'])?$_POST['email']:''),
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));
?>

<?php echo Bootstrap::formClose(); ?>