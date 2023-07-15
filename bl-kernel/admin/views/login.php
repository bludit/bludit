<?php defined('BLUDIT') or die('Bludit CMS.');

echo '<h1 class="text-center mb-3 mt-3 font-weight-normal" style="color: #555;">'.$site->title().'</h1>';

echo Bootstrap::formOpen(array());

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo '
	<div class="form-group">
		<input type="text" value="'.(isset($_POST['username'])?Sanitize::html($_POST['username']):'').'" class="form-control form-control-lg" id="jsusername" name="username" placeholder="'.$L->g('Username').'" autofocus>
	</div>
	';

	echo '
	<div class="form-group">
		<input type="password" class="form-control form-control-lg" id="jspassword" name="password" placeholder="'.$L->g('Password').'">
	</div>
	';

	echo '
	<div class="form-check">
		<input class="form-check-input" type="checkbox" value="true" id="jsremember" name="remember">
		<label class="form-check-label" for="jsremember">'.$L->g('Remember me').'</label>
	</div>

	<div class="form-group mt-3">
		<button type="submit" class="btn btn-primary btn-lg mr-2 w-100" name="save">'.$L->g('Login').'</button>
	</div>
	';

echo '</form>';

echo '<p class="mt-3 text-right">'.$L->g('Powered by Bludit').((defined('BLUDIT_PRO'))?' PRO':'').'</p>'
?>
