<div class="login-form">

<form method="post" action="" class="uk-form" autocomplete="off">

	<input type="hidden" id="jstoken" name="tokenCSRF" value="<?php $Security->printTokenCSRF() ?>">

	<div class="uk-form-row">
	<input name="username" class="uk-width-1-1 uk-form-large" placeholder="<?php $L->p('Username') ?>" type="text">
	</div>

	<div class="uk-form-row">
	<input name="password" class="uk-width-1-1 uk-form-large" placeholder="<?php $L->p('Password') ?>" type="password">
	</div>

	<div class="uk-form-row">
	<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large"><?php $Language->p('Login') ?></button>
	</div>

</form>

</div>

<a class="login-email" href="<?php echo HTML_PATH_ADMIN_ROOT.'login-email' ?>"><i class="uk-icon-envelope-o"></i> <?php $L->p('Send me a login access code') ?></a>
