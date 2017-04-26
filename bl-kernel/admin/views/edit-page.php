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

	// Unique identifier
	HTML::formInputHidden(array(
		'name'=>'uniqueId',
		'value'=>$_Page->uniqueId()
	));

// LEFT SIDE
// --------------------------------------------------------------------
echo '<div class="uk-grid uk-grid-medium">';
echo '<div class="bl-publish-view uk-width-8-10">';

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
	echo '
		<button class="uk-button uk-button-primary" type="submit">'.$L->g('Save').'</button>
		<button class="uk-button uk-button-primary" type="button" id="jsSaveDraft">'.$L->g('Save as draft').'</button>
	';

if(count($_Page->children())===0)
{
	echo '	<button id="jsdelete" name="delete-page" class="uk-button" type="submit">'.$L->g('Delete').'</button>';
	echo '	<a class="uk-button" href="'.HTML_PATH_ADMIN_ROOT.'manage-posts">'.$L->g('Cancel').'</a>';
}

	echo '</div>';

echo '</div>';

// RIGHT SIDE
// --------------------------------------------------------------------
echo '<div class="bl-publish-sidebar uk-width-2-10">';

	echo '<ul>';

	// GENERAL TAB
	// --------------------------------------------------------------------
	echo '<li><h2 class="sidebar-button" data-view="sidebar-general-view"><i class="uk-icon-angle-down"></i> '.$L->g('General').'</h2></li>';
	echo '<li id="sidebar-general-view" class="sidebar-view">';

	// Description input
	HTML::formTextarea(array(
		'name'=>'description',
		'label'=>$L->g('description'),
		'value'=>$_Page->description(),
		'rows'=>'4',
		'class'=>'uk-width-1-1 uk-form-medium',
		'tip'=>$L->g('this-field-can-help-describe-the-content')
	));

	echo '</li>';

	// IMAGES TAB
	// --------------------------------------------------------------------
	echo '<li><h2 class="sidebar-button" data-view="sidebar-images-view"><i class="uk-icon-angle-down"></i> '.$L->g('Images').'</h2></li>';
	echo '<li id="sidebar-images-view" class="sidebar-view">';

	// --- BLUDIT COVER IMAGE ---
	HTML::bluditCoverImage($_Page->coverImage(false));

	// --- BLUDIT QUICK IMAGES ---
	HTML::bluditQuickImages();

	// --- BLUDIT IMAGES V8 ---
	HTML::bluditImagesV8();

	// --- BLUDIT MENU V8 ---
	HTML::bluditMenuV8();

	echo '</li>';


	// TAGS
	// --------------------------------------------------------------------
	echo '<li><h2 class="sidebar-button" data-view="sidebar-tags-view"><i class="uk-icon-angle-down"></i> '.$L->g('Tags').'</h2></li>';
	echo '<li id="sidebar-tags-view" class="sidebar-view">';

	// Tags input
	HTML::tags(array(
		'name'=>'tags',
		'label'=>$L->g('Tags'),
		'allTags'=>$dbTags->getAll(),
		'selectedTags'=>$_Page->tags(true)
	));

	echo '</li>';

	// ADVANCED TAB
	// --------------------------------------------------------------------
	echo '<li><h2 class="sidebar-button" data-view="sidebar-advanced-view"><i class="uk-icon-angle-down"></i> '.$L->g('Advanced').'</h2></li>';
	echo '<li id="sidebar-advanced-view" class="sidebar-view">';

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

	// Button Save as draft
	$("#jsSaveDraft").on("click", function() {
		$("#jsstatus").val("draft");
		$(".uk-form").submit();
	});

	// Right sidebar
	$(".sidebar-button").click(function() {
		var view = "#" + $(this).data("view");

		if( $(view).is(":visible") ) {
			$(view).hide();
		}
		else {
			$(".sidebar-view").hide();
			$(view).show();			
		}
	});

});

</script>