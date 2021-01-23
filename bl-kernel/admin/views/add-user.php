<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-person"></i><?php $L->p('New user') ?></h2>
	<div class="ms-auto">
		<button id="btnSave" type="button" class="btn btn-primary btn-sm"><?php $L->p('Save') ?></button>
		<a id="btnCancel" class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'users' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
</div>

<?php
echo Bootstrap::formInputText(array(
	'id' => 'username',
	'name' => 'username',
	'label' => $L->g('Username'),
	'value' => ''
));

echo Bootstrap::formInputText(array(
	'id' => 'password',
	'name' => 'password',
	'type' => 'password',
	'label' => $L->g('Password'),
	'value' => ''
));

echo Bootstrap::formInputText(array(
	'id' => 'confirmPassword',
	'name' => 'confirmPassword',
	'type' => 'password',
	'label' => $L->g('Confirm Password'),
	'value' => ''
));

echo Bootstrap::formSelect(array(
	'name' => 'role',
	'label' => $L->g('Role'),
	'options' => array('author' => $L->g('Author'), 'editor' => $L->g('Editor'), 'admin' => $L->g('Administrator')),
	'selected' => 'Author',
	'tip' => $L->g('author-can-write-and-edit-their-own-content')
));

echo Bootstrap::formInputText(array(
	'id' => 'email',
	'name' => 'email',
	'type' => 'email',
	'label' => $L->g('Email'),
	'value' => ''
));
?>

<script>
	$(document).ready(function() {
		$('#btnSave').on('click', function() {
			var username = $('#username').val();
			var password = $('#password').val();
			var confirmPassword = $('#confirmPassword').val();

			if (username.length < 1) {
				showAlertError("<?php $L->p('Complete all fields') ?>");
				return false;
			}

			if (password.length < PASSWORD_LENGTH) {
				showAlertError("<?php $L->p('Password must be at least 6 characters long') ?>");
				return false;
			}

			if (password !== confirmPassword) {
				showAlertError("<?php $L->p('The password and confirmation password do not match') ?>");
				return false;
			}

			var args = {
				username: username,
				password: password,
				role: $('#role').val(),
				email: $('#email').val()
			};
			api.createUser(args).then(function(response) {
				if (response.status == 0) {
					logs('User created. Username: ' + response.data.username);
					window.location.replace(HTML_PATH_ADMIN_ROOT + 'users');
				} else {
					logs('An error occurred while trying to create the user.');
					showAlertError(response.message);
				}
			});
			return true;
		});
	});
</script>