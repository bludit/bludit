<?php

HTML::title(array('title'=>$L->g('Edit Category'), 'icon'=>'globe'));

HTML::formOpen(array('class'=>'uk-form-horizontal'));

	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	HTML::formInputHidden(array(
		'name'=>'categoryKey',
		'value'=>$categoryKey
	));

	HTML::formInputText(array(
		'name'=>'category',
		'label'=>$L->g('Name'),
		'value'=>$category,
		'class'=>'uk-width-1-2 uk-form-medium'
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" name="edit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<button type="submit" name="delete" class="uk-button uk-button-primary">'.$L->g('Delete').'</button>
		<a href="'.HTML_PATH_ADMIN_ROOT.'categories" class="uk-button">'.$L->g('Cancel').'</a>
		</div>
	</div>';

HTML::formClose();
