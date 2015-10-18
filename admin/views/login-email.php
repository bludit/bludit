<div class="login-form">

<form method="post" action="<?php echo HTML_PATH_ADMIN_ROOT.'login' ?>" class="uk-form" autocomplete="off">

	<input type="hidden" id="jstoken" name="token" value="<?php $Security->printToken() ?>">

	<div class="uk-form-row">
	<input name="email" class="uk-width-1-1 uk-form-large" placeholder="<?php $L->p('Email') ?>" type="text">
	</div>

	<div class="uk-form-row">
	<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Send me the email</button>
	</div>

</form>

</div>

<a class="login-email" href="<?php echo HTML_PATH_ADMIN_ROOT.'login' ?>"> Back to login form</a>