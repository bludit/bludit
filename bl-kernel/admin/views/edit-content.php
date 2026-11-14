<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php

// FORM START
echo Bootstrap::formOpen(array(
	'id' => 'jsform',
	'class' => 'd-flex flex-column h-100'
));

// Token CSRF
echo Bootstrap::formInputHidden(array(
	'name' => 'tokenCSRF',
	'value' => $security->getTokenCSRF()
));

// UUID
// The UUID is generated in the controller
echo Bootstrap::formInputHidden(array(
	'name' => 'uuid',
	'value' => $page->uuid()
));

// Type = published, draft, sticky, static
echo Bootstrap::formInputHidden(array(
	'name' => 'type',
	'value' => $page->type()
));

// Cover image
echo Bootstrap::formInputHidden(array(
	'name' => 'coverImage',
	'value' => $page->coverImage(false)
));

// Content
echo Bootstrap::formInputHidden(array(
	'name' => 'content',
	'value' => ''
));

// Current page key
echo Bootstrap::formInputHidden(array(
	'name' => 'key',
	'value' => $page->key()
));
?>

<!-- TOOLBAR -->
<?php $switch = ($page->draft() ? 'draft' : 'publish'); ?>
<?php $saveLabels = array('draft' => $L->g('Save as draft'), 'publish' => ($page->draft() ? $L->g('Publish') : $L->g('Save'))); ?>
<div id="jseditorToolbar" class="mb-1">
	<div id="jseditorToolbarRight" class="btn-group btn-group-sm" role="group" aria-label="Toolbar right">
		<button type="button" class="btn btn-light" id="jsbuttonSwitch" data-switch="<?php echo $switch ?>"><span class="fa fa-square switch-icon-<?php echo $switch ?>"></span> <?php echo ($switch == 'draft' ? $L->g('Draft') : $L->g('Publish')) ?></button>
		<button type="button" class="btn btn-light" id="jsmediaManagerOpenModal" data-bs-toggle="modal" data-bs-target="#jsmediaManagerModal"><span class="fa fa-image"></span> <?php $L->p('Images') ?></button>
		<?php Theme::plugins('editorToolbar') ?>
		<button type="button" class="btn btn-light d-none d-md-inline-block" data-bs-toggle="modal" data-bs-target="#jsoptionsGeneralModal"><span class="fa fa-cog"></span> <?php $L->p('General') ?></button>
		<button type="button" class="btn btn-light d-none d-md-inline-block" data-bs-toggle="modal" data-bs-target="#jsoptionsAdvancedModal"><span class="fa fa-sliders"></span> <?php $L->p('Advanced') ?></button>
		<?php if (!empty($site->customFields())) : ?>
			<button type="button" class="btn btn-light d-none d-md-inline-block" data-bs-toggle="modal" data-bs-target="#jsoptionsCustomModal"><span class="fa fa-list"></span> <?php $L->p('Custom') ?></button>
		<?php endif ?>
		<button type="button" class="btn btn-light d-none d-md-inline-block" data-bs-toggle="modal" data-bs-target="#jsoptionsSeoModal"><span class="fa fa-search"></span> <?php $L->p('SEO') ?></button>
		<button type="button" class="btn btn-light d-none d-md-inline-block" data-bs-toggle="modal" data-bs-target="#jsdeletePageModal"><span class="fa fa-trash"></span> <?php $L->p('Delete') ?></button>
		<!-- On phones the options go inside a hamburger button -->
		<div class="btn-group btn-group-sm d-md-none" role="group">
			<button type="button" class="btn btn-light" id="jsoptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="<?php $L->p('Options') ?>"><span class="fa fa-bars"></span></button>
			<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="jsoptionsDropdown">
				<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#jsoptionsGeneralModal"><?php $L->p('General') ?></a></li>
				<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#jsoptionsAdvancedModal"><?php $L->p('Advanced') ?></a></li>
				<?php if (!empty($site->customFields())) : ?>
					<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#jsoptionsCustomModal"><?php $L->p('Custom') ?></a></li>
				<?php endif ?>
				<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#jsoptionsSeoModal"><?php $L->p('SEO') ?></a></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#jsdeletePageModal"><?php $L->p('Delete') ?></a></li>
			</ul>
		</div>
	</div>

	<div id="jseditorToolbarLeft">
		<button id="jsbuttonSave" type="button" class="btn btn-sm btn-primary"><span class="fa fa-save"></span> <span id="jsbuttonSaveLabel"><?php echo $saveLabels[$switch] ?></span></button>
		<button id="jsbuttonPreview" type="button" class="btn btn-sm btn-secondary"><span class="fa fa-eye"></span> <?php $L->p('Preview') ?></button>
	</div>

	<?php if ($page->scheduled()) : ?>
		<div class="alert alert-warning p-1 mt-1 mb-0"><?php $L->p('scheduled') ?>: <?php echo $page->date(SCHEDULED_DATE_FORMAT) ?></div>
	<?php endif; ?>
</div>

<!-- OPTIONS: each group has its own modal, they stay inside the form -->
<?php echo Bootstrap::modalOpen(array('id' => 'jsoptionsGeneralModal', 'title' => $L->g('General'), 'icon' => 'cog', 'done' => $L->g('Done'))) ?>
			<div class="row">
			<div class="col-md-7">
			<?php
			// Category
			echo Bootstrap::formSelectBlock(array(
				'name' => 'category',
				'label' => $L->g('Category'),
				'selected' => $page->categoryKey(),
				'class' => '',
				'emptyOption' => '- ' . $L->g('Uncategorized') . ' -',
				'options' => $categories->getKeyNameArray(),
				'tip' => $L->g('choose-a-category-to-organize-your-content')
			));

			// Tags: a picker that suggests the existing tags, the hidden input keeps the comma separated list
			$pageTags = $page->tags(true);
			$tagOptions = '';
			foreach (array_unique(array_merge(array_values($tags->getKeyNameArray()), array_values($pageTags))) as $tagName) {
				$tagOptions .= '<option value="' . $tagName . '"' . (in_array($tagName, $pageTags) ? ' selected' : '') . '>' . $tagName . '</option>';
			}
			?>
			<div class="form-group m-0">
				<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="jstagsPicker"><?php $L->p('Tags') ?></label>
				<input type="hidden" id="jstags" name="tags" value="<?php echo implode(',', $pageTags) ?>">
				<select id="jstagsPicker" class="form-select" multiple><?php echo $tagOptions ?></select>
				<small class="form-text text-muted"><?php $L->p('Write the tags separated by commas') ?></small>
			</div>
			<script>
				$(document).ready(function() {
					$("#jstagsPicker").select2({
						tags: true,
						tokenSeparators: [","],
						theme: "bootstrap-5",
						width: "100%",
						placeholder: "",
						dropdownParent: $("#jsoptionsGeneralModal")
					}).on("change", function() {
						$("#jstags").val(($(this).val() || []).join(","));
					});
				});
			</script>
			<?php

			// Description
			echo Bootstrap::formTextareaBlock(array(
				'name' => 'description',
				'label' => $L->g('Description'),
				'selected' => '',
				'class' => '',
				'value' => $page->description(),
				'rows' => 5,
				'placeholder' => '',
				'tip' => $L->g('this-field-can-help-describe-the-content')
			));

			// Date
			echo Bootstrap::formInputTextBlock(array(
				'name' => 'date',
				'label' => $L->g('Date'),
				'placeholder' => '',
				'value' => $page->dateRaw(),
				'tip' => $L->g('date-format-format')
			));
			?>

			</div>
			<div class="col-md-5">
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
			<!-- Click to select an image, click again to remove it -->
			<div id="jscoverImageBox" class="cover-image">
				<img id="jscoverImagePreview" class="mx-auto d-block w-100" alt="Cover image preview" src="<?php echo ($page->coverImage() ? $page->coverImage() : HTML_PATH_CORE_IMG . 'default.svg') ?>" />
				<span class="cover-image-remove"><span class="fa fa-trash"></span></span>
			</div>
			<?php
			// External cover image
			echo Bootstrap::formInputTextBlock(array(
				'name' => 'externalCoverImage',
				'label' => $L->g('External cover image'),
				'placeholder' => "https://",
				'value' => $externalCoverImage,
				'tip' => $L->g('Set a cover image from external URL, such as a CDN or some server dedicated for images.')
			));
			?>
			</div>
			</div>
			<script>
				$(document).ready(function() {
					// The media manager is another modal: swap them and come back when it closes
					var generalModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('jsoptionsGeneralModal'));
					var backToGeneral = false;
					var coverImageBox = $("#jscoverImageBox");
					var coverImageDefault = HTML_PATH_CORE_IMG + 'default.svg';

					function coverImageIsSet() {
						return $("#jscoverImagePreview").attr('src') != coverImageDefault;
					}

					// The tooltip tells what the click is going to do
					coverImageBox.on("mouseenter", function() {
						$(this).attr('title', coverImageIsSet() ? "<?php $L->p('Remove cover image') ?>" : "<?php $L->p('Select cover image') ?>");
					});

					coverImageBox.on("click", function() {
						if (coverImageIsSet()) {
							$("#jscoverImage").val('');
							$("#jsexternalCoverImage").val('');
							$("#jscoverImagePreview").attr('src', coverImageDefault);
							return;
						}
						backToGeneral = true;
						generalModal.hide();
						openMediaManager();
					});
					document.getElementById('jsmediaManagerModal').addEventListener('hidden.bs.modal', function() {
						if (backToGeneral) {
							backToGeneral = false;
							generalModal.show();
						}
					});
				});
			</script>
<?php echo Bootstrap::modalClose() ?>

<?php echo Bootstrap::modalOpen(array('id' => 'jsoptionsAdvancedModal', 'title' => $L->g('Advanced'), 'icon' => 'sliders', 'done' => $L->g('Done'))) ?>
			<?php
			// Type
			echo Bootstrap::formSelectBlock(array(
				'name' => 'typeSelector',
				'label' => $L->g('Type'),
				'selected' => $page->type(),
				'options' => array(
					'published' => '- ' . $L->g('Default') . ' -',
					'sticky' => $L->g('Sticky'),
					'static' => $L->g('Static')
				),
				'tip' => ''
			));

			// Position
			echo Bootstrap::formInputTextBlock(array(
				'name' => 'position',
				'label' => $L->g('Position'),
				'tip' => $L->g('Field used when ordering content by position'),
				'value' => $page->position()
			));

			// Parent
			try {
				$options = array();
				$parentKey = $page->parent();
				if (!empty($parentKey)) {
					$parent = new Page($parentKey);
					$options = array($parentKey => $parent->title());
				}
			} catch (Exception $e) {
				// continue
			}
			echo Bootstrap::formSelectBlock(array(
				'name' => 'parent',
				'label' => $L->g('Parent'),
				'options' => $options,
				'selected' => false,
				'class' => '',
				'tip' => $L->g('Start typing a page title to see a list of suggestions.'),
			));

			?>

			<script>
				$(document).ready(function() {
					var parent = $("#jsparent").select2({
						placeholder: "",
						allowClear: true,
						dropdownParent: $("#jsoptionsAdvancedModal"),
						theme: "bootstrap-5",
						minimumInputLength: 2,
						ajax: {
							url: HTML_PATH_ADMIN_ROOT + "ajax/get-published",
							data: function(params) {
								var query = {
									checkIsParent: true,
									query: params.term
								}
								return query;
							},
							processResults: function(data) {
								return data;
							}
						},
						escapeMarkup: function(markup) {
							return markup;
						},
						templateResult: function(data) {
							var html = data.text
							if (data.type == "static") {
								html += '<span class="badge rounded-pill text-bg-light">' + data.type + '</span>';
							}
							return html;
						}
					});
				});
			</script>

			<?php

			// Template
			echo Bootstrap::formInputTextBlock(array(
				'name' => 'template',
				'label' => $L->g('Template'),
				'placeholder' => '',
				'value' => $page->template(),
				'tip' => $L->g('Write a template name to filter the page in the theme and change the style of the page.')
			));

			// Username
			echo Bootstrap::formInputTextBlock(array(
				'name' => '',
				'label' => $L->g('Author'),
				'placeholder' => '',
				'value' => $page->username(),
				'tip' => '',
				'disabled' => true
			));
			?>
			<script>
				$(document).ready(function() {
					// Changes in External cover image input
					$("#jsexternalCoverImage").change(function() {
						$("#jscoverImage").val($(this).val());
						$("#jscoverImagePreview").attr('src', $(this).val() ? $(this).val() : HTML_PATH_CORE_IMG + 'default.svg');
					});

					// Datepicker
					$("#jsdate").datetimepicker({
						format: DB_DATE_FORMAT
					});
				});
			</script>
<?php echo Bootstrap::modalClose() ?>

<?php if (!empty($site->customFields())) : ?>
<?php echo Bootstrap::modalOpen(array('id' => 'jsoptionsCustomModal', 'title' => $L->g('Custom'), 'icon' => 'list', 'done' => $L->g('Done'))) ?>
				<?php
				$customFields = $site->customFields();
				foreach ($customFields as $field => $options) {
					if (!isset($options['position'])) {
						if ($options['type'] == "string") {
							echo Bootstrap::formInputTextBlock(array(
								'name' => 'custom[' . $field . ']',
								'value' => (isset($options['default']) ? $options['default'] : ''),
								'tip' => (isset($options['tip']) ? $options['tip'] : ''),
								'label' => (isset($options['label']) ? $options['label'] : ''),
								'placeholder' => (isset($options['placeholder']) ? $options['placeholder'] : ''),
								'value' => $page->custom($field)
							));
						} elseif ($options['type'] == "bool") {
							echo Bootstrap::formCheckbox(array(
								'name' => 'custom[' . $field . ']',
								'label' => (isset($options['label']) ? $options['label'] : ''),
								'placeholder' => (isset($options['placeholder']) ? $options['placeholder'] : ''),
								'checked' => $page->custom($field),
								'labelForCheckbox' => (isset($options['tip']) ? $options['tip'] : '')
							));
						}
					}
				}
				?>
<?php echo Bootstrap::modalClose() ?>
<?php endif ?>

<?php echo Bootstrap::modalOpen(array('id' => 'jsoptionsSeoModal', 'title' => $L->g('SEO'), 'icon' => 'search', 'done' => $L->g('Done'))) ?>
			<?php
			// Friendly URL
			echo Bootstrap::formInputTextBlock(array(
				'name' => 'slug',
				'tip' => $L->g('URL associated with the content'),
				'label' => $L->g('Friendly URL'),
				'placeholder' => $L->g('Leave empty for autocomplete by Bludit.'),
				'value' => $page->slug()
			));

			// Robots
			echo Bootstrap::formCheckbox(array(
				'name' => 'noindex',
				'label' => 'Robots',
				'labelForCheckbox' => $L->g('apply-code-noindex-code-to-this-page'),
				'placeholder' => '',
				'checked' => $page->noindex(),
				'tip' => $L->g('This tells search engines not to show this page in their search results.')
			));

			// Robots
			echo Bootstrap::formCheckbox(array(
				'name' => 'nofollow',
				'label' => '',
				'labelForCheckbox' => $L->g('apply-code-nofollow-code-to-this-page'),
				'placeholder' => '',
				'checked' => $page->nofollow(),
				'tip' => $L->g('This tells search engines not to follow links on this page.')
			));

			// Robots
			echo Bootstrap::formCheckbox(array(
				'name' => 'noarchive',
				'label' => '',
				'labelForCheckbox' => $L->g('apply-code-noarchive-code-to-this-page'),
				'placeholder' => '',
				'checked' => $page->noarchive(),
				'tip' => $L->g('This tells search engines not to save a cached copy of this page.')
			));
			?>
<?php echo Bootstrap::modalClose() ?>

<!-- Custom fields: TOP -->
<?php
$customFields = $site->customFields();
foreach ($customFields as $field => $options) {
	if (isset($options['position']) && ($options['position'] == 'top')) {
		if ($options['type'] == "string") {
			echo Bootstrap::formInputTextBlock(array(
				'name' => 'custom[' . $field . ']',
				'label' => (isset($options['label']) ? $options['label'] : ''),
				'value' => $page->custom($field),
				'tip' => (isset($options['tip']) ? $options['tip'] : ''),
				'placeholder' => (isset($options['placeholder']) ? $options['placeholder'] : ''),
				'class' => 'mb-2',
				'labelClass' => 'mb-2 pb-2 border-bottom text-uppercase w-100'

			));
		} elseif ($options['type'] == "bool") {
			echo Bootstrap::formCheckbox(array(
				'name' => 'custom[' . $field . ']',
				'label' => (isset($options['label']) ? $options['label'] : ''),
				'placeholder' => (isset($options['placeholder']) ? $options['placeholder'] : ''),
				'checked' => $page->custom($field),
				'labelForCheckbox' => (isset($options['tip']) ? $options['tip'] : ''),
				'class' => 'mb-2',
				'labelClass' => 'mb-2 pb-2 border-bottom text-uppercase w-100'
			));
		}
	}
}
?>

<!-- Title -->
<div class="form-group mb-1">
	<input id="jstitle" name="title" type="text" dir="auto" class="form-control form-control-lg rounded-0" value="<?php echo $page->title() ?>" placeholder="<?php $L->p('Enter title') ?>">
</div>

<!-- URL of the content, click to change it -->
<div id="jsurlPreview" class="mb-1 d-none">
	<a href="#" class="small text-muted text-decoration-none" data-bs-toggle="modal" data-bs-target="#jsoptionsSeoModal"><span class="fa fa-link"></span> <span id="jsurlPreviewText"></span></a>
</div>

<!-- Editor -->
<textarea id="jseditor" class="editable h-100" style=""><?php echo $page->contentRaw(true) ?></textarea>

<!-- Custom fields: BOTTOM -->
<?php
$customFields = $site->customFields();
foreach ($customFields as $field => $options) {
	if (isset($options['position']) && ($options['position'] == 'bottom')) {
		if ($options['type'] == "string") {
			echo Bootstrap::formInputTextBlock(array(
				'name' => 'custom[' . $field . ']',
				'label' => (isset($options['label']) ? $options['label'] : ''),
				'value' => $page->custom($field),
				'tip' => (isset($options['tip']) ? $options['tip'] : ''),
				'placeholder' => (isset($options['placeholder']) ? $options['placeholder'] : ''),
				'class' => 'mt-2',
				'labelClass' => 'mb-2 pb-2 border-bottom text-uppercase w-100'

			));
		} elseif ($options['type'] == "bool") {
			echo Bootstrap::formCheckbox(array(
				'name' => 'custom[' . $field . ']',
				'label' => (isset($options['label']) ? $options['label'] : ''),
				'placeholder' => (isset($options['placeholder']) ? $options['placeholder'] : ''),
				'checked' => $page->custom($field),
				'labelForCheckbox' => (isset($options['tip']) ? $options['tip'] : ''),
				'class' => 'mt-2',
				'labelClass' => 'mb-2 pb-2 border-bottom text-uppercase w-100'
			));
		}
	}
}
?>

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
				<button type="button" class="btn btn-link" data-bs-dismiss="modal"><?php $L->p('Cancel') ?></button>
				<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="jsbuttonDeleteAccept"><?php $L->p('Delete') ?></button>
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
<?php include(PATH_ADMIN_THEMES . 'booty/html/media.php'); ?>

<script>
	$(document).ready(function() {

		// Define function if they don't exist
		// This helps if the user doesn't activate any plugin as editor
		if (typeof editorGetContent != "function") {
			window.editorGetContent = function() {
				return $("#jseditor").val();
			};
		}
		if (typeof editorInsertMedia != "function") {
			window.editorInsertMedia = function(filename) {
				$("#jseditor").val($('#jseditor').val() + '<img src="' + filename + '" alt="">');
			};
		}
		if (typeof editorInsertLinkedMedia != "function") {
			window.editorInsertLinkedMedia = function(filename, link) {
				$("#jseditor").val($('#jseditor').val() + '<a href="' + link + '"><img src="' + filename + '" alt=""></a>');
			};
		}

		// URL under the title, built from the parent and the friendly URL
		window.updateUrlPreview = function() {
			var slug = $("#jsslug").val();
			var parent = $("#jsparent").val();
			$("#jsurlPreviewText").text(<?php echo json_encode(DOMAIN_PAGES) ?> + (parent ? parent + "/" : "") + slug);
			$("#jsurlPreview").toggleClass("d-none", !slug);
		};
		$("#jsslug").on("input", updateUrlPreview);
		$("#jsparent").on("change", updateUrlPreview);
		updateUrlPreview();

		// Button switch, the Save button says what it is going to do
		var switchLabels = <?php echo json_encode(array('draft' => $L->g('Draft'), 'publish' => $L->g('Publish'))) ?>;
		var saveLabels = <?php echo json_encode($saveLabels) ?>;
		$("#jsbuttonSwitch").on("click", function() {
			var value = ($(this).data("switch") == "publish") ? "draft" : "publish";
			$(this).data("switch", value);
			$(this).html('<span class="fa fa-square switch-icon-' + value + '"></span> ' + switchLabels[value]);
			$("#jsbuttonSaveLabel").text(saveLabels[value]);
		});

		// Button preview
		$("#jsbuttonPreview").on("click", function() {
			var uuid = $("#jsuuid").val();
			var title = $("#jstitle").val();
			var content = editorGetContent();
			var ajax = new bluditAjax();
			bluditAjax.saveAsDraft(uuid, title, content).then(function(data) {
				var preview = window.open("<?php echo DOMAIN_PAGES . 'autosave-' . $page->uuid() . '?preview=' . hash_hmac('sha256', 'autosave-' . $page->uuid(), $site->previewKey()) ?>", "bludit-preview");
				preview.focus();
			});
		});

		// Button Save
		$("#jsbuttonSave").on("click", function() {
			// If the switch is setted to "published", get the value from the selector
			if ($("#jsbuttonSwitch").data("switch") == "publish") {
				var value = $("#jstypeSelector option:selected").val();
				$("#jstype").val(value);
			} else {
				$("#jstype").val("draft");
			}

			// Get the content
			$("#jscontent").val(editorGetContent());

			// Submit the form
			$("#jsform").submit();
		});

		// Button Save as draft
		$("#jsbuttonDraft").on("click", function() {
			// Set the type as draft
			$("#jstype").val("draft");

			// Get the content
			$("#jscontent").val(editorGetContent());

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
			if (content.length < 100) {
				return false;
			}
			// Autosave only when the user change the content
			if (currentContent != content) {
				currentContent = content;
				bluditAjax.saveAsDraft(uuid, title, content).then(function(data) {
					if (data.status == 0) {
						showAlert("<?php $L->p('Autosave') ?>");
					}
				});
			}
		}, 1000 * 60 * AUTOSAVE_INTERVAL);

	});
</script>
