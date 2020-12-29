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
<div id="jseditorToolbar" class="mb-1">
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
			<?php if (!empty($site->customFields())): ?>
			<a class="nav-link" id="nav-custom-tab" data-toggle="tab" href="#nav-custom" role="tab" aria-controls="custom"><?php $L->p('Custom') ?></a>
			<?php endif ?>
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
				echo Bootstrap::formSelectBlock(array(
					'name'=>'parent',
					'label'=>$L->g('Parent'),
					'options'=>array(),
					'selected'=>false,
					'class'=>'',
					'tip'=>$L->g('Start typing a page title to see a list of suggestions.'),
				));

			?>

			<script>
			$(document).ready(function() {
				var parent = $("#jsparent").select2({
					placeholder: "",
					allowClear: true,
					theme: "bootstrap4",
					minimumInputLength: 2,
					ajax: {
						url: HTML_PATH_ADMIN_ROOT+"ajax/get-published",
						data: function (params) {
							var query = {
								checkIsParent: true,
								query: params.term
							}
							return query;
						},
						processResults: function (data) {
							return data;
						}
					},
					escapeMarkup: function(markup) {
						return markup;
					},
					templateResult: function(data) {
						var html = data.text;
						if (data.type=="static") {
							html += '<span class="badge badge-pill badge-light">'+data.type+'</span>';
						}
						return html;
					}
				});
			});
			</script>

			<?php
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


			});
			</script>
		</div>
		<?php if (!empty($site->customFields())): ?>
		<div id="nav-custom" class="tab-pane fade" role="tabpanel" aria-labelledby="custom-tab">
		<?php
			$customFields = $site->customFields();
			foreach ($customFields as $field=>$options) {
				if ( !isset($options['position']) ) {
					if ($options['type']=="string") {
						echo Bootstrap::formInputTextBlock(array(
							'name'=>'custom['.$field.']',
							'label'=>(isset($options['label'])?$options['label']:''),
							'value'=>(isset($options['default'])?$options['default']:''),
							'tip'=>(isset($options['tip'])?$options['tip']:''),
							'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:'')
						));
					} elseif ($options['type']=="bool") {
						echo Bootstrap::formCheckbox(array(
							'name'=>'custom['.$field.']',
							'label'=>(isset($options['label'])?$options['label']:''),
							'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:''),
							'checked'=>(isset($options['checked'])?true:false),
							'labelForCheckbox'=>(isset($options['tip'])?$options['tip']:'')
						));
					} elseif ($options['type']=="text") {
						echo Bootstrap::formTextareaBlock(array(
							'name'=>'custom['.$field.']',
							'value'=>(isset($options['default'])?$options['default']:''),
							'tip'=>(isset($options['tip'])?$options['tip']:''),
							'label'=>(isset($options['label'])?$options['label']:''),
							'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:''),
							'rows' => '4'
						));
					}
				}
			}
		?>
		</div>
		<?php endif ?>
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

<!-- Custom fields: TOP -->
<?php
	$customFields = $site->customFields();
	foreach ($customFields as $field=>$options) {
		if ( isset($options['position']) && ($options['position']=='top') ) {
			if ($options['type']=="string") {
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'custom['.$field.']',
					'label'=>(isset($options['label'])?$options['label']:''),
					'value'=>(isset($options['default'])?$options['default']:''),
					'tip'=>(isset($options['tip'])?$options['tip']:''),
					'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:''),
					'class'=>'mb-2',
					'labelClass'=>'mb-2 pb-2 border-bottom text-uppercase w-100'
				));
			} elseif ($options['type']=="bool") {
				echo Bootstrap::formCheckbox(array(
					'name'=>'custom['.$field.']',
					'label'=>(isset($options['label'])?$options['label']:''),
					'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:''),
					'checked'=>(isset($options['checked'])?true:false),
					'labelForCheckbox'=>(isset($options['tip'])?$options['tip']:''),
					'class'=>'mb-2',
					'labelClass'=>'mb-2 pb-2 border-bottom text-uppercase w-100'
				));
			} elseif ($options['type']=="text") {
				echo Bootstrap::formTextareaBlock(array(
					'name'=>'custom['.$field.']',
					'value'=>(isset($options['default'])?$options['default']:''),
					'tip'=>(isset($options['tip'])?$options['tip']:''),
					'label'=>(isset($options['label'])?$options['label']:''),
					'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:''),
					'rows' => '4'
				));
			}
		}
	}
?>


<!-- Title -->
<div id="jseditorTitle" class="form-group mb-1">
	<input id="jstitle" name="title" type="text" class="form-control form-control-lg rounded-0" value="" placeholder="<?php $L->p('Enter title') ?>">
</div>

<!-- Editor -->
<textarea id="jseditor" class="editable h-100 mb-1"></textarea>

<!-- Custom fields: BOTTOM -->
<?php
	$customFields = $site->customFields();
	foreach ($customFields as $field=>$options) {
		if ( isset($options['position']) && ($options['position']=='bottom') ) {
			if ($options['type']=="string") {
				echo Bootstrap::formInputTextBlock(array(
					'name'=>'custom['.$field.']',
					'label'=>(isset($options['label'])?$options['label']:''),
					'value'=>(isset($options['default'])?$options['default']:''),
					'tip'=>(isset($options['tip'])?$options['tip']:''),
					'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:''),
					'class'=>'mt-2',
					'labelClass'=>'mb-2 pb-2 border-bottom text-uppercase w-100'
				));
			} elseif ($options['type']=="bool") {
				echo Bootstrap::formCheckbox(array(
					'name'=>'custom['.$field.']',
					'label'=>(isset($options['label'])?$options['label']:''),
					'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:''),
					'checked'=>(isset($options['checked'])?true:false),
					'labelForCheckbox'=>(isset($options['tip'])?$options['tip']:''),
					'class'=>'mt-2',
					'labelClass'=>'mb-2 pb-2 border-bottom text-uppercase w-100'
				));
			} elseif ($options['type']=="text") {
				echo Bootstrap::formTextareaBlock(array(
					'name'=>'custom['.$field.']',
					'value'=>(isset($options['default'])?$options['default']:''),
					'tip'=>(isset($options['tip'])?$options['tip']:''),
					'label'=>(isset($options['label'])?$options['label']:''),
					'placeholder'=>(isset($options['placeholder'])?$options['placeholder']:''),
					'rows' => '4'
				));
			}
		}
	}
?>

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
	if (typeof editorInsertLinkedMedia != "function") {
		window.editorInsertLinkedMedia = function(filename, link){
			$("#jseditor").val($('#jseditor').val()+'<a href="'+link+'"><img src="'+filename+'" alt=""></a>');
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
			var preview = window.open("<?php echo DOMAIN_PAGES.'autosave-'.$uuid.'?preview='.md5('autosave-'.$uuid) ?>", "bludit-preview");
			preview.focus();
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
