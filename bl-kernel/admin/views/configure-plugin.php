<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$plugin->name(), 'icon'=>'wrench'));

echo Bootstrap::formOpen(array('class'=>'plugin-form'));

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	// Print the plugin form
	echo $plugin->form();

	if ($plugin->formButtons()) {
		echo '
		<div class="form-group mt-4">
			<button type="submit" class="btn btn-primary mr-2" name="save">'.$L->g('Save').'</button>
			<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'plugins" role="button">'.$L->g('Cancel').'</a>
		</div>
		';
	}

echo Bootstrap::formClose();
