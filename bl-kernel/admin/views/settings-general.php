<?php

HTML::title(array('title'=>$L->g('General settings'), 'icon'=>'cogs'));

HTML::formOpen(array('class'=>'uk-form-horizontal'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	HTML::legend(array('value'=>$L->g('Site information'), 'class'=>'first-child'));

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

	HTML::legend(array('value'=>$L->g('Social networks links')));

	HTML::formInputText(array(
		'name'=>'twitter',
		'label'=>'Twitter',
		'value'=>$Site->twitter(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'placeholder'=>'https://twitter.com/USERNAME',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'facebook',
		'label'=>'Facebook',
		'value'=>$Site->facebook(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'placeholder'=>'https://www.facebook.com/USERNAME',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'googlePlus',
		'label'=>'Google+',
		'value'=>$Site->googlePlus(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'placeholder'=>'https://plus.google.com/+USERNAME',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'instagram',
		'label'=>'Instagram',
		'value'=>$Site->googlePlus(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'placeholder'=>'https://www.instagram.com/USERNAME',
		'tip'=>''
	));

	HTML::formInputText(array(
		'name'=>'github',
		'label'=>'Github',
		'value'=>$Site->github(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'placeholder'=>'https://github.com/USERNAME',
		'tip'=>''
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		</div>
	</div>';

HTML::formClose();