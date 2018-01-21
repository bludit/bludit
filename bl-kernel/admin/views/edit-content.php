<?php

HTML::title(array('title'=>$L->g('Edit content'), 'icon'=>'file-text-o'));

HTML::formOpen(array('class'=>'uk-form-stacked'));

	// Security token
	HTML::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	// Key input
	HTML::formInputHidden(array(
		'name'=>'key',
		'value'=>$page->key()
	));

// LEFT SIDE
// --------------------------------------------------------------------
echo '<div class="uk-grid uk-grid-medium">';
echo '<div class="bl-publish-view uk-width-8-10">';

	// Title input
	HTML::formInputText(array(
		'name'=>'title',
		'value'=>$page->title(),
		'class'=>'uk-width-1-1 uk-form-large',
		'placeholder'=>$L->g('Title')
	));

	// Content input
	HTML::formTextarea(array(
		'name'=>'content',
		'value'=>$page->contentRaw(false),
		'class'=>'uk-width-1-1 uk-form-large',
		'placeholder'=>''
	));

	// Form buttons
	echo '<div class="uk-form-row uk-margin-bottom">';
	echo '
		<button class="uk-button uk-button-primary" type="submit">'.$L->g('Save').'</button>
		<button class="uk-button uk-button-primary" type="button" id="jsSaveDraft">'.$L->g('Save as draft').'</button>
	';

if(count($page->children())===0)
{
	echo '	<button id="jsdelete" name="delete-page" class="uk-button" type="submit">'.$L->g('Delete').'</button>';
	echo '	<a class="uk-button" href="'.HTML_PATH_ADMIN_ROOT.'content">'.$L->g('Cancel').'</a>';
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

	// Category
	HTML::formSelect(array(
		'name'=>'category',
		'label'=>$L->g('Category'),
		'class'=>'uk-width-1-1 uk-form-medium',
		'options'=>$dbCategories->getKeyNameArray(),
		'selected'=>$page->categoryKey(),
		'tip'=>'',
		'addEmptySpace'=>true
	));

	// Description input
	HTML::formTextarea(array(
		'name'=>'description',
		'label'=>$L->g('description'),
		'value'=>$page->description(),
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
	$coverImage = $page->coverImage(false);
	$externalCoverImage = '';
	if (filter_var($coverImage, FILTER_VALIDATE_URL)) {
		$coverImage = '';
		$externalCoverImage = $page->coverImage(false);
	}

	HTML::bluditCoverImage($coverImage);

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
		'allTags'=>$dbTags->getKeyNameArray(),
		'selectedTags'=>$page->tags(true)
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
		'options'=>array(
			'published'=>$L->g('Published'),
			'static'=>$L->g('Static'),
			'draft'=>$L->g('Draft')
		),
		'selected'=>$page->status(),
		'tip'=>''
	));

	// Date input
	HTML::formInputText(array(
		'name'=>'date',
		'value'=>$page->dateRaw(),
		'class'=>'uk-width-1-1 uk-form-medium',
		'tip'=>$L->g('To schedule the content select the date and time'),
		'label'=>$L->g('Date')
	));

	echo '<hr>';

	// Parent input
	// Check if the page has children
	if (count($page->children())==0) {
		$options = array(' '=>'- '.$L->g('No parent').' -');
		$parentsList = $dbPages->getParents();
		foreach ($parentsList as $pageKey) {
			$parent = buildPage($pageKey);
			$options[$pageKey] = $parent->title();
		}
		unset($options[$page->key()]);

		HTML::formSelect(array(
			'name'=>'parent',
			'label'=>$L->g('Parent'),
			'class'=>'uk-width-1-1 uk-form-medium',
			'options'=>$options,
			'selected'=>$page->parentKey(),
			'tip'=>'',
			'disabled'=>$page->status()=='static'
		));

		echo '<hr>';
	}

	// Position input
	HTML::formInputText(array(
		'name'=>'position',
		'value'=>$page->position(),
		'class'=>'uk-width-1-1 uk-form-medium',
		'label'=>$L->g('Position'),
		'tip'=>$L->g('This field is used when you order the content by position')
	));

	echo '<hr>';

	// External Coverimage
	HTML::formInputText(array(
		'name'=>'externalCoverImage',
		'value'=>$externalCoverImage,
		'class'=>'uk-width-1-1 uk-form-medium',
		'label'=>$L->g('External Cover Image'),
		'tip'=>$L->g('Full image URL')
	));

	echo '<hr>';

	// Slug input
	HTML::formInputText(array(
		'name'=>'slug',
		'value'=>$page->slug(),
		'class'=>'uk-width-1-1 uk-form-medium',
		'tip'=>$L->g('URL associated with the content'),
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

	$("#jsdate").datetimepicker({format:"<?php echo DB_DATE_FORMAT ?>"});

	$("#jsslug").keyup(function() {
		var text = $(this).val();
		var parent = $("#jsparent").val();

		generateSlug(text, parent, key, $("#jsslug"));
	});

	$("#jstitle").keyup(function() {
		var text = $(this).val();
		var parent = $("#jsparent").val();

		generateSlug(text, parent, key, $("#jsslug"));
	});

	$("#jsparent").change(function() {
		var parent = $(this).val();
		var text = $("#jsslug").val();

		if (parent=="") {
			$("#jsparentExample").text("");
		}
		else {
			$("#jsparentExample").text(parent+"/");
		}

		generateSlug(text, parent, key, $("#jsslug"));
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

	$("#jsstatus").change(function() {
		if ($(this).val()=='static') {
			$("#jsparent").val(' ');
			$("#jsparent").attr('disabled','disabled');
		} else {
			$("#jsparent").removeAttr('disabled');
		}
	});

});

</script>