<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================
	var _pageKey = <?php echo $pageKey ? '"' . $pageKey . '"' : 'null' ?>;

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// Default function for the editor
	// These functions work if the user does not activate any plugin
	if (typeof editorGetContent != 'function') {
		window.editorGetContent = function() {
			return $('#editor').val();
		};
	}
	if (typeof editorInsertContent != 'function') {
		window.editorInsertContent = function(content, type = '') {
			if (type == 'image') {
				var html = '<img src="' + content + '" alt="" />';
			} else {
				var html = content;
			}
			$('#editor').val($('#editor').val() + html);
		};
	}

	// Create the a page
	// This function set the global variable "_pageKey"
	function createPage() {
		logs('Creating page.');
		api.createPage().then(function(response) {
			if (response.status == 0) {
				logs('Page created. Key: ' + response.data.key);
				// Set the global variable with the page key
				_pageKey = response.data.key;
				// Set Friendly URL
				$('#friendlyURL').val(response.data.key);
				// Get current files
				fmGetFiles();
			} else {
				logs("An error occurred while trying to create the page.");
				showAlertError(response.message);
			}
		});
		return true;
	}

	// Set the page in the editor
	function setPage() {
		logs('Setting up the page');
		// Get current files
		fmGetFiles();
		return true;
	}

	// Save the current page
	// This function set the global variable "_pageKey"
	function savePage(args) {
		logs('Saving page.');

		if (_pageKey == null) {
			logs('Error, page not created.');
			showAlertError("Error, page not created.");
			return false;
		}

		args['pageKey'] = _pageKey;
		api.savePage(args).then(function(response) {
			if (response.status == 0) {
				logs('Page saved. Old key: ' + _pageKey + ' / New key: ' + response.data.key);
				// Set the global variable with the page key
				// The page key can change after save the page so you need to set again the variable
				_pageKey = response.data.key;
				// Set friendly URL with the key
				$('#friendlyURL').val(response.data.key);
			} else {
				logs('An error occurred while trying to save the current page.');
				showAlertError(response.message);
			}
		});
		return true;
	}

	/*
		Open the modal and store the current value
		The current value is store to recover it if the user click on the button "Cancel"
	 */
	function openModal(fieldName) {
		var value = $('#' + fieldName).val();
		localStorage.setItem(fieldName, value);
		$('#modal-' + fieldName).modal('show');
	}

	/*
		Close the modal
		The function also recover the old value
	*/
	function closeModal(fieldName, revertValue=false) {
		if (revertValue) {
			var value = localStorage.getItem(fieldName);
			$('#' + fieldName).val(value);
		}
		$('#modal-' + fieldName).modal('hide');
	}

	/*
		Disable the "Save" button
	*/
	function disableBtnSave() {
		$('#btnSave').addClass('btn-primary-disabled').attr('data-current', 'saved').html('<i class="bi bi-check-square"></i><?php $L->p('Saved') ?>');
	}

	/*
		Enable the "Save" button
	*/
	function enableBtnSave() {
		$('#btnSave').removeClass('btn-primary-disabled').attr('data-current', 'unsaved').html('<i class="bi bi-save"></i><?php $L->p('Save') ?>');
	}

	// This function is to catch all key press and provides shortcuts
	// The editor plugin need to call this function for the event "keydown"
	function keypress(event) {
		logs(event);

		// Shortcuts
		// ------------------------------------------------------------------------
		// Ctrl+S or Command+S
		if ((event.ctrlKey || event.metaKey) && event.which == 83) {
			event.preventDefault();
			var args = {
				title: $('#title').val(),
				content: editorGetContent(),
				coverImage: $('#coverImage').val(),
				category: $('#category option:selected').val(),
				tags: $("#tags option:selected").map(function() {
					return this.value
				}).get().join(",")
			}
			savePage(args);
			disableBtnSave();
			return false;
		}

		// Ctrl+ or Command+ or Alt+ or Shift+ or Option+
		if (event.ctrlKey || event.metaKey || event.altKey || event.shiftKey) {
			return true;
		}

		enableBtnSave();
		return true;
	}

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {

		// Main interface events
		// ------------------------------------------------------------------------

		// Catch all keypress for shortcuts or other actions
		$(this).keydown(function(event) {
			keypress(event);
		});

		// Warn the user to save the changes before leave
		$(window).bind('beforeunload', function(e) {
			if ($('#btnSave').attr('data-current') == 'unsaved') {
				(e || window.event).returnValue = '';
				return '';
			}
			return undefined; // Return undefined to continue the unload
		});

		$('#btnSave').on('click', function() {
			var args = {
				title: $('#title').val(),
				content: editorGetContent(),
				coverImage: $('#coverImage').val(),
				category: $('#category option:selected').val(),
				tags: $("#tags option:selected").map(function() {
					return this.value
				}).get().join(",")
			}
			savePage(args);
			disableBtnSave();
		});

		$('#btnCurrenType').on('click', function() {
			openModal('type');
		});

		$('#category').on("change", function() {
			enableBtnSave();
		});

		// Modal description events
		// ------------------------------------------------------------------------
		$('#btnSaveDescription').on('click', function() {
			var args = {
				description: $('#description').val()
			};
			savePage(args);
			disableBtnSave();
			closeModal('description');
		});

		$('#btnCancelDescription').on('click', function() {
			closeModal('description', true);
		});

		// Modal date events
		// ------------------------------------------------------------------------
		$('#btnSaveDate').on('click', function() {
			var args = {
				date: $('#date').val()
			};
			savePage(args);
			disableBtnSave();
			closeModal('date');
		});

		$('#btnCancelDate').on('click', function() {
			closeModal('date', true);
		});

		// Modal friendly-url events
		// ------------------------------------------------------------------------
		$('#btnSaveFriendlyURL').on('click', function() {
			var args = {
				slug: $('#friendlyURL').val()
			};
			savePage(args);
			disableBtnSave();
			closeModal('friendlyURL');
		});

		$('#btnCancelFriendlyURL').on('click', function() {
			closeModal('friendlyURL', true);
		});

		$('#btnGenURLFromTitle').on('click', function() {
			var args = {
				text: $('#title').val(),
				parentKey: $('#parent').val(),
				pageKey: _pageKey
			}
			api.friendlyURL(args).then(function(response) {
				if (response.status == 0) {
					logs('Friendly URL created: ' + response.data.slug);
					$('#friendlyURL').val(response.data.slug);
				} else {
					logs('An error occurred while trying to generate a friendly URL for the page.');
					showAlertError(response.message);
				}
			});
		});

		// Modal type events
		// ------------------------------------------------------------------------
		$('#btnSaveType').on('click', function() {
			var args = {
				type: $('input[name="type"]:checked').val()
			};

			savePage(args);
			disableBtnSave();
			closeModal('type');

			if (args['type'] == 'draft') {
				$('#btnCurrenType').html('<i class="bi bi-circle"></i><?php $L->p('Draft') ?>');
			} else if (args['type'] == 'published') {
				$('#btnCurrenType').html('<i class="bi bi-check2-circle"></i><?php $L->p('Published') ?>');
			} else if (args['type'] == 'unlisted') {
				$('#btnCurrenType').html('<i class="bi bi-check2-circle"></i><?php $L->p('Unlisted') ?>');
			} else if (args['type'] == 'sticky') {
				$('#btnCurrenType').html('<i class="bi bi-check2-circle"></i><?php $L->p('Sticky') ?>');
			} else if (args['type'] == 'static') {
				$('#btnCurrenType').html('<i class="bi bi-check2-circle"></i><?php $L->p('Static') ?>');
			}
		});

		$('#btnCancelType').on('click', function() {
			closeModal('type', true);
		});

		// Modal SEO events
		// ------------------------------------------------------------------------
		$('#btnSaveSeo').on('click', function() {
			var args = {
				noindex: $('input[name="noindex"]').is(':checked'),
				nofollow: $('input[name="nofollow"]').is(':checked'),
				noarchive: $('input[name="noarchive"]').is(':checked')
			};
			savePage(args);
			disableBtnSave();
			closeModal('seo');
		});

		$('#btnCancelSeo').on('click', function() {
			closeModal('seo', true);
		});

		// Modal parent events
		// ------------------------------------------------------------------------
		$('#btnSaveParent').on('click', function() {
			var args = {
				parent: $('#parent').val()
			};
			savePage(args);
			disableBtnSave();
			closeModal('parent');
		});

		$('#btnCancelParent').on('click', function() {
			closeModal('parent', true);
		});

	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// How do you hang your toilet paper ? over or under ?

		// Create the page or set the page
		if (_pageKey == null) {
			createPage();
		} else {
			setPage();
		}

		// Autosave
		setInterval(function() {
			var content = editorGetContent();
			// Autosave when content has at least 100 characters
			if (content.length < 100) {
				return false;
			}
			savePage();
			disableBtnSave();
		}, 1000 * 60 * AUTOSAVE_INTERVAL);

		$("#parent").select2({
			placeholder: '',
			allowClear: true,
			theme: 'bootstrap-5',
			minimumInputLength: 2,
			dropdownParent: $('#modal-parent'),
			ajax: {
				url: HTML_PATH_ADMIN_ROOT + 'ajax/get-published',
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
				var html = data.text;
				if (data.type == 'static') {
					html += '<span class="badge badge-pill badge-light">' + data.type + '</span>';
				}
				return html;
			}
		});

	});
</script>

<!-- File manager -->
<?php include(PATH_ADMIN_VIEWS . 'editor' . DS . 'file-manager.php') ?>
<!-- End File manager -->

<!-- Modal Description -->
<div class="modal" id="modal-description" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label for="parent" class="fw-bold mb-2">Page description</label>
					<textarea id="description" name="description" class="form-control" rows="3"><?php echo ($pageKey ? $page->description() : '') ?></textarea>
					<div class="form-text"><?php echo $L->get('this-field-can-help-describe-the-content') ?></div>
				</div>
			</div>
			<div class="modal-footer ps-2 pe-2 pt-1 pb-1">
				<button id="btnCancelDescription" type="button" class="btn btn-sm btn-secondary"><i class="bi bi-x"></i>Cancel</button>
				<button id="btnSaveDescription" type="button" class="btn btn-sm btn-primary"><i class="bi bi-check"></i>Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal Description -->

<!-- Modal Date -->
<div class="modal" id="modal-date" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label for="date" class="fw-bold mb-2">Publish date</label>
					<input id="date" name="date" type="text" class="form-control" value="<?php echo ($pageKey ? $page->dateRaw() : Date::current(DB_DATE_FORMAT)) ?>">
					<div class="form-text"><?php echo $L->g('date-format-format') ?></div>
				</div>
			</div>
			<div class="modal-footer ps-2 pe-2 pt-1 pb-1">
				<button id="btnCancelDate" type="button" class="btn btn-sm btn-secondary"><i class="bi bi-x"></i>Cancel</button>
				<button id="btnSaveDate" type="button" class="btn btn-sm btn-primary"><i class="bi bi-check"></i>Save</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$("#date").datetimepicker({
			format: DB_DATE_FORMAT
		});
	});
</script>
<!-- End Modal Date -->

<!-- Modal friendly URL -->
<div class="modal" id="modal-friendlyURL" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<div class="d-flex mb-2">
						<label for="friendlyURL" class="p-0 m-0 me-auto fw-bold">Page URL</label>
						<button id="btnGenURLFromTitle" type="button" class="btn p-0 m-0 text-primary"><i class="bi bi-hammer"></i>Generate from page title</button>
					</div>
					<input id="friendlyURL" name="friendlyURL" type="text" class="form-control" value="<?php echo ($pageKey ? $page->slug() : '') ?>">
					<div class="form-text">https://www.varlogdiego.com/my-page-about-k8s</div>
				</div>
			</div>
			<div class="modal-footer ps-2 pe-2 pt-1 pb-1">
				<button id="btnCancelFriendlyURL" type="button" class="btn btn-sm btn-secondary"><i class="bi bi-x"></i>Cancel</button>
				<button id="btnSaveFriendlyURL" type="button" class="btn btn-sm btn-primary"><i class="bi bi-check"></i>Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal friendly URL -->

<!-- Modal Type -->
<div class="modal" id="modal-type" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label class="fw-bold mb-2">Page type</label>
				</div>
				<div class="form-check mb-2">
					<input id="statusDraft" name="type" class="form-check-input" type="radio" value="draft" <?php echo ((($pageKey && $page->draft()) || !$pageKey) ? 'checked' : '') ?>>
					<label class="form-check-label" for="statusDraft">Draft</label>
					<div class="form-text">Page as draft, is not visible for visitors.</div>
				</div>
				<div class="form-check mb-2">
					<input id="statusPublish" name="type" class="form-check-input" type="radio" value="published" <?php echo (($pageKey && $page->published()) ? 'checked' : '') ?>>
					<label class="form-check-label" for="statusPublish">Publish</label>
					<div class="form-text">Publish the page, everyone can see it.</div>
				</div>
				<hr>
				<div class="form-check mb-2">
					<input id="statusSticky" name="type" class="form-check-input" type="radio" value="sticky" <?php echo (($pageKey && $page->sticky()) ? 'checked' : '') ?>>
					<label class="form-check-label" for="statusSticky">Publish as sticky</label>
					<div class="form-text">The page can be seen by everyone in the top of the main page.</div>
				</div>
				<div class="form-check mb-2">
					<input id="statusStatic" name="type" class="form-check-input" type="radio" value="static" <?php echo (($pageKey && $page->isStatic()) ? 'checked' : '') ?>>
					<label class="form-check-label" for="statusStatic">Publish as static</label>
					<div class="form-text">The page can be seen by everyone as static page.</div>
				</div>
				<div class="form-check mb-2">
					<input id="statusUnlisted" name="type" class="form-check-input" type="radio" value="unlisted" <?php echo (($pageKey && $page->unlisted()) ? 'checked' : '') ?>>
					<label class="form-check-label" for="statusUnlisted">Publish as unlisted</label>
					<div class="form-text">The page can be seen and shared by anyone with the link.</div>
				</div>
			</div>
			<div class="modal-footer ps-2 pe-2 pt-1 pb-1">
				<button id="btnCancelType" type="button" class="btn btn-sm btn-secondary"><i class="bi bi-x"></i>Cancel</button>
				<button id="btnSaveType" type="button" class="btn btn-sm btn-primary"><i class="bi bi-check"></i>Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal Type -->

<!-- Modal SEO -->
<div class="modal" id="modal-seo" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label class="fw-bold mb-2">SEO features</label>
				</div>
				<div class="form-check mb-2">
					<input id="noindex" name="noindex" class="form-check-input" type="checkbox" value="noindex" <?php echo (($pageKey && $page->noindex()) ? 'checked' : '') ?>>
					<label class="form-check-label" for="noindex"><?php echo $L->g('apply-code-noindex-code-to-this-page') ?></label>
					<div class="form-text"><?php echo $L->g('This tells search engines not to show this page in their search results.') ?></div>
				</div>
				<div class="form-check mb-2">
					<input id="nofollow" name="nofollow" class="form-check-input" type="checkbox" value="nofollow" <?php echo (($pageKey && $page->nofollow()) ? 'checked' : '') ?>>
					<label class="form-check-label" for="nofollow"><?php echo $L->g('apply-code-nofollow-code-to-this-page') ?></label>
					<div class="form-text"><?php echo $L->g('This tells search engines not to follow links on this page.') ?></div>
				</div>
				<div class="form-check mb-2">
					<input id="noarchive" name="noarchive" class="form-check-input" type="checkbox" value="noarchive" <?php echo (($pageKey && $page->noarchive()) ? 'checked' : '') ?>>
					<label class="form-check-label" for="noarchive"><?php echo $L->g('apply-code-noarchive-code-to-this-page') ?></label>
					<div class="form-text"><?php echo $L->g('This tells search engines not to save a cached copy of this page.') ?></div>
				</div>
			</div>
			<div class="modal-footer ps-2 pe-2 pt-1 pb-1">
				<button id="btnCancelSeo" type="button" class="btn btn-sm btn-secondary"><i class="bi bi-x"></i>Cancel</button>
				<button id="btnSaveSeo" type="button" class="btn btn-sm btn-primary"><i class="bi bi-check"></i>Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal SEO -->

<!-- Modal Parent -->
<div class="modal" id="modal-parent" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
			<div class="col-sm-10">
				<select id="parent" name="parent" class="form-select" data-current-value="" data-save="true"></select>
			</div>
			</div>
			<div class="modal-footer ps-2 pe-2 pt-1 pb-1">
				<button id="btnCancelParent" type="button" class="btn btn-sm btn-secondary"><i class="bi bi-x"></i>Cancel</button>
				<button id="btnSaveParent" type="button" class="btn btn-sm btn-primary"><i class="bi bi-check"></i>Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal Parent -->

<div class="container-fluid h-100">
	<div class="row h-100">
		<div class="col-sm-9 d-flex flex-column" style="height: 85%">

			<!-- Toolbar > Save, Preview, Type and Options -->
			<div id="editorToolbar" class="d-flex align-items-center mb-2">
				<div id="editorToolbarLeft">
					<button id="btnSave" type="button" data-current="saved" class="btn btn-sm btn-primary btn-primary-disabled"><i class="bi bi-save"></i><?php $L->p('Saved') ?></button>
					<button id="btnPreview" type="button" class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-up-right"></i><?php $L->p('Preview') ?></button>
				</div>
				<div id="editorToolbarRight" class="ms-auto">
					<span id="btnCurrenType" class="ms-1 text-uppercase">
						<?php
						if ($pageKey) {
							if ($page->draft()) {
								echo '<i class="bi bi-circle"></i><span>' . $L->g('Draft') . '</span>';
							} elseif ($page->published()) {
								echo '<i class="bi bi-check2-circle"></i><span>' . $L->g('Published') . '</span>';
							} elseif ($page->sticky()) {
								echo '<i class="bi bi-check2-circle"></i><span>' . $L->g('Sticky') . '</span>';
							} elseif ($page->isStatic()) {
								echo '<i class="bi bi-check2-circle"></i><span>' . $L->g('Static') . '</span>';
							} elseif ($page->unlisted()) {
								echo '<i class="bi bi-check2-circle"></i><span>' . $L->g('Unlisted') . '</span>';
							}
						} else {
							echo '<i class="bi bi-circle"></i><span>' . $L->g('Draft') . '</span>';
						}
						?>
					</span>
				</div>
			</div>
			<!-- End Toolbar > Save, Preview, Type and Options -->

			<!-- Title -->
			<div class="mb-2">
				<input id="title" name="title" type="text" class="form-control form-control-lg" value="<?php echo ($pageKey ? $page->title() : '') ?>" placeholder="<?php $L->p('Enter title') ?>">
			</div>
			<!-- End Title -->

			<!-- Editor -->
			<textarea class="form-control flex-grow-1" placeholder="" id="editor"><?php echo ($pageKey ? $page->contentRaw() : '') ?></textarea>
			<!-- End Editor -->

		</div> <!-- End <div class="col-sm-9 h-100"> -->

		<div class="col-sm-3 h-100 mt-2">

			<!-- Cover Image -->
			<h6 class="text-uppercase"><?php $L->p('Cover Image') ?></h6>
			<div>
				<input id="coverImage" name="coverImage" data-save="true" type="hidden" value="<?php echo (($pageKey && $page->coverImage()) ? $page->coverImage(false) : '') ?>">
				<img id="coverImagePreview" class="mx-auto d-block w-100" alt="Cover image preview" src="<?php echo (($pageKey && $page->coverImage()) ? $page->coverImage() : HTML_PATH_CORE_IMG . 'default.svg') ?>" />
			</div>
			<!-- End Cover Image -->

			<!-- Category -->
			<h6 class="text-uppercase mt-4">Category</h6>
			<?php
			echo Bootstrap::formSelect(array(
				'id' => 'category',
				'name' => 'category',
				'selected' => ($pageKey ? $page->categoryKey() : ''),
				'options' => array_merge(array('' => $L->g('Uncategorized')), $categories->getKeyNameArray())
			));
			?>
			<!-- End Category -->

			<!-- Tags -->
			<h6 class="text-uppercase mt-4">Tags</h6>
			<div class="mb-1">
				<input id="addTag" name="addTag" type="text" class="form-control" value="" placeholder="<?php $L->p('Add tag') ?>">
			</div>
			<select id="tags" size="5" class="form-select" multiple aria-label="multiple select">
				<?php
				foreach ($tags->db as $key => $fields) {
					echo '<option value="' . $key . '" ' . ($pageKey && in_array($key, $page->tags(true)) ? 'selected' : '') . '>' . $fields['name'] . '</option>';
				}
				?>
			</select>
			<script>
				$(document).ready(function() {
					$('#addTag').keypress(function(e) {
						if (e.which == 13) {
							var value = $(this).val();
							if ($("#tags option[value='" + value + "']").length > 0) {
								$("#tags option[value='" + value + "']").prop('selected', true);
							} else {
								$('#tags').prepend($('<option>', {
									value: $(this).val(),
									text: $(this).val(),
									selected: true
								}));
							}
							$(this).val('');
							return false;
						}
					});

					$("#tags").on("mousedown", 'option', function(e) {
						e.preventDefault();
						$(this).prop('selected', !$(this).prop('selected'));
						enableBtnSave();
						return false;
					});
				});
			</script>
			<!-- End Tags -->

			<h6 class="text-uppercase mt-4">More options</h6>
			<ul class="list-group">
				<li class="list-group-item p-0 pt-3 bg-transparent border-0"><a onclick="fmOpen()" href="#"><i class="bi bi-files"></i>Files & images</a></li>
				<li class="list-group-item p-0 pt-3 bg-transparent border-0"><a onclick="openModal('description')" href="#"><i class="bi bi-info-square"></i>Description</a></li>
				<li class="list-group-item p-0 pt-3 bg-transparent border-0"><a onclick="openModal('date')" href="#"><i class="bi bi-calendar"></i>Publish date</a></li>
				<li class="list-group-item p-0 pt-3 bg-transparent border-0"><a onclick="openModal('friendlyURL')" href="#"><i class="bi bi-link"></i>Change URL</a></li>
				<li class="list-group-item p-0 pt-3 bg-transparent border-0"><a onclick="openModal('type')" href="#"><i class="bi bi-eye"></i>Type</a></li>
				<li class="list-group-item p-0 pt-3 bg-transparent border-0"><a onclick="openModal('seo')" href="#"><i class="bi bi-compass"></i>SEO features</a></li>
				<li class="list-group-item p-0 pt-3 bg-transparent border-0"><a onclick="openModal('parent')" href="#"><i class="bi bi-diagram-2"></i>Parent page</a></li>

			</ul>

			<!-- Quick files
			<h6 class="text-uppercase mt-4"><?php $L->p('Quick files') ?></h6>
			<div id="quickFiles">
				<div class="d-flex align-items-center mb-1">
					<i class="bi bi-image" style="font-size: 1.6rem;"></i>
					<span>photo1.jpg</span>
				</div>
				<div class="d-flex align-items-center mb-1">
					<i class="bi bi-image" style="font-size: 1.6rem;"></i>
					<span>test.txt</span>
				</div>
				<div class="d-flex align-items-center mb-1">
					<i class="bi bi-image" style="font-size: 1.6rem;"></i>
					<span>test.txt</span>
				</div>
			</div>
			End Quick files
-->

		</div> <!-- End <div class="col-sm-3 h-100"> -->
	</div> <!-- End <div class="row h-100"> -->
</div> <!-- End <div class="container-fluid h-100"> -->