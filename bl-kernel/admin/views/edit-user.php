<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	function changePassword() {
		var newPassword = $('#newPassword').val();
		var confirmPassword = $('#confirmPassword').val();

		if (newPassword.length < PASSWORD_LENGTH) {
			showAlertError("<?php $L->p('Password must be at least 6 characters long') ?>");
			return false;
		}

		if (newPassword !== confirmPassword) {
			showAlertError("<?php $L->p('The password and confirmation password do not match') ?>");
			return false;
		}

		bootbox.confirm({
			message: '<?php $L->p('Are you sure you want to change the password') ?>',
			buttons: {
				cancel: {
					label: '<i class="fa fa-times"></i><?php $L->p('Cancel') ?>',
					className: 'btn-sm btn-secondary'
				},
				confirm: {
					label: '<i class="fa fa-check"></i><?php $L->p('Confirm') ?>',
					className: 'btn-sm btn-primary'
				}
			},
			closeButton: false,
			callback: function(result) {
				if (result) {
					// The user accepted the action to change the password
					var args = {
						username: $('#username').val(),
						password: $('#newPassword').val()
					};
					api.editUser(args).then(function(response) {
						if (response.status == 0) {
							logs('User password changed. Username: ' + response.data.key);
							showAlertInfo("<?php $L->p('The changes have been saved') ?>");
						} else {
							logs('An error occurred while trying to change the user password.');
							showAlertError(response.message);
						}
					});
				}
				$('#newPassword').val('');
				$('#confirmPassword').val('');
				return true;
			}
		});
	}

	function save() {
		let args = {
			username: $('#username').val(),
			role: $('#role').val()
		};

		$('input[data-save="true"]').each(function() {
			var key = $(this).attr('name');
			var value = $(this).val();
			args[key] = value;
		});

		$('select[data-save="true"]').each(function() {
			var key = $(this).attr('name');
			var value = $(this).val();
			args[key] = value;
		});

		$('textarea[data-save="true"]').each(function() {
			var key = $(this).attr('name');
			var value = $(this).val();
			args[key] = value;
		});

		api.editUser(args).then(function(response) {
			if (response.status == 0) {
				logs('User edited. Username: ' + response.data.key);
				showAlertInfo("<?php $L->p('The changes have been saved') ?>");
			} else {
				logs('An error occurred while trying to edit the user.');
				showAlertError(response.message);
			}
		});
	}

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {

		$('#btnSave').on('click', function() {
			// Change the password if the user write a new one in the input
			if ($('#newPassword').val()) {
				changePassword();
			} else {
				// Save the edited fields
				save();
			}
		});

		$('#inputProfilePicture').on("change", function(e) {
			var inputProfilePicture = $('#inputProfilePicture')[0].files;
			var username = $('#username').val();

			var formData = new FormData();
			formData.append("file", inputProfilePicture[0]);
			formData.append("token", api.body.token);
			formData.append("authentication", api.body.authentication);
			$.ajax({
				url: api.apiURL + 'users/picture/' + username,
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				xhr: function() {
					var xhr = $.ajaxSettings.xhr();
					if (xhr.upload) {
						xhr.upload.addEventListener("progress", function(e) {
							if (e.lengthComputable) {
								var percentComplete = (e.loaded / e.total) * 100;
								logs('Uploading profile image: ' + percentComplete + '%');
							}
						}, false);
					}
					return xhr;
				}
			}).done(function(response) {
				logs(response);
				if (response.status == 0) {
					logs("Profile picture uploaded.");
					showAlertInfo("<?php $L->p('The changes have been saved') ?>");
					$('#profilePicturePreview').attr('src', response.data.absoluteURL);
				} else {
					logs("An error occurred while trying to upload the profile picture.");
					showAlertError(response.message);
				}
			});
			return true;
		});

		$('#btnRemoveProfilePicture').on('click', function() {
			var username = $('#username').val();
			logs('Deleting profile picture. Username: ' + username);
			bootbox.confirm({
				message: '<?php $L->p('Are you sure you want to delete the profile picture') ?>',
				buttons: {
					cancel: {
						label: '<i class="fa fa-times"></i><?php $L->p('Cancel') ?>',
						className: 'btn-sm btn-secondary'
					},
					confirm: {
						label: '<i class="fa fa-check"></i><?php $L->p('Confirm') ?>',
						className: 'btn-sm btn-primary'
					}
				},
				closeButton: false,
				callback: function(result) {
					if (result) {
						var args = {
							username: username
						};
						api.deleteProfilePicture(args).then(function(response) {
							if (response.status == 0) {
								logs('Profile picture deleted. Username: ' + response.data.key);
								showAlertInfo("<?php $L->p('The changes have been saved') ?>");
								$('#profilePicturePreview').attr('src', '<?php echo HTML_PATH_CORE_IMG . 'default.svg' ?>');
							} else {
								logs("An error occurred while trying to delete the profile picture.");
								showAlertError(response.message);
							}
						});
						return true;
					}
				}
			});
		});

		$('#btnDisableUser').on('click', function() {
			var username = $('#username').val();
			logs('Disabling user. Username: ' + username);
			bootbox.confirm({
				message: '<?php $L->p('Are you sure you want to disable this user') ?>',
				buttons: {
					cancel: {
						label: '<i class="fa fa-times"></i><?php $L->p('Cancel') ?>',
						className: 'btn-sm btn-secondary'
					},
					confirm: {
						label: '<i class="fa fa-check"></i><?php $L->p('Confirm') ?>',
						className: 'btn-sm btn-primary'
					}
				},
				closeButton: false,
				callback: function(result) {
					if (result) {
						var args = {
							username: $('#username').val(),
							disable: true
						};
						api.editUser(args).then(function(response) {
							if (response.status == 0) {
								logs('User disabled. Username: ' + response.data.key);
								window.location.replace(HTML_PATH_ADMIN_ROOT + 'users');
							} else {
								logs("An error occurred while trying to disable the user.");
								showAlertError(response.message);
							}
						});
					}
				}
			});
		});

        $('#btnDeleteUserAndKeepContent').on('click', function() {
            var username = $('#username').val();
            logs('Deleting user. Username: ' + username);
            bootbox.confirm({
                message: '<?php $L->p('Are you sure you want to delete this user') ?>',
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i><?php $L->p('Cancel') ?>',
                        className: 'btn-sm btn-secondary'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i><?php $L->p('Confirm') ?>',
                        className: 'btn-sm btn-primary'
                    }
                },
                closeButton: false,
                callback: function(result) {
                    if (result) {
                        var args = {
                            username: $('#username').val()
                        };
                        api.deleteUser(args).then(function(response) {
                            if (response.status == 0) {
                                logs('User deleted. Username: ' + response.data.key);
                                window.location.replace(HTML_PATH_ADMIN_ROOT + 'users');
                            } else {
                                logs("An error occurred while trying to disable the user.");
                                showAlertError(response.message);
                            }
                        });
                    }
                }
            });
        });

        $('#btnDeleteUserAndContent').on('click', function() {
            var username = $('#username').val();
            logs('Deleting user and content. Username: ' + username);
            bootbox.confirm({
                message: '<?php $L->p('Are you sure you want to delete this user') ?>',
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i><?php $L->p('Cancel') ?>',
                        className: 'btn-sm btn-secondary'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i><?php $L->p('Confirm') ?>',
                        className: 'btn-sm btn-primary'
                    }
                },
                closeButton: false,
                callback: function(result) {
                    if (result) {
                        var args = {
                            username: $('#username').val(),
                            deleteContent: true
                        };
                        api.deleteUser(args).then(function(response) {
                            if (response.status == 0) {
                                logs('User and content deleted. Username: ' + response.data.key);
                                window.location.replace(HTML_PATH_ADMIN_ROOT + 'users');
                            } else {
                                logs("An error occurred while trying to disable the user.");
                                showAlertError(response.message);
                            }
                        });
                    }
                }
            });
        });

	});

	// ============================================================================
	// Initlization for the view
	// ============================================================================
	$(document).ready(function() {
		// nothing here yet
		// how do you hang your toilet paper ? over or under ?
	});
</script>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-person"></i><?php $L->p('Edit user') ?></h2>
	<div class="ms-auto">
		<button id="btnSave" type="button" class="btn btn-primary btn-sm"><?php $L->p('Save') ?></button>
		<a id="btnCancel" class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'users' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs ps-3 mb-3" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true"><?php $L->p('Profile') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="picture-tab" data-bs-toggle="tab" href="#picture" role="tab" aria-controls="picture" aria-selected="false"><?php $L->p('Profile picture') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="security-tab" data-bs-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false"><?php $L->p('Security') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="social-tab" data-bs-toggle="tab" href="#social" role="tab" aria-controls="social" aria-selected="false"><?php $L->p('Social Networks') ?></a>
	</li>
</ul>
<!-- End Tabs -->

<!-- Content -->
<div class="tab-content" id="tabContent">

	<!-- Tab profile -->
	<div class="tab-pane show active" id="profile" role="tabpanel">
		<?php
		echo Bootstrap::formInputText(array(
			'name' => 'username',
			'label' => $L->g('Username'),
			'value' => $user->username(),
			'disabled' => true
		));

		if ($login->role() === 'admin') {
			echo Bootstrap::formSelect(array(
				'name' => 'role',
				'label' => $L->g('Role'),
				'options' => array('author' => $L->g('Author'), 'editor' => $L->g('Editor'), 'admin' => $L->g('Administrator')),
				'selected' => $user->role(),
				'tip' => $L->g('author-can-write-and-edit-their-own-content')
			));
		}

		echo Bootstrap::formInputText(array(
			'name' => 'email',
			'label' => $L->g('Email'),
			'value' => $user->email(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'nickname',
			'label' => $L->g('Nickname'),
			'value' => $user->nickname(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'firstName',
			'label' => $L->g('First Name'),
			'value' => $user->firstName(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'lastName',
			'label' => $L->g('Last Name'),
			'value' => $user->lastName(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formTextarea(array(
			'name' => 'bio',
			'label' => $L->g('Bio'),
			'value' => $user->bio(),
			'rows' => 4,
			'data' => array('save' => 'true')
		));
		?>
	</div>
	<!-- End Tab profile -->

	<!-- Tab profile picture -->
	<div class="tab-pane" id="picture" role="tabpanel">
		<div class="container">
			<div class="row">
				<div class="col-8">
					<img id="profilePicturePreview" class="img-fluid img-thumbnail" alt="Profile picture preview" src="<?php echo ($user->profilePicture() ? $user->profilePicture() . '?version=' . time() : HTML_PATH_CORE_IMG . 'default.svg') ?>" />
				</div>
				<div class="col-4">
					<label id="btnUploadProfilePicture" class="btn btn-primary"><i class="bi bi-upload"></i><?php $L->p('Upload image'); ?><input type="file" id="inputProfilePicture" name="inputProfilePicture" hidden></label>
					<button id="btnRemoveProfilePicture" type="button" class="btn btn-secondary"><i class="bi bi-trash"></i><?php $L->p('Remove image'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<!-- End Tab profile picture -->

	<!-- Tab security -->
	<div class="tab-pane" id="security" role="tabpanel">
		<?php
		if (checkRole(array('admin'), false)) {
			echo Bootstrap::formTitle(array('title' => $L->g('Status')));

			echo Bootstrap::formInputText(array(
				'name' => 'status',
				'label' => $L->g('Current status'),
				'value' => $user->enabled() ? $L->g('Enabled') : $L->g('Disabled'),
				'disabled' => true,
				'tip' => $user->enabled() ? '' : $L->g('To enable the user you must set a new password')
			));

			echo Bootstrap::formInputText(array(
				'name' => 'registered',
				'label' => $L->g('Registered'),
				'value' => Date::format($user->registered(), DB_DATE_FORMAT, ADMIN_PANEL_DATE_FORMAT),
				'disabled' => true
			));

			if ($user->enabled()) {
				echo '
				<div class="form-group row">
				<div class="col-sm-2"></div>
				<div class="col-sm-10">
					<button type="button" class="btn btn-warning me-2" id="btnDisableUser"><i class="bi bi-slash-circle"></i>' . $L->g('Disable user') . '</button>
					<button type="button" class="btn btn-danger me-2" id="btnDeleteUserAndKeepContent"><i class="bi bi-trash"></i>' . $L->g('Delete user and keep content') . '</button>
					<button type="button" class="btn btn-danger" id="btnDeleteUserAndContent"><i class="bi bi-trash"></i>' . $L->g('Delete user and delete content') . '</button>
				</div>
				</div>
				';
			}
		}

		echo Bootstrap::formTitle(array('title' => $L->g('Authentication Token')));

		echo Bootstrap::formInputText(array(
			'name' => 'tokenAuth',
			'label' => $L->g('Token'),
			'value' => $user->tokenAuth(),
			'tip' => $L->g('this-token-is-similar-to-a-password-it-should-not-be-shared')
		));

		echo Bootstrap::formTitle(array('title' => $L->g('Change password')));

		echo Bootstrap::formInputText(array(
			'name' => 'newPassword',
			'label' => $L->g('New password'),
			'type' => 'password',
			'value' => ''
		));

		echo Bootstrap::formInputText(array(
			'name' => 'confirmPassword',
			'label' => $L->g('Confirm password'),
			'type' => 'password',
			'value' => ''
		));
		?>
	</div>
	<!-- End Tab security -->

	<!-- Social Networks tab -->
	<div class="tab-pane" id="social" role="tabpanel">
		<?php
		echo Bootstrap::formInputText(array(
			'name' => 'youtube',
			'label' => 'Youtube',
			'value' => $user->youtube(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'twitter',
			'label' => 'Twitter',
			'value' => $user->twitter(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'facebook',
			'label' => 'Facebook',
			'value' => $user->facebook(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'discord',
			'label' => 'Discord',
			'value' => $user->discord(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'codepen',
			'label' => 'CodePen',
			'value' => $user->codepen(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'instagram',
			'label' => 'Instagram',
			'value' => $user->instagram(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'gitlab',
			'label' => 'GitLab',
			'value' => $user->gitlab(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'github',
			'label' => 'GitHub',
			'value' => $user->github(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'linkedin',
			'label' => 'LinkedIn',
			'value' => $user->linkedin(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'xing',
			'label' => 'Xing',
			'value' => $user->xing(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'mastodon',
			'label' => 'Mastodon',
			'value' => $user->mastodon(),
			'data' => array('save' => 'true')
		));

		echo Bootstrap::formInputText(array(
			'name' => 'vk',
			'label' => 'VK',
			'value' => $user->vk(),
			'data' => array('save' => 'true')
		));
		?>
	</div>
</div>
