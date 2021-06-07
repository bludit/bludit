<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		// No events for the view yet
	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<?php

echo '<h1 class="text-center fw-normal mb-5">'.$site->title().'</h1>';

echo Bootstrap::formOpen(array('name'=>'login'));

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo Bootstrap::formFloatingLabels(array(
		'id'=>'username',
		'name'=>'username',
		'type'=>'text',
		'value'=>(isset($_POST['username'])?Sanitize::html($_POST['username']):''),
		'class'=>'form-control-lg',
		'placeholder'=>$L->g('Username')
	));

	echo Bootstrap::formFloatingLabels(array(
		'id'=>'password',
		'name'=>'password',
		'type'=>'password',
		'value'=>'',
		'class'=>'form-control-lg',
		'placeholder'=>$L->g('Password')
	));

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

echo '<p class="mt-5 text-end">'.$L->g('Powered by Bludit').'</p>'

?>