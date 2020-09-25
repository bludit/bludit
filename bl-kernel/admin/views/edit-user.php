<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'tab-content')); ?>

<div class="align-middle">
	<div class="float-right mt-1">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php echo Bootstrap::pageTitle(array('title'=>$L->g('Edit user'), 'icon'=>'user')); ?>
</div>

<!-- TABS -->
<nav class="mb-3">
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"><?php $L->p('Profile') ?></a>
		<a class="nav-item nav-link" id="nav-picture-tab" data-toggle="tab" href="#picture" role="tab" aria-controls="nav-picture" aria-selected="false"><?php $L->p('Profile picture') ?></a>
		<a class="nav-item nav-link" id="nav-security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="nav-security" aria-selected="false"><?php $L->p('Security') ?></a>
		<a class="nav-item nav-link" id="nav-social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="nav-social" aria-selected="false"><?php $L->p('Social Networks') ?></a>
	</div>
</nav>

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
				'options'=>array('author'=>$L->g('Author'), 'editor'=>$L->g('Editor'), 'admin'=>$L->g('Administrator')),
				'selected'=>$user->role(),
				'class'=>'',
				'tip'=>$L->g('author-can-write-and-edit-their-own-content')
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
	?>
	</div>

	<!-- Profile picture tab -->
	<div class="tab-pane fade" id="picture" role="tabpanel" aria-labelledby="nav-picture-tab">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-sm-12 p-0 pr-2">
					<div class="custom-file">
						<input type="file" class="custom-file-input" id="jsprofilePictureInputFile" name="profilePictureInputFile">
						<label class="custom-file-label" for="jsprofilePictureInputFile"><?php $L->p('Upload image'); ?></label>
					</div>
					<!-- <button id="jsbuttonRemovePicture" type="button" class="btn btn-primary w-100 mt-4 mb-4"><i class="fa fa-trash"></i> Remove picture</button> -->
				</div>
				<div class="col-lg-8 col-sm-12 p-0 text-center">
					<img id="jsprofilePicturePreview" class="img-fluid img-thumbnail" alt="Profile picture preview" src="<?php echo (Sanitize::pathFile(PATH_UPLOADS_PROFILES.$user->username().'.png')?DOMAIN_UPLOADS_PROFILES.$user->username().'.png?version='.time():HTML_PATH_CORE_IMG.'default.svg') ?>" />
				</div>
			</div>
		</div>
		<script>
		// $("#jsbuttonRemovePicture").on("click", function() {
		// 	var username = $("#jsusername").val();
		// 	bluditAjax.removeProfilePicture(username);
		// 	$("#jsprofilePicturePreview").attr("src", "<?php echo HTML_PATH_CORE_IMG.'default.svg' ?>");
		// });

		$("#jsprofilePictureInputFile").on("change", function() {
			var formData = new FormData();
			formData.append('tokenCSRF', tokenCSRF);
			formData.append('profilePictureInputFile', $(this)[0].files[0]);
			formData.append('username', $("#jsusername").val());
			$.ajax({
				url: HTML_PATH_ADMIN_ROOT+"ajax/profile-picture-upload",
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false
			}).done(function(data) {
				if (data.status==0) {
					$("#jsprofilePicturePreview").attr('src',data.absoluteURL+"?time="+Math.random());
				} else {
					showAlert(data.message);
				}
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

		if (checkRole(array('admin'),false)) {
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
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'facebook',
			'label'=>'Facebook',
			'value'=>$user->facebook(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'codepen',
			'label'=>'CodePen',
			'value'=>$user->codepen(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'instagram',
			'label'=>'Instagram',
			'value'=>$user->instagram(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'gitlab',
			'label'=>'GitLab',
			'value'=>$user->gitlab(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'github',
			'label'=>'GitHub',
			'value'=>$user->github(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'linkedin',
			'label'=>'LinkedIn',
			'value'=>$user->linkedin(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'xing',
			'label'=>'Xing',
			'value'=>$user->xing(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'mastodon',
			'label'=>'Mastodon',
			'value'=>$user->mastodon(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formInputText(array(
			'name'=>'vk',
			'label'=>'VK',
			'value'=>$user->vk(),
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));
	?>
	</div>
</div>

<?php echo Bootstrap::formClose(); ?>

<script>
	// Open current tab after refresh page
	$(function() {
		$('a[data-toggle="tab"]').on('click', function(e) {
			window.localStorage.setItem('activeTab', $(e.target).attr('href'));
			console.log($(e.target).attr('href'));
		});
		var activeTab = window.localStorage.getItem('activeTab');
		if (activeTab) {
			$('#nav-tab a[href="' + activeTab + '"]').tab('show');
			//window.localStorage.removeItem("activeTab");
		}
	});
</script>