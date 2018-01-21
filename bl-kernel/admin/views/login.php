<div class="login-form">

<form method="post" action="" class="uk-form" autocomplete="off">

	<input type="hidden" id="jstoken" name="tokenCSRF" value="<?php echo $Security->getTokenCSRF() ?>">

	<div class="uk-form-row">
	<input name="username" class="uk-width-1-1 uk-form-large" placeholder="<?php $L->p('Username') ?>" type="text">
	</div>

	<div class="uk-form-row">
	<input name="password" class="uk-width-1-1 uk-form-large" placeholder="<?php $L->p('Password') ?>" type="password">
	</div>

	<div class="uk-form-row">
	<label><input type="checkbox" name="remember"> <?php $L->p('Remember me') ?></label>
	</div>

	<div class="uk-form-row">
	<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large"><?php $Language->p('Login') ?></button>
	</div>

</form>

</div>