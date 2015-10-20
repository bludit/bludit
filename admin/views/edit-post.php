<?php

HTML::title(array('title'=>$L->g('Edit post'), 'icon'=>'pencil'));

HTML::formOpen(array('class'=>'uk-form-stacked'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getToken()
	));

	// Key input
	HTML::formInputHidden(array(
		'name'=>'key',
		'value'=>$_Post->key()
	));

// ---- LEFT SIDE ----
echo '<div class="uk-grid">';
echo '<div class="uk-width-large-7-10">';

	// Title input
	HTML::formInputText(array(
		'name'=>'title',
		'value'=>$_Post->title(),
		'class'=>'uk-width-1-1 uk-form-large',
		'placeholder'=>$L->g('Title')
	));

	// Content input
	HTML::formTextarea(array(
		'name'=>'content',
		'value'=>$_Post->contentRaw(false),
		'class'=>'uk-width-1-1 uk-form-large',
		'placeholder'=>$L->g('Content')
	));

	// Form buttons
	echo '<div class="uk-form-row uk-margin-bottom">
		<button class="uk-button uk-button-primary" type="submit">'.$L->g('Save').'</button>
		<button id="jsdelete-post" name="delete-post" class="uk-button" type="submit">'.$L->g('Delete').'</button>
		<a class="uk-button" href="'.HTML_PATH_ADMIN_ROOT.'manage-posts">'.$L->g('Cancel').'</a>
	</div>';

echo '</div>';

// ---- RIGHT SIDE ----
echo '<div class="uk-width-large-3-10">';

	// Tabs, general and advanced mode
	echo '<ul class="uk-tab" data-uk-tab="{connect:\'#tab-options\'}">';
	echo '<li><a href="">'.$L->g('General').'</a></li>';
	echo '<li><a href="">'.$L->g('Advanced').'</a></li>';
	echo '</ul>';

	echo '<ul id="tab-options" class="uk-switcher uk-margin">';

	// ---- GENERAL TAB ----
	echo '<li>';

	// Description input
	HTML::formTextarea(array(
		'name'=>'description',
		'label'=>$L->g('description'),
		'value'=>$_Post->description(),
		'rows'=>'7',
		'class'=>'uk-width-1-1 uk-form-medium',
		'tip'=>$L->g('this-field-can-help-describe-the-content')
	));

	// Tags input
	HTML::formInputText(array(
		'name'=>'tags',
		'value'=>$_Post->tags(),
		'class'=>'uk-width-1-1 uk-form-large',
		'tip'=>$L->g('Write the tags separated by commas'),
		'label'=>$L->g('Tags')
	));

	echo '</li>';

	// ---- ADVANCED TAB ----
	echo '<li>';

	// Date input
	HTML::formInputText(array(
		'name'=>'date',
		'value'=>$_Post->date(),
		'class'=>'uk-width-1-1 uk-form-large',
		'tip'=>$L->g('To schedule the post just select the date and time'),
		'label'=>$L->g('Date')
	));

	// Status input
	HTML::formSelect(array(
		'name'=>'status',
		'label'=>$L->g('Status'),
		'class'=>'uk-width-1-1 uk-form-medium',
		'options'=>array('published'=>$L->g('Published'), 'draft'=>$L->g('Draft')),
		'selected'=>($_Post->draft()?'draft':'published'),
		'tip'=>''
	));

	// Slug input
	HTML::formInputText(array(
		'name'=>'slug',
		'value'=>$_Post->slug(),
		'class'=>'uk-width-1-1 uk-form-large',
		'tip'=>$L->g('you-can-modify-the-url-which-identifies'),
		'label'=>$L->g('Friendly URL')
	));

	echo '</li>';
	echo '</ul>';

echo '</div>';
echo '</div>';

HTML::formClose();

?>

<script>

$(document).ready(function() {

	var key = $("#jskey").val();

	$("#jsdate").datetimepicker({format:"<?php echo DB_DATE_FORMAT ?>"});

	$("#jstitle").keyup(function() {
		var slug = $(this).val();
		checkSlugPost(slug, key, $("#jsslug"));
	});

	$("#jsslug").keyup(function() {
		var slug = $("#jsslug").val();
		checkSlugPost(slug, key, $("#jsslug"));
	});

	$("#jsdelete-post").click(function() {
		if(confirm("<?php $Language->p('confirm-delete-this-action-cannot-be-undone') ?>")==false) {
			return false;
		}
	});

});

</script>