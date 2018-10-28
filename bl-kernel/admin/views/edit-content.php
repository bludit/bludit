<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php

// FORM START
echo Bootstrap::formOpen(array(
	'id'=>'jsform',
	'class'=>'d-flex flex-column h-100'
));

	// Token CSRF
	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	// Parent
	echo Bootstrap::formInputHidden(array(
		'name'=>'parent',
		'value'=>$page->parent()
	));

	// UUID
	// The UUID is generated in the controller
	echo Bootstrap::formInputHidden(array(
		'name'=>'uuid',
		'value'=>$uuid
	));

	// Type = published, draft, sticky, static
	echo Bootstrap::formInputHidden(array(
		'name'=>'type',
		'value'=>$page->type()
	));

	// Cover image
	echo Bootstrap::formInputHidden(array(
		'name'=>'coverImage',
		'value'=>$page->coverImage(false)
	));

	// Content
	echo Bootstrap::formInputHidden(array(
		'name'=>'content',
		'value'=>''
	));

	// Current page key
	echo Bootstrap::formInputHidden(array(
		'name'=>'key',
		'value'=>$page->key()
	));
?>

<!-- TOOLBAR -->
<div>
	<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
		<button type="button" class="btn btn-light" id="jsmediaManagerOpenModal" data-toggle="modal" data-target="#jsmediaManagerModal"><?php $L->p('Images') ?></button>
		<button type="button" class="btn btn-light" id="jscoverImageOpenModal" data-toggle="modal" data-target="#jscoverImageModal"><?php $L->p('Cover image') ?></button>
		<button type="button" class="btn btn-light" id="jscategoryOpenModal" data-toggle="modal" data-target="#jscategoryModal"><?php $L->p('Category') ?><span class="option"></span></button>
		<button type="button" class="btn btn-light" id="jsdescriptionOpenModal" data-toggle="modal" data-target="#jsdescriptionModal"><?php $L->p('Description') ?><span class="option"></span></button>
		<button type="button" class="btn btn-light" id="jsoptionsOpenModal" data-toggle="modal" data-target="#jsoptionsModal"><?php $L->p('More options') ?></button>
	</div>

	<div class="btn-group btn-group-sm float-right" role="group" aria-label="Basic example">
		<button type="button" class="btn btn-primary" id="jsbuttonSave"><?php echo ($page->draft()?$L->g('Publish'):$L->g('Save')) ?></button>
		<?php if(!$page->draft()): ?>
		<button type="button" class="btn btn-secondary" id="jsbuttonDraft"><?php $L->p('Save as draft') ?></button>
		<?php endif; ?>
		<?php if (count($page->children())===0): ?>
		<button type="button" class="btn btn-danger" id="jsbuttonDelete" data-toggle="modal" data-target="#jsdeletePageModal"><?php $L->p('Delete') ?></button>
		<?php endif; ?>
		<a href="<?php echo HTML_PATH_ADMIN_ROOT ?>dashboard" class="btn btn-secondary"><?php $L->p('Cancel') ?></a>
	</div>
</div>

<!-- Title -->
<div class="form-group mt-1 mb-1">
	<input id="jstitle" name="title" type="text" class="form-control form-control-lg rounded-0" value="<?php echo $page->title() ?>" placeholder="<?php $L->p('Enter title') ?>">
</div>

<!-- Editor -->
<div id="jseditor" class="editable h-100" style=""><?php echo $page->contentRaw(false) ?></div>

<!-- Modal for Cover Image -->
<div id="jscoverImageModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php $L->p('Cover Image') ?></h5>
			</div>
			<div class="modal-body">
				<?php
					$coverImage = $page->coverImage(false);
					$externalCoverImage = '';
					if (filter_var($coverImage, FILTER_VALIDATE_URL)) {
						$coverImage = '';
						$externalCoverImage = $page->coverImage(false);
					}
				?>

				<div>
					<img id="jscoverImagePreview" style="width: 350px; height: 200px;" class="mx-auto d-block" alt="Cover image preview" src="<?php echo (empty($coverImage) ? HTML_PATH_ADMIN_THEME_IMG.'default.svg' : $page->coverImage() ) ?>" />
				</div>
				<div class="mt-2 text-center">
					<button type="button" id="jsbuttonSelectCoverImage" class="btn btn-primary btn-sm"><?php echo $L->g('Select cover image') ?></button>
					<button type="button" id="jsbuttonRemoveCoverImage" class="btn btn-secondary btn-sm"><?php echo $L->g('Remove cover image') ?></button>
				</div>

				<hr>

				<?php
					echo Bootstrap::formTitle(array('title'=>$L->g('External Cover Image')));

					echo Bootstrap::formInputTextBlock(array(
						'name'=>'externalCoverImage',
						'placeholder'=>"https://",
						'value'=>$externalCoverImage,
						'tip'=>$L->g('Set a cover image from external URL, such as a CDN or some server dedicated for images.')
					));
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal"><?php $L->p('Done') ?></button>
			</div>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		$("#jsexternalCoverImage").change(function() {
			$("#jscoverImage").val( $(this).val() );
		});

		$("#jscoverImagePreview").on("click", function() {
			openMediaManager();
		});

		$("#jsbuttonSelectCoverImage").on("click", function() {
			openMediaManager();
		});

		$("#jsbuttonRemoveCoverImage").on("click", function() {
			$("#jscoverImage").val('');
			$("#jscoverImagePreview").attr('src', HTML_PATH_ADMIN_THEME_IMG+'default.svg');
		});
	});
	</script>
</div>

<!-- Modal for Categories -->
<div id="jscategoryModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php $L->p('Category') ?></h5>
			</div>
			<div class="modal-body">
				<?php
					echo Bootstrap::formSelectBlock(array(
						'name'=>'category',
						'label'=>'',
						'selected'=>$page->categoryKey(),
						'class'=>'',
						'emptyOption'=>'- '.$L->g('Uncategorized').' -',
						'options'=>$categories->getKeyNameArray()
					));
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal"><?php $L->p('Done') ?></button>
			</div>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		function setCategoryBox(value) {
			var selected = $("#jscategory option:selected");
			var value = selected.val().trim();
			if (value) {
				$("#jscategoryOpenModal").find("span.option").html(": "+selected.text());
			} else {
				$("#jscategoryOpenModal").find("span.option").html("");
			}
		}

		// Set the current category selected
		setCategoryBox();

		// When the user select the category update the category button
		$("#jscategory").on("change", function() {
			setCategoryBox();
		});
	});
	</script>
</div>

<!-- Modal for Description -->
<div id="jsdescriptionModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php $L->p('Description') ?></h5>
			</div>
			<div class="modal-body">
				<?php
					echo Bootstrap::formTextareaBlock(array(
						'name'=>'description',
						'label'=>'',
						'selected'=>'',
						'class'=>'',
						'value'=>$page->description(),
						'rows'=>3,
						'placeholder'=>$L->get('this-field-can-help-describe-the-content')
					));
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal"><?php $L->p('Done') ?></button>
			</div>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		function setDescriptionBox(value) {
			var value = $("#jsdescription").val();
			if (value) {
				value = ": "+$.trim(value).substring(0, 30).split(" ").slice(0, -1).join(" ") + "...";
			}
			$("#jsdescriptionOpenModal").find("span.option").html(value);
		}

		// Set the current description
		setDescriptionBox();

		// When the user write the description update the description button
		$("#jsdescription").on("change", function() {
			setDescriptionBox();
		});
	});
	</script>
</div>

<!-- Modal for More options -->
<div id="jsoptionsModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php $L->p('More options') ?></h5>
			</div>
			<div class="modal-body">
				<?php
				// Username
				echo Bootstrap::formInputText(array(
					'name'=>'',
					'label'=>$L->g('Author'),
					'placeholder'=>'',
					'value'=>$page->username(),
					'tip'=>'',
					'disabled'=>true
				));

				// Date
				echo Bootstrap::formInputText(array(
					'name'=>'date',
					'label'=>$L->g('Date'),
					'placeholder'=>'',
					'value'=>$page->dateRaw(),
					'tip'=>$L->g('date-format-format')
				));

				// Type
				echo Bootstrap::formSelect(array(
					'name'=>'typeTMP',
					'label'=>$L->g('Type'),
					'selected'=>$page->type(),
					'options'=>array(
						'published'=>'- '.$L->g('Default').' -',
						'sticky'=>$L->g('Sticky'),
						'static'=>$L->g('Static')
					),
					'tip'=>''
				));

				// Parent
				try {
					$parentKey = $page->parent();
					$parent = new Page($parentKey);
					$parentValue = $parent->title();
				} catch (Exception $e) {
					$parentValue = '';
				}
				echo Bootstrap::formInputText(array(
					'name'=>'parentTMP',
					'label'=>$L->g('Parent'),
					'placeholder'=>'',
					'tip'=>$L->g('Start typing a page title to see a list of suggestions.'),
					'value'=>$parentValue
				));

				// Position
				echo Bootstrap::formInputText(array(
					'name'=>'position',
					'label'=>$L->g('Position'),
					'tip'=>$L->g('Field used when ordering content by position'),
					'value'=>$page->position()
				));

				// Template
				echo Bootstrap::formInputText(array(
					'name'=>'template',
					'label'=>$L->g('Template'),
					'placeholder'=>'',
					'tip'=>$L->g('Write a template name to filter the page in the theme and change the style of the page.'),
					'value'=>$page->template()
				));

				// Tags
				echo Bootstrap::formInputText(array(
					'name'=>'tags',
					'label'=>$L->g('Tags'),
					'placeholder'=>'',
					'tip'=>$L->g('Write the tags separated by comma'),
					'value'=>$page->tags()
				));

				echo Bootstrap::formTitle(array('title'=>$L->g('SEO')));

				// Friendly URL
				echo Bootstrap::formInputText(array(
					'name'=>'slug',
					'tip'=>$L->g('URL associated with the content'),
					'label'=>$L->g('Friendly URL'),
					'placeholder'=>$L->g('Leave empty for autocomplete by Bludit.'),
					'value'=>$page->slug()
				));

				echo Bootstrap::formCheckbox(array(
					'name'=>'noindex',
					'label'=>'Robots',
					'labelForCheckbox'=>$L->g('apply-code-noindex-code-to-this-page'),
					'placeholder'=>'',
					'class'=>'mt-4',
					'checked'=>$page->noindex(),
					'tip'=>$L->g('This tells search engines not to show this page in their search results.')
				));

				echo Bootstrap::formCheckbox(array(
					'name'=>'nofollow',
					'label'=>'',
					'labelForCheckbox'=>$L->g('apply-code-nofollow-code-to-this-page'),
					'placeholder'=>'',
					'checked'=>$page->nofollow(),
					'tip'=>$L->g('This tells search engines not to follow links on this page.')
				));

				echo Bootstrap::formCheckbox(array(
					'name'=>'noarchive',
					'label'=>'',
					'labelForCheckbox'=>$L->g('apply-code-noarchive-code-to-this-page'),
					'placeholder'=>'',
					'checked'=>$page->noarchive(),
					'tip'=>$L->g('This tells search engines not to save a cached copy of this page.')
				));
			?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal"><?php $L->p('Done') ?></button>
			</div>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		// Generate slug when the user type the title
		$("#jstitle").keyup(function() {
			var text = $(this).val();
			var parent = $("#jsparent").val();
			var currentKey = "";
			var ajax = new bluditAjax();
			var callBack = $("#jsslug");
			ajax.generateSlug(text, parent, currentKey, callBack);
		});

		// Datepicker
		$("#jsdate").datetimepicker({format:DB_DATE_FORMAT});

		// Parent autocomplete
		var parentsXHR;
		var parentsList; // Keep the parent list returned to get the key by the title page
		$("#jsparentTMP").autoComplete({
			minChars: 1,
			source: function(term, response) {
				// Prevent call inmediatly another ajax request
				try { parentsXHR.abort(); } catch(e){}
				// Get the list of parent pages by title (term)
				parentsXHR = $.getJSON(HTML_PATH_ADMIN_ROOT+"ajax/get-parents", {query: term},
					function(data) {
						parentsList = data;
						term = term.toLowerCase();
						var matches = [];
						for (var title in data) {
							if (~title.toLowerCase().indexOf(term))
								matches.push(title);
						}
						response(matches);
				});
			},
			onSelect: function(event, term, item) {
				// parentsList = array( pageTitle => pageKey )
				var parentKey = parentsList[term];
				$("#jsparent").attr("value", parentKey);
			}
		});
	});
	</script>
</div>

</form>

<!-- Modal for Delete page -->
<div id="jsdeletePageModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php $L->p('Delete content') ?></h5>
			</div>
			<div class="modal-body">
				<?php $L->p('Are you sure you want to delete this page') ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php $L->p('Cancel') ?></button>
				<button type="button" class="btn btn-danger" data-dismiss="modal" id="jsbuttonDeleteAccept"><?php $L->p('Delete') ?></button>
			</div>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		$("#jsbuttonDeleteAccept").on("click", function() {
			$("#jstype").val("delete");
			$("#jscontent").val("");
			$("#jsform").submit();
		});
	});
	</script>
</div>

<!-- Modal for Media Manager -->
<?php include(PATH_ADMIN_THEMES.'booty/html/media.php'); ?>

<script>
$(document).ready(function() {

	// Button Publish or Save
	$("#jsbuttonSave").on("click", function() {
		// Get the type
		var type = $("#jstypeTMP option:selected").val();
		$("#jstype").val(type);

		// Get the content
		$("#jscontent").val( editorGetContent() );

		// Submit the form
		$("#jsform").submit();
	});

	// Button Save as draft
	$("#jsbuttonDraft").on("click", function() {
		// Set the type as draft
		$("#jstype").val("draft");

		// Get the content
		$("#jscontent").val( editorGetContent() );

		// Submit the form
		$("#jsform").submit();
	});

	// Autosave
	// Autosave works when the content of the page is bigger than 100 characters
	var currentContent = editorGetContent();
	setInterval(function() {
			var uuid = $("#jsuuid").val();
			var title = $("#jstitle").val();
			var content = editorGetContent();
			// Call autosave only when the user change the content
			if (currentContent!=content) {
				currentContent = content;
				var ajax = new bluditAjax();
				// showAlert is the function to display an alert defined in alert.php
				ajax.autosave(uuid, title, content, showAlert);
			}
	},1000*60*AUTOSAVE_INTERVAL);

});
</script>
