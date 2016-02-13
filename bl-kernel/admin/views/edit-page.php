<?php

HTML::title(array('title'=>$L->g('Edit page'), 'icon'=>'file-text-o'));

HTML::formOpen(array('class'=>'uk-form-stacked'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	// Key input
	HTML::formInputHidden(array(
		'name'=>'key',
		'value'=>$_Page->key()
	));

// ---- LEFT SIDE ----
echo '<div class="uk-grid">';
echo '<div class="uk-width-large-8-10">';

	// Title input
	HTML::formInputText(array(
		'name'=>'title',
		'value'=>$_Page->title(),
		'class'=>'uk-width-1-1 uk-form-large',
		'placeholder'=>$L->g('Title')
	));

	// Content input
	HTML::formTextarea(array(
		'name'=>'content',
		'value'=>$_Page->contentRaw(false),
		'class'=>'uk-width-1-1 uk-form-large',
		'placeholder'=>''
	));


	// Form buttons
	echo '<div class="uk-form-row uk-margin-bottom">';
	echo '	<button class="uk-button uk-button-primary" type="submit">'.$L->g('Save').'</button>';

if(count($_Page->children())===0)
{
	echo '	<button id="jsdelete" name="delete-page" class="uk-button" type="submit">'.$L->g('Delete').'</button>';
	echo '	<a class="uk-button" href="'.HTML_PATH_ADMIN_ROOT.'manage-posts">'.$L->g('Cancel').'</a>';
}

	echo '</div>';

echo '</div>';

// ---- RIGHT SIDE ----
echo '<div class="sidebar uk-width-large-2-10">';

	// Tabs, general and advanced mode
	echo '<ul class="uk-tab" data-uk-tab="{connect:\'#tab-options\'}">';
	echo '<li><a href="">'.$L->g('General').'</a></li>';
	echo '<li><a href="">'.$L->g('Images').'</a></li>';
	echo '<li><a href="">'.$L->g('Advanced').'</a></li>';
	echo '</ul>';

	echo '<ul id="tab-options" class="uk-switcher uk-margin">';

	// ---- GENERAL TAB ----
	echo '<li>';

	// Description input
	HTML::formTextarea(array(
		'name'=>'description',
		'label'=>$L->g('description'),
		'value'=>$_Page->description(),
		'rows'=>'4',
		'class'=>'uk-width-1-1 uk-form-medium',
		'tip'=>$L->g('this-field-can-help-describe-the-content')
	));

	// Tags input
	HTML::tags(array(
		'name'=>'tags',
		'label'=>$L->g('Tags'),
		'allTags'=>$dbTags->getAll(),
		'selectedTags'=>$_Page->tags(true)
	));

	echo '</li>';

	// ---- IMAGES TAB ----
	echo '<li>';

	// --- BLUDIT COVER IMAGE ---
	echo '<hr>';
	HTML::bluditCoverImage($_Page->coverImage(false));
	echo '<hr>';

	// --- BLUDIT QUICK IMAGES ---
	HTML::bluditQuickImages();

	// --- BLUDIT IMAGES V8 ---
	HTML::bluditImagesV8();

	// --- BLUDIT MENU V8 ---
	HTML::bluditMenuV8();

	echo '</li>';

	// ---- ADVANCED TAB ----
	echo '<li>';

	// Status input
	HTML::formSelect(array(
		'name'=>'status',
		'label'=>$L->g('Status'),
		'class'=>'uk-width-1-1 uk-form-medium',
		'options'=>array('published'=>$L->g('Published'), 'draft'=>$L->g('Draft')),
		'selected'=>($_Page->draft()?'draft':'published'),
		'tip'=>''
	));

// If the page is parent then doesn't can have a parent.
if(count($_Page->children())===0)
{
	// Parent input
	$options = array();
	$options[NO_PARENT_CHAR] = '('.$Language->g('No parent').')';
	$options += $dbPages->parentKeyList();
	unset($options[$_Page->key()]);

	HTML::formSelect(array(
		'name'=>'parent',
		'label'=>$L->g('Parent'),
		'class'=>'uk-width-1-1 uk-form-medium',
		'options'=>$options,
		'selected'=>$_Page->parentKey(),
		'tip'=>''
	));
}

	// Position input
	HTML::formInputText(array(
		'name'=>'position',
		'value'=>$_Page->position(),
		'class'=>'uk-width-1-1 uk-form-large',
		'label'=>$L->g('Position')
	));

	// Slug input
	HTML::formInputText(array(
		'name'=>'slug',
		'value'=>$_Page->slug(),
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

$(document).ready(function()
{
    var key = $("#jskey").val();

    $("#jsslug").keyup(function() {
        var text = $(this).val();
        var parent = $("#jsparent").val();

        checkSlugPage(text, parent, key, $("#jsslug"));
    });

    $("#jstitle").keyup(function() {
        var text = $(this).val();
        var parent = $("#jsparent").val();

        checkSlugPage(text, parent, key, $("#jsslug"));
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

        checkSlugPage(text, parent, key, $("#jsslug"));
    });

    $("#jsdelete").click(function() {
        if(confirm("<?php $Language->p('confirm-delete-this-action-cannot-be-undone') ?>")==false) {
            return false;
        }
    });

});

</script>
