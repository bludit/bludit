<?php

HTML::title(array('title'=>$L->g('Settings'), 'icon'=>'cogs'));

HTML::formOpen(array('class'=>'uk-form-horizontal'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'token',
		'value'=>$Security->getToken()
	));

	HTML::formInputText(array(
		'name'=>'title',
		'label'=>$L->g('Site title'),
		'value'=>$Site->title(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('use-this-field-to-name-your-site')
	));

	HTML::formInputText(array(
		'name'=>'slogan',
		'label'=>$L->g('Site slogan'),
		'value'=>$Site->slogan(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('use-this-field-to-add-a-catchy-phrase')
	));

	HTML::formInputText(array(
		'name'=>'description',
		'label'=>$L->g('Site description'),
		'value'=>$Site->description(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('you-can-add-a-site-description-to-provide')
	));

	HTML::formInputText(array(
		'name'=>'footer',
		'label'=>$L->g('Footer text'),
		'value'=>$Site->footer(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('you-can-add-a-small-text-on-the-bottom')
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<a href="#" class="uk-button">'.$L->g('Cancel').'</a>
		</div>
	</div>';

HTML::formClose();