<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php

// Start form
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
		'value'=>''
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
		'value'=>'published'
	));

	// Cover image
	echo Bootstrap::formInputHidden(array(
		'name'=>'coverImage',
		'value'=>''
	));

	// Content
	echo Bootstrap::formInputHidden(array(
		'name'=>'content',
		'value'=>''
	));
?>

<!-- TOOLBAR -->
<div id="jseditorToolbar">
	<div id="jseditorToolbarRight" class="btn-group btn-group-sm float-right" role="group" aria-label="Toolbar right">
		<button type="button" class="btn btn-light" id="jsmediaManagerOpenModal" data-toggle="modal" data-target="#jsmediaManagerModal"><span class="fa fa-image"></span> <?php $L->p('Images') ?></button>
		<button type="button" class="btn btn-light" id="jsoptionsSidebar" style="z-index:30"><span class="fa fa-cog"></span> <?php $L->p('Options') ?></button>
	</div>

	<div id="jseditorToolbarLeft">
		<button id="jsbuttonSave" type="button" class="btn btn-sm btn-primary" ><?php $L->p('Save') ?></button>
		<button id="jsbuttonPreview" type="button" class="btn btn-sm btn-secondary"><?php $L->p('Preview') ?></button>
		<span id="jsbuttonSwitch" data-switch="publish" class="ml-2 text-secondary switch-button"><i class="fa fa-square switch-icon-publish"></i> <?php $L->p('Publish') ?></span>
	</div>
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
					'selected'=>'',
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
					'value'=>'',
					'rows'=>5,
					'placeholder'=>$L->get('this-field-can-help-describe-the-content')
				));
			?>

			<!-- Cover Image -->
			<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100"><?php $L->p('Cover Image') ?></label>
			<div>
				<img id="jscoverImagePreview" class="mx-auto d-block w-100" alt="Cover image preview" src="<?php echo HTML_PATH_CORE_IMG ?>default.svg" />
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
					'value'=>Date::current(DB_DATE_FORMAT),
					'tip'=>$L->g('date-format-format')
				));

				// Type
				echo Bootstrap::formSelectBlock(array(
					'name'=>'typeSelector',
					'label'=>$L->g('Type'),
					'selected'=>'',
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
					'value'=>$pages->nextPositionNumber()
				));

				// Tags
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'tags',
					'label'=>$L->g('Tags'),
					'placeholder'=>'',
					'tip'=>$L->g('Write the tags separated by comma')
				));

				// Parent
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'parentTMP',
					'label'=>$L->g('Parent'),
					'placeholder'=>'',
					'tip'=>$L->g('Start typing a page title to see a list of suggestions.'),
					'value'=>''
				));

				// Template
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'template',
					'label'=>$L->g('Template'),
					'placeholder'=>'',
					'value'=>'',
					'tip'=>$L->g('Write a template name to filter the page in the theme and change the style of the page.')
				));

				echo Bootstrap::formInputTextBlock(array(
					'name'=>'externalCoverImage',
					'label'=>$L->g('External cover image'),
					'placeholder'=>"https://",
					'value'=>'',
					'tip'=>$L->g('Set a cover image from external URL, such as a CDN or some server dedicated for images.')
				));

				// Username
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'',
					'label'=>$L->g('Author'),
					'placeholder'=>'',
					'value'=>$login->username(),
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
		<div id="nav-seo" class="tab-pane fade" role="tabpanel" aria-labelledby="seo-tab">
			<?php
				// Friendly URL
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'slug',
					'tip'=>$L->g('URL associated with the content'),
					'label'=>$L->g('Friendly URL'),
					'placeholder'=>$L->g('Leave empty for autocomplete by Bludit.')
				));

				// Robots
				echo Bootstrap::formCheckbox(array(
					'name'=>'noindex',
					'label'=>'Robots',
					'labelForCheckbox'=>$L->g('apply-code-noindex-code-to-this-page'),
					'placeholder'=>'',
					'checked'=>false,
					'tip'=>$L->g('This tells search engines not to show this page in their search results.')
				));

				// Robots
				echo Bootstrap::formCheckbox(array(
					'name'=>'nofollow',
					'label'=>'',
					'labelForCheckbox'=>$L->g('apply-code-nofollow-code-to-this-page'),
					'placeholder'=>'',
					'checked'=>false,
					'tip'=>$L->g('This tells search engines not to follow links on this page.')
				));

				// Robots
				echo Bootstrap::formCheckbox(array(
					'name'=>'noarchive',
					'label'=>'',
					'labelForCheckbox'=>$L->g('apply-code-noarchive-code-to-this-page'),
					'placeholder'=>'',
					'checked'=>false,
					'tip'=>$L->g('This tells search engines not to save a cached copy of this page.')
				));
			?>
		</div>
	</div>
</div>

<!-- Title -->
<div id="jseditorTitle" class="form-group mt-1 mb-1">
	<input id="jstitle" name="title" type="text" class="form-control form-control-lg rounded-0" value="" placeholder="<?php $L->p('Enter title') ?>">
</div>

<!-- Editor -->
<textarea id="jseditor" class="editable h-100 mb-1"></textarea>

</form>

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
	$("#jsbuttonSwitch").on("click", function() {
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
		bluditAjax.saveAsDraft(uuid, title, content).then(function(data) {
			window.open("<?php echo DOMAIN_PAGES.'autosave-'.$uuid.'?preview='.md5('autosave-'.$uuid) ?>", "_blank");
		});
	});

	// Button Save
	$("#jsbuttonSave").on("click", function() {
		// If the switch is setted to "published", get the value from the selector
		if ($("#jsbuttonSwitch").data("switch")=="publish") {
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
