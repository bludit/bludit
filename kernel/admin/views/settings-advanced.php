<?php

HTML::title(array('title'=>$L->g('Advanced settings'), 'icon'=>'cogs'));

HTML::formOpen(array('class'=>'uk-form-horizontal'));

    HTML::formInputHidden(array(
        'name'=>'tokenCSRF',
        'value'=>$Security->getTokenCSRF()
    ));

    HTML::legend(array('value'=>$L->g('General'), 'class'=>'first-child'));

    HTML::formSelect(array(
        'name'=>'postsperpage',
        'label'=>$L->g('Posts per page'),
        'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8'),
        'selected'=>$Site->postsPerPage(),
        'class'=>'uk-width-1-3 uk-form-medium',
        'tip'=>$L->g('number-of-posts-to-show-per-page')
    ));

    HTML::formSelect(array(
        'name'=>'homepage',
        'label'=>$L->g('Default home page'),
        'options'=>$_homePageList,
        'selected'=>$Site->homepage(),
        'class'=>'uk-width-1-3 uk-form-medium',
        'tip'=>''
    ));

    HTML::formInputText(array(
        'name'=>'url',
        'label'=>$L->g('Site URL'),
        'value'=>$Site->url(),
        'class'=>'uk-width-1-2 uk-form-medium',
        'tip'=>$L->g('the-url-of-your-site')
    ));

    HTML::legend(array('value'=>$L->g('Command Line Mode')));

    HTML::formSelect(array(
        'name'=>'cliMode',
        'label'=>$L->g('Cli Mode'),
        'options'=>array('true'=>$L->g('Enabled'), 'false'=>$L->g('Disabled')),
        'selected'=>$Site->cliMode(),
        'class'=>'uk-width-1-3 uk-form-medium',
        'tip'=>$L->g('enable-the-command-line-mode-if-you-add-edit')
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
        'name'=>'uriPost',
        'label'=>$L->g('Posts'),
        'value'=>$Site->uriFilters('post'),
        'class'=>'uk-width-1-2 uk-form-medium',
        'tip'=>''
    ));

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
        'name'=>'uriBlog',
        'label'=>$L->g('Blog'),
        'value'=>$Site->uriFilters('blog'),
        'class'=>'uk-width-1-2 uk-form-medium',
        'tip'=>''
    ));

    echo '<div class="uk-form-row">
        <div class="uk-form-controls">
        <button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
        </div>
    </div>';

HTML::formClose();
