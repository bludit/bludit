<h2 class="title"><?php $Language->p('Login') ?></h2>

<form method="post" action="<?php echo HTML_PATH_ADMIN_ROOT.'login' ?>" class="forms" autocomplete="off">

	<input type="hidden" id="jstoken" name="token" value="<?php $Security->printToken() ?>">

	<label>
		<input type="text" name="username" placeholder="<?php $Language->p('Username') ?>" class="width-100" autocomplete="off">
	</label>

	<label>
		<input type="password" name="password" placeholder="<?php $Language->p('Password') ?>" class="width-100" autocomplete="off">
	</label>

	<p>
		<button class="btn btn-blue width-100"><?php $Language->p('Login') ?></button>
	</p>
</form>