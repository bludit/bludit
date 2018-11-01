<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::pageTitle(array('title'=>$L->g('Edit user'), 'icon'=>'person')); ?>

<nav class="mb-3">
<div class="nav nav-tabs" id="nav-tab" role="tablist">
	<a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</a>
	<a class="nav-item nav-link" id="nav-picture-tab" data-toggle="tab" href="#picture" role="tab" aria-controls="nav-picture" aria-selected="false">Profile picture</a>
	<a class="nav-item nav-link" id="nav-security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="nav-security" aria-selected="false">Security</a>
	<a class="nav-item nav-link" id="nav-social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="nav-social" aria-selected="false">Social Networks</a>
</div>
</nav>

<?php
	// Start form
	echo Bootstrap::formOpen(array(
		'id'=>'jsform',
		'class'=>''
	));

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
?>

<div class="tab-content" id="nav-tabContent">
	<!-- Profile tab -->
	<div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="nav-profile-tab">
	<?php
		// Display username but disable the field
		echo Bootstrap::formInputText(array(
			'name'=>'usernameDisabled',
			'label'=>$L->g('Username'),
			'value'=>$user->username(),
			'class'=>'',
			'placeholder'=>'',
			'disabled'=>true,
			'tip'=>''
		));

		if ($login->role()==='admin') {
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

		echo Bootstrap::formInputText(array(
			'name'=>'nickname',
			'label'=>$L->g('Nickname'),
			'value'=>$user->nickname(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>$L->g('The nickname is almost used in the themes to display the author of the content')
		));

		echo Bootstrap::formInputText(array(
			'name'=>'firstName',
			'label'=>$L->g('First Name'),
			'value'=>$user->firstName(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'lastName',
			'label'=>$L->g('Last Name'),
			'value'=>$user->lastName(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo '
		<div class="form-group mt-4">
			<button type="submit" class="btn btn-primary mr-2" name="save">'.$L->g('Save').'</button>
			<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'dashboard" role="button">'.$L->g('Cancel').'</a>
		</div>
		';
	?>
	</div>

	<!-- Profile picture tab -->
	<div class="tab-pane fade" id="picture" role="tabpanel" aria-labelledby="nav-picture-tab">
		<div>
			<img id="jscoverImagePreview" class="d-block w-50" alt="Profile picture preview" src="<?php echo HTML_PATH_ADMIN_THEME_IMG ?>default.svg" />
		</div>
		<div class="mt-2">
			<button type="button" id="jsbuttonSelectCoverImage" class="btn btn-primary btn-sm"><?php echo $L->g('Select cover image') ?></button>
			<button type="button" id="jsbuttonRemoveCoverImage" class="btn btn-secondary btn-sm"><?php echo $L->g('Remove cover image') ?></button>
		</div>
		<script>
			$(document).ready(function() {
				$("#jscoverImagePreview").on("click", function() {
					openMediaManager();
				});

				$("#jsbuttonSelectCoverImage").on("click", function() {
					openMediaManager();
				});

				$("#jsbuttonRemoveCoverImage").on("click", function() {
					$("#jscoverImage").val('');
					$("#jscoverImagePreview").attr('src', HTML_PATH_ADMIN_THEME_IMG+'default.svg');
				});
			});
		</script>
	</div>

	<!-- Security tab -->
	<div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="nav-security-tab">
	<?php
		echo Bootstrap::formTitle(array('title'=>$L->g('Password')));

		echo '
		<div class="form-group">
			<a href="'.HTML_PATH_ADMIN_ROOT.'user-password/'.$user->username().'" class="btn btn-primary mr-2">'.$L->g('Change password').'</a>
		</div>
		';

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
				<button type="submit" class="btn btn-warning mr-2" id="jsdisableUser" name="disableUser">'.$L->g('Disable user').'</button>
				<button type="submit" class="btn btn-danger mr-2" id="jsdeleteUserAndKeepContent" name="deleteUserAndKeepContent">'.$L->g('Delete user and keep content').'</button>
				<button type="submit" class="btn btn-danger mr-2" id="jsdeleteUserAndDeleteContent" name="deleteUserAndDeleteContent">'.$L->g('Delete user and delete content').'</button>
			</div>
			</div>
			';
		}
	?>
	</div>

	<!-- Social Networks tab -->
	<div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="nav-social-tab">
	<?php
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
			'name'=>'instagram',
			'label'=>'Instagram',
			'value'=>$user->instagram(),
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
			'name'=>'linkedin',
			'label'=>'Linkedin',
			'value'=>$user->linkedin(),
			'class'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'github',
			'label'=>'Github',
			'value'=>$user->github(),
			'class'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'gitlab',
			'label'=>'Gitlab',
			'value'=>$user->gitlab(),
			'class'=>'',
			'tip'=>''
		));

		echo '
		<div class="form-group mt-4">
			<button type="submit" class="btn btn-primary mr-2" name="save">'.$L->g('Save').'</button>
			<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'dashboard" role="button">'.$L->g('Cancel').'</a>
		</div>
		';
	?>
	</div>
</div>