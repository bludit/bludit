<?php

HTML::title(array('title'=>$L->g('New page'), 'icon'=>'file-text-o'));

HTML::formOpen(array('class'=>'uk-form-stacked'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getToken()
	));

// ---- LEFT SIDE ----
echo '<div class="uk-grid">';
echo '<div class="uk-width-large-7-10">';

	// Title input
	HTML::formInputText(array(
		'name'=>'title',
		'value'=>'',
		'class'=>'uk-width-1-1 uk-form-large',
		'placeholder'=>$L->g('Title')
	));

	// Content input
	HTML::formTextarea(array(
		'name'=>'content',
		'value'=>'',
		'class'=>'uk-width-1-1 uk-form-large',
		'placeholder'=>$L->g('Content')
	));

	// Form buttons
	echo '<div class="uk-form-row uk-margin-bottom">
		<button class="uk-button uk-button-primary" type="submit">'.$L->g('Save').'</button>
		<a class="uk-button" href="'.HTML_PATH_ADMIN_ROOT.'manage-pages">'.$L->g('Cancel').'</a>
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
		'value'=>'',
		'rows'=>'7',
		'class'=>'uk-width-1-1 uk-form-medium',
		'tip'=>$L->g('this-field-can-help-describe-the-content')
	));

	// Tags input
	HTML::formInputText(array(
		'name'=>'tags',
		'value'=>'',
		'class'=>'uk-width-1-1 uk-form-large',
		'tip'=>$L->g('Write the tags separated by commas'),
		'label'=>$L->g('Tags')
	));

	echo '</li>';

	// ---- ADVANCED TAB ----
	echo '<li>';

	// Parent input
	$options = array();
	$options[NO_PARENT_CHAR] = '('.$Language->g('No parent').')';
	$options += $dbPages->parentKeyList();

	HTML::formSelect(array(
		'name'=>'parent',
		'label'=>$L->g('Parent'),
		'class'=>'uk-width-1-1 uk-form-medium',
		'options'=>$options,
		'selected'=>NO_PARENT_CHAR,
		'tip'=>''
	));

	// Status input
	HTML::formSelect(array(
		'name'=>'status',
		'label'=>$L->g('Status'),
		'class'=>'uk-width-1-1 uk-form-medium',
		'options'=>array('published'=>$L->g('Published'), 'draft'=>$L->g('Draft')),
		'selected'=>'published',
		'tip'=>''
	));

	// Position input
	HTML::formInputText(array(
		'name'=>'position',
		'value'=>'1',
		'class'=>'uk-width-1-1 uk-form-large',
		'label'=>$L->g('Position')
	));

	// Slug input
	HTML::formInputText(array(
		'name'=>'slug',
		'value'=>'',
		'class'=>'uk-width-1-1 uk-form-large',
		'tip'=>$L->g('you-can-modify-the-url-which-identifies'),
		'label'=>$L->g('Friendly URL')
	));

	echo '</li>';
	echo '<ul>';

echo '</div>';
echo '</div>';

HTML::formClose();

?>

<script>

$(document).ready(function()
{

    $("#jsslug").keyup(function() {
        var text = $(this).val();
        var parent = $("#jsparent").val();

        checkSlugPage(text, parent, "", $("#jsslug"));
    });

    $("#jstitle").keyup(function() {
        var text = $(this).val();
        var parent = $("#jsparent").val();

        checkSlugPage(text, parent, "", $("#jsslug"));
    });

    $("#jsparent").change(function() {
        var parent = $(this).val();
        var text = $("#jsslug").val();

        if(parent==NO_PARENT_CHAR) {
            $("#jsparentExample").text("");
        }
        else {
            $("#jsparentExample").text(parent+"/");
        }

        checkSlugPage(text, parent, "", $("#jsslug"));
    });

});

</script>