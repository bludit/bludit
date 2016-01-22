<?php

HTML::title(array('title'=>$L->g('Language and timezone'), 'icon'=>'cogs'));

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
		'tip'=>$L->g('you-can-use-this-field-to-define-a-set-off')
	));

	HTML::legend(array('value'=>$L->g('Date and time formats')));

	HTML::formInputText(array(
		'name'=>'dateFormat',
		'label'=>$L->g('Date format'),
		'value'=>$Site->dateFormat(),
		'class'=>'uk-width-1-2 uk-form-medium'
	));

	echo '<div class="uk-form-row">
		<div class="uk-form-controls">
		<button type="submit" class="uk-button uk-button-primary">'.$L->g('Save').'</button>
		</div>
	</div>';

HTML::formClose();

?>

<script>

$(document).ready(function() {

	$("#jslanguage").change(function () {
		var locale = $("#jslanguage option:selected").val();
		$("#jslocale").attr("value",locale);
	});

});

</script>
