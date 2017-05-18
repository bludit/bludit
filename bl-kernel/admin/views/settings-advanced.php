<?php

HTML::title(array('title'=>$L->g('Advanced settings'), 'icon'=>'cogs'));

HTML::formOpen(array('class'=>'uk-form-horizontal'));

    HTML::formInputHidden(array(
        'name'=>'tokenCSRF',
        'value'=>$Security->getTokenCSRF()
    ));

    HTML::legend(array('value'=>$L->g('General'), 'class'=>'first-child'));

    HTML::formSelect(array(
        'name'=>'itemsPerPage',
        'label'=>$L->g('Items per page'),
        'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8'),
        'selected'=>$Site->itemsPerPage(),
        'class'=>'uk-width-1-3 uk-form-medium',
        'tip'=>$L->g('number-of-posts-to-show-per-page')
    ));

    HTML::formInputText(array(
        'name'=>'url',
        'label'=>$L->g('Site URL'),
        'value'=>$Site->url(),
        'class'=>'uk-width-1-2 uk-form-medium',
        'tip'=>$L->g('the-url-of-your-site')
    ));

    HTML::legend(array('value'=>$L->g('Website or Blog')));

    HTML::formSelect(array(
        'name'=>'orderBy',
        'label'=>$L->g('Order Pages By'),
        'options'=>array('date'=>'Date','position'=>'Position'),
        'selected'=>$Site->orderBy(),
        'class'=>'uk-width-1-3 uk-form-medium',
        'tip'=>$L->g('Order the pages by date to create a Blog or order the pages by position to create a Website')
    ));

    HTML::legend(array('value'=>$L->g('Email account settings')));

    HTML::formInputText(array(
        'name'=>'emailFrom',
        'label'=>$L->g('Sender email'),
        'value'=>$Site->emailFrom(),
        'class'=>'uk-width-1-2 uk-form-medium',
        'tip'=>$L->g('Emails will be sent from this address')
    ));

    HTML::legend(array('value'=>$L->g('URL Filters')));

    HTML::formInputText(array(
        'name'=>'uriPage',
        'label'=>$L->g('Pages'),
        'value'=>$Site->uriFilters('page'),
        'class'=>'uk-width-1-2 uk-form-medium',
        'tip'=>''
    ));

    HTML::formInputText(array(
        'name'=>'uriTag',
        'label'=>$L->g('Tags'),
        'value'=>$Site->uriFilters('tag'),
        'class'=>'uk-width-1-2 uk-form-medium',
        'tip'=>''
    ));

    HTML::formInputText(array(
        'name'=>'uriCategory',
        'label'=>$L->g('Category'),
        'value'=>$Site->uriFilters('category'),
        'class'=>'uk-width-1-2 uk-form-medium',
        'tip'=>''
    ));

    echo '<div class="uk-form-row">
        <div class="uk-form-controls">
        <button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
        </div>
    </div>';

HTML::formClose();
