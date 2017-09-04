<?php

HTML::title(array('title'=>$L->g('Language and timezone'), 'icon'=>'globe'));

HTML::formOpen(array('class'=>'uk-form-horizontal'));

	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	HTML::legend(array('value'=>$L->g('General'), 'class'=>'first-child'));

	HTML::formSelect(array(
		'name'=>'language',
		'label'=>$L->g('Language'),
		'options'=>$Language->getLanguageList(),
		'selected'=>$Site->language(),
		'class'=>'uk-width-1-3 uk-form-medium',
		'tip'=>$L->g('select-your-sites-language')
	));

	HTML::formSelect(array(
		'name'=>'timezone',
		'label'=>$L->g('Timezone'),
		'options'=>Date::timezoneList(),
		'selected'=>$Site->timezone(),
		'class'=>'uk-width-1-3 uk-form-medium',
		'tip'=>$L->g('select-a-timezone-for-a-correct')
	));

	HTML::formInputText(array(
		'name'=>'locale',
		'label'=>$L->g('Locale'),
		'value'=>$Site->locale(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('with-the-locales-you-can-set-the-regional-user-interface')
	));

	HTML::legend(array('value'=>$L->g('Date and time formats')));

	HTML::formInputText(array(
		'name'=>'dateFormat',
		'label'=>$L->g('Date format'),
		'value'=>$Site->dateFormat(),
		'class'=>'uk-width-1-2 uk-form-medium',
		'tip'=>$L->g('Current format').': '.Date::current($Site->dateFormat())
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		<a class="uk-button" href="'.HTML_PATH_ADMIN_ROOT.'settings-regional">'.$L->g('Cancel').'</a>
		</div>
	</div>';

HTML::formClose();

?>

<script>

$(document).ready(function() {

	$("#jslanguage").change(function () {
		$("#jslocale").attr("value", "<?php $L->p('You can change this field when save the current changes') ?>");
		$("#jslocale").attr("disabled", true);
	});

});

</script>