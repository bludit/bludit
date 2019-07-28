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
		'value'=>$page->uuid()
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
<div id="jseditorToolbar">
	<div id="jseditorToolbarRight" class="btn-group btn-group-sm float-right" role="group" aria-label="Toolbar right">
		<button type="button" class="btn btn-light" id="jsmediaManagerOpenModal" data-toggle="modal" data-target="#jsmediaManagerModal"><span class="fa fa-image"></span> <?php $L->p('Images') ?></button>
		<button type="button" class="btn btn-light" id="jsoptionsSidebar" style="z-index:30"><span class="fa fa-cog"></span> <?php $L->p('Options') ?></button>
	</div>

	<div id="jseditorToolbarLeft">
		<button type="button" class="btn btn-sm btn-primary" id="jsbuttonSave"><?php echo $L->g('Save') ?></button>
		<button id="jsbuttonPreview" type="button" class="btn btn-sm btn-secondary"><?php $L->p('Preview') ?></button>
		<span id="jsswitchButton" data-switch="<?php echo ($page->draft()?'draft':'publish') ?>" class="ml-2 text-secondary switch-button"><i class="fa fa-square switch-icon-<?php echo ($page->draft()?'draft':'publish') ?>"></i> <?php echo ($page->draft()?$L->g('Draft'):$L->g('Publish')) ?></span>
	</div>

	<?php if($page->scheduled()): ?>
	<div class="alert alert-warning p-1 mt-1 mb-0"><?php $L->p('scheduled') ?>: <?php echo $page->date(SCHEDULED_DATE_FORMAT) ?></div>
	<?php endif; ?>
</div>
<script>
	$(document).ready(function() {
		$("#jsoptionsSidebar").on("click", function() {
			$("#jseditorSidebar").toggle();
			$("#jsshadow").toggle();
		});

		$("#jsshadow").on("click", function() {
			$("#jseditorSidebar").toggle();
			$("#jsshadow").toggle();
		});
	});
</script>

<!-- SIDEBAR OPTIONS -->
<div id="jseditorSidebar">
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			<a class="nav-link active show" id="nav-general-tab"  data-toggle="tab" href="#nav-general"  role="tab" aria-controls="general"><?php $L->p('General') ?></a>
			<a class="nav-link" id="nav-advanced-tab" data-toggle="tab" href="#nav-advanced" role="tab" aria-controls="advanced"><?php $L->p('Advanced') ?></a>
			<a class="nav-link" id="nav-seo-tab" data-toggle="tab" href="#nav-seo" role="tab" aria-controls="seo"><?php $L->p('SEO') ?></a>
		</div>
	</nav>

	<div class="tab-content pr-3 pl-3 pb-3">
		<div id="nav-general" class="tab-pane fade show active" role="tabpanel" aria-labelledby="general-tab">
			<?php
				// Category
				echo Bootstrap::formSelectBlock(array(
					'name'=>'category',
					'label'=>$L->g('Category'),
					'selected'=>$page->categoryKey(),
					'class'=>'',
					'emptyOption'=>'- '.$L->g('Uncategorized').' -',
					'options'=>$categories->getKeyNameArray()
				));

				// Description
				echo Bootstrap::formTextareaBlock(array(
					'name'=>'description',
					'label'=>$L->g('Description'),
					'selected'=>'',
					'class'=>'',
					'value'=>$page->description(),
					'rows'=>5,
					'placeholder'=>$L->get('this-field-can-help-describe-the-content')
				));
			?>

			<!-- Cover Image -->
			<?php
				$coverImage = $page->coverImage(false);
				$externalCoverImage = '';
				if (filter_var($coverImage, FILTER_VALIDATE_URL)) {
					$coverImage = '';
					$externalCoverImage = $page->coverImage(false);
				}
			?>
			<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100"><?php $L->p('Cover Image') ?></label>
			<div>
			<img id="jscoverImagePreview" class="mx-auto d-block w-100" alt="Cover image preview" src="<?php echo (empty($coverImage) ? HTML_PATH_CORE_IMG.'default.svg' : $page->coverImage() ) ?>" />
			</div>
			<div class="mt-2 text-center">
				<button type="button" id="jsbuttonSelectCoverImage" class="btn btn-primary btn-sm"><?php echo $L->g('Select cover image') ?></button>
				<button type="button" id="jsbuttonRemoveCoverImage" class="btn btn-secondary btn-sm"><?php echo $L->g('Remove cover image') ?></button>
			</div>
			<script>
				$(document).ready(function() {
					$("#jscoverImagePreview").on("click", function() {
						openMediaManager();
					});

					$("#jsbuttonSelectCoverImage").on("click", function() {
						openMediaManager();
					});

					$("#jsbuttonRemoveCoverImage").on("click", function() {
						$("#jscoverImage").val('');
						$("#jscoverImagePreview").attr('src', HTML_PATH_CORE_IMG+'default.svg');
					});
				});
			</script>
		</div>
		<div id="nav-advanced" class="tab-pane fade" role="tabpanel" aria-labelledby="advanced-tab">
			<?php
				// Date
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'date',
					'label'=>$L->g('Date'),
					'placeholder'=>'',
					'value'=>$page->dateRaw(),
					'tip'=>$L->g('date-format-format')
				));

				// Type
				echo Bootstrap::formSelectBlock(array(
					'name'=>'typeSelector',
					'label'=>$L->g('Type'),
					'selected'=>$page->type(),
					'options'=>array(
						'published'=>'- '.$L->g('Default').' -',
						'sticky'=>$L->g('Sticky'),
						'static'=>$L->g('Static')
					),
					'tip'=>''
				));

				// Position
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'position',
					'label'=>$L->g('Position'),
					'tip'=>$L->g('Field used when ordering content by position'),
					'value'=>$page->position()
				));

				// Tags
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'tags',
					'label'=>$L->g('Tags'),
					'placeholder'=>'',
					'tip'=>$L->g('Write the tags separated by comma'),
					'value'=>$page->tags()
				));

				// Parent
				try {
					$parentKey = $page->parent();
					$parent = new Page($parentKey);
					$parentValue = $parent->title();
				} catch (Exception $e) {
					$parentValue = '';
				}
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'parentTMP',
					'label'=>$L->g('Parent'),
					'placeholder'=>'',
					'tip'=>$L->g('Start typing a page title to see a list of suggestions.'),
					'value'=>$parentValue
				));

				// Template
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'template',
					'label'=>$L->g('Template'),
					'placeholder'=>'',
					'value'=>$page->template(),
					'tip'=>$L->g('Write a template name to filter the page in the theme and change the style of the page.')
				));

				echo Bootstrap::formInputTextBlock(array(
					'name'=>'externalCoverImage',
					'label'=>$L->g('External cover image'),
					'placeholder'=>"https://",
					'value'=>$externalCoverImage,
					'tip'=>$L->g('Set a cover image from external URL, such as a CDN or some server dedicated for images.')
				));

				// Username
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'',
					'label'=>$L->g('Author'),
					'placeholder'=>'',
					'value'=>$page->username(),
					'tip'=>'',
					'disabled'=>true
				));
			?>
			<script>
			$(document).ready(function() {
				// Changes in External cover image input
				$("#jsexternalCoverImage").change(function() {
					$("#jscoverImage").val( $(this).val() );
				});

				// Parent
				$("#jsparentTMP").change(function() {
					var parent = $("#jsparentTMP").val();
					if (parent.length===0) {
						$("#jsparent").val("");
					}
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
		<div id="nav-seo" class="tab-pane fade" role="tabpanel" aria-labelledby="seo-tab">
			<?php
				// Friendly URL
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'slug',
					'tip'=>$L->g('URL associated with the content'),
					'label'=>$L->g('Friendly URL'),
					'placeholder'=>$L->g('Leave empty for autocomplete by Bludit.'),
					'value'=>$page->slug()
				));

				// Robots
				echo Bootstrap::formCheckbox(array(
					'name'=>'noindex',
					'label'=>'Robots',
					'labelForCheckbox'=>$L->g('apply-code-noindex-code-to-this-page'),
					'placeholder'=>'',
					'checked'=>$page->noindex(),
					'tip'=>$L->g('This tells search engines not to show this page in their search results.')
				));

				// Robots
				echo Bootstrap::formCheckbox(array(
					'name'=>'nofollow',
					'label'=>'',
					'labelForCheckbox'=>$L->g('apply-code-nofollow-code-to-this-page'),
					'placeholder'=>'',
					'checked'=>$page->nofollow(),
					'tip'=>$L->g('This tells search engines not to follow links on this page.')
				));

				// Robots
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
	</div>
</div>

<!-- Title -->
<div class="form-group mt-1 mb-1">
	<input id="jstitle" name="title" type="text" class="form-control form-control-lg rounded-0" value="<?php echo $page->title() ?>" placeholder="<?php $L->p('Enter title') ?>">
</div>

<!-- Editor -->
<textarea id="jseditor" class="editable h-100" style=""><?php echo $page->contentRaw(true) ?></textarea>

</form>

<!-- Modal for Delete page -->
<div id="jsdeletePageModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<h3><?php $L->p('Delete content') ?></h3>
				<p><?php $L->p('Are you sure you want to delete this page') ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link" data-dismiss="modal"><?php $L->p('Cancel') ?></button>
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

	// Define function if they doesn't exist
	// This helps if the user doesn't activate any plugin as editor
	if (typeof editorGetContent != "function") {
		window.editorGetContent = function(){
			return $("#jseditor").val();
		};
	}
	if (typeof editorInsertMedia != "function") {
		window.editorInsertMedia = function(filename){
			$("#jseditor").val($('#jseditor').val()+'<img src="'+filename+'" alt="">');
		};
	}

	// Button switch
	$("#jsswitchButton").on("click", function() {
		if ($(this).data("switch")=="publish") {
			$(this).html('<i class="fa fa-square switch-icon-draft"></i> <?php $L->p('Draft') ?>');
			$(this).data("switch", "draft");
		} else {
			$(this).html('<i class="fa fa-square switch-icon-publish"></i> <?php $L->p('Publish') ?>');
			$(this).data("switch", "publish");
		}
	});

	// Button preview
	$("#jsbuttonPreview").on("click", function() {
		var uuid = $("#jsuuid").val();
		var title = $("#jstitle").val();
		var content = editorGetContent();
		var ajax = new bluditAjax();
		bluditAjax.saveAsDraft(uuid, title, content).then(function(data) {
			window.open("<?php echo DOMAIN_PAGES.'autosave-'.$page->uuid().'?preview='.md5('autosave-'.$page->uuid()) ?>", "_blank");
		});
	});

	// Button Save
	$("#jsbuttonSave").on("click", function() {
		// If the switch is setted to "published", get the value from the selector
		if ($("#jsswitchButton").data("switch")=="publish") {
			var value = $("#jstypeSelector option:selected").val();
			$("#jstype").val(value);
		} else {
			$("#jstype").val("draft");
		}

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
	var currentContent = editorGetContent();
	setInterval(function() {
			var uuid = $("#jsuuid").val();
			var title = $("#jstitle").val() + "[<?php $L->p('Autosave') ?>]";
			var content = editorGetContent();
			// Autosave when content has at least 100 characters
			if (content.length<100) {
				return false;
			}
			// Autosave only when the user change the content
			if (currentContent!=content) {
				currentContent = content;
				bluditAjax.saveAsDraft(uuid, title, content).then(function(data) {
					if (data.status==0) {
						showAlert("<?php $L->p('Autosave') ?>");
					}
				});
			}
	},1000*60*AUTOSAVE_INTERVAL);

});
</script>
