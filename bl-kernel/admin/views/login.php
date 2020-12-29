<?php defined('BLUDIT') or die('Bludit CMS.');

echo '<h1 class="text-center font-weight-normal mb-5">'.$site->title().'</h1>';

echo Bootstrap::formOpen(array());

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo '
	<div class="mb-3">
		<input type="text" value="'.(isset($_POST['username'])?Sanitize::html($_POST['username']):'').'" class="form-control form-control-lg" id="username" name="username" placeholder="'.$L->g('Username').'" autofocus>
	</div>
	';

	echo '
	<div class="mb-3">
		<input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="'.$L->g('Password').'">
	</div>
	';

	echo '
	<div class="form-check">
		<input class="form-check-input" type="checkbox" value="true" id="jsremember" name="remember">
		<label class="form-check-label" for="jsremember">'.$L->g('Remember me').'</label>
	</div>

	<div class="mt-4">
		<button type="submit" class="btn btn-primary btn-lg me-2 w-100" name="save">'.$L->g('Login').'</button>
	</div>
	';

echo '</form>';

echo '<p class="mt-5 text-end">Powered by <a href="https://www.bludit.com">Bludit</a></p>'

?>
