<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::formOpen(array());

	echo '
	<div>
	<div class="float-right">
		<button type="submit" class="btn btn-primary btn-sm" name="save">'.$L->g('Save').'</button>
		<a class="btn btn-secondary btn-sm" href="'.HTML_PATH_ADMIN_ROOT.'users" role="button">'.$L->g('Cancel').'</a>
	</div>
	<h2 class="mt-0 mb-3">
		<span class="oi oi-person" style="font-size: 0.7em;"></span> '.$L->g('Edit user').'
	</h2>
	</div>
	';

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

	echo Bootstrap::formTitle(array('title'=>$L->g('Profile')));

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

	echo Bootstrap::formTitle(array('title'=>$L->g('Password')));

	echo '
	<div class="form-group row">
	<div class="col-sm-2"></div>
	<div class="col-sm-10">
		<a href="'.HTML_PATH_ADMIN_ROOT.'user-password/'.$user->username().'" class="btn btn-primary mr-2">'.$L->g('Change password').'</a>
	</div>
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
			<button type="submit" class="btn btn-primary mr-2" id="jsdisableUser" name="disableUser">'.$L->g('Disable user').'</button>
			<button type="submit" class="btn btn-danger mr-2" id="jsdeleteUserAndKeepContent" name="deleteUserAndKeepContent">'.$L->g('Delete user and keep content').'</button>
			<button type="submit" class="btn btn-danger mr-2" id="jsdeleteUserAndDeleteContent" name="deleteUserAndDeleteContent">'.$L->g('Delete user and delete content').'</button>
		</div>
		</div>
		';
	}

	echo Bootstrap::formTitle(array('title'=>$L->g('Social Networks')));

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
		'name'=>'googlePlus',
		'label'=>'Google+',
		'value'=>$user->googlePlus(),
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
		<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'users" role="button">'.$L->g('Cancel').'</a>
	</div>
	';

echo Bootstrap::formClose();

echo Bootstrap::formTitle(array('title'=>$L->g('Profile picture')));

$src = (Sanitize::pathFile(PATH_UPLOADS_PROFILES.$user->username().'.png')?DOMAIN_UPLOADS_PROFILES.$user->username().'.png':HTML_PATH_ADMIN_THEME_IMG.'default.svg');
echo '
<div class="form-group row">
<div class="col-sm-2"></div>
<div class="col-sm-10">
	<img id="jsprofilePictureImg" style="width: 350px; height: 200px;" class="img-thumbnail mb-2" alt="Profile Picture" src="'.$src.'" />

	<form id="jsprofilePictureForm" name="profilePictureForm" enctype="multipart/form-data">
		<input type="hidden" name="tokenCSRF" value="'.$security->getTokenCSRF().'">
		<div class="custom-file">
			<input type="file" class="custom-file-input" id="jsprofilePictureInputFile" name="profilePictureInputFile">
			<label class="custom-file-label" for="jsprofilePictureInputFile"></label>
		</div>
	</form>
</div>
</div>
';

?>

<script>
$(document).ready(function() {

	$("#jsdeleteUserAndDeleteContent").click(function() {
		if(confirm("<?php $L->p('Confirm delete this action cannot be undone') ?>")==false) {
			return false;
		}
	});

	$("#jsdeleteUserAndKeepContent").click(function() {
		if(confirm("<?php $L->p('Confirm delete this action cannot be undone') ?>")==false) {
			return false;
		}
	});

	$("#jsprofilePictureInputFile").on("change", function() {

		// Data to send via AJAX
		var username = $("#jsusername").val();
		var formData = new FormData($("#jsprofilePictureForm")[0]);
		formData.append('username', username);
		formData.append('tokenCSRF', tokenCSRF);

		$.ajax({
			url: HTML_PATH_ADMIN_ROOT+"ajax/profile-picture",
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
							var percentComplete = (e.loaded / e.total)*100;
							console.log("Uploading profile picture: "+percentComplete);
						}
					}, false);
				}
				return xhr;
			}
		}).done(function(e) {
			$("#jsprofilePictureImg").attr('src',e.absoluteURL+"?time="+Math.random());
		});

	});

});
</script>
