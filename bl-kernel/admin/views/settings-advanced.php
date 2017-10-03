<?php

HTML::title(array('title'=>$L->g('Advanced settings'), 'icon'=>'cogs'));

HTML::formOpen(array('class'=>'uk-form-horizontal'));

	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	HTML::legend(array('value'=>$L->g('Content')));

	HTML::formSelect(array(
		'name'=>'itemsPerPage',
		'label'=>$L->g('Items per page'),
		'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8', '-1'=>$L->g('All content')),
		'selected'=>$Site->itemsPerPage(),
		'class'=>'uk-width-1-3 uk-form-medium',
		'tip'=>$L->g('Number of items to show per page')
	));

	HTML::formSelect(array(
		'name'=>'orderBy',
		'label'=>$L->g('Order content by'),
		'options'=>array('date'=>$L->g('Date'),'position'=>$L->g('Position')),
		'selected'=>$Site->orderBy(),
		'class'=>'uk-width-1-3 uk-form-medium',
		'tip'=>$L->g('order-the-content-by-date-to-build-a-blog')
	));

	HTML::legend(array('value'=>$L->g('Predefined pages')));

	HTML::formSelect(array(
		'name'=>'homepage',
		'label'=>$L->g('Homepage'),
		'options'=>$homepageOptions,
		'selected'=>$Site->homepage(),
		'class'=>'uk-width-1-3 uk-form-medium',
		'tip'=>$L->g('Returning page for the main page')
	));

	$homepageOptions[' '] = '- '.$L->g('Default message').' -';
	HTML::formSelect(array(
		'name'=>'pageNotFound',
		'label'=>$L->g('Page not found'),
		'options'=>$homepageOptions,
		'selected'=>$Site->pageNotFound(),
		'class'=>'uk-width-1-3 uk-form-medium',
		'tip'=>$L->g('Returning page when the page doesnt exist')
	));

	HTML::legend(array('value'=>$L->g('Email account settings')));

	HTML::formInputText(array(
		'name'=>'emailFrom',
		'label'=>$L->g('Sender email'),
		'value'=>$Site->emailFrom(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('Emails will be sent from this address')
	));

	HTML::legend(array('value'=>$L->g('Site URL')));

	HTML::formInputText(array(
		'name'=>'url',
		'label'=>'',
		'value'=>$Site->url(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('full-url-of-your-site'),
		'placeholder'=>'https://'
	));

	HTML::legend(array('value'=>$L->g('URL Filters')));

	HTML::formInputText(array(
		'name'=>'uriPage',
		'label'=>$L->g('Pages'),
		'value'=>$Site->uriFilters('page'),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>DOMAIN_PAGES
	));

	HTML::formInputText(array(
		'name'=>'uriTag',
		'label'=>$L->g('Tags'),
		'value'=>$Site->uriFilters('tag'),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>DOMAIN_TAGS
	));

	HTML::formInputText(array(
		'name'=>'uriCategory',
		'label'=>$L->g('Category'),
		'value'=>$Site->uriFilters('category'),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>DOMAIN_CATEGORIES
	));

	HTML::formInputText(array(
		'name'=>'uriBlog',
		'label'=>$L->g('Blog'),
		'value'=>$Site->uriFilters('blog'),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>DOMAIN_BLOG
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<a class="uk-button" href="'.HTML_PATH_ADMIN_ROOT.'settings-advanced">'.$L->g('Cancel').'</a>
		</div>
	</div>';

HTML::formClose();
