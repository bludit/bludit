<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
// ----------------------------------------------------------------------------
// Variables for the view
// ----------------------------------------------------------------------------
var _pageKey = null; // The page key is generated the first time the user click on the button "Save"
var _uuid = '<?php echo $uuid ?>'; // The UUID is generated at the begining if the user uploaded files to the page

// ----------------------------------------------------------------------------
// Functions for the view
// ----------------------------------------------------------------------------
// Default function for the editor area (textarea)
// This helps if the user doesn't activate any plugin as editor
if (typeof editorGetContent != 'function') {
	window.editorGetContent = function(){
		return $('#editor').val();
	};
}
if (typeof editorInsertContent != 'function') {
	window.editorInsertContent = function(html){
		$('#editor').val($('#editor').val()+html);
	};
}

// Creates or save the page
// This function set the global variable "_pageKey"
function save(args) {
	args['uuid'] = _uuid;
	// If the "page key" doesn't exists means the page not was created
	// Create the page to generate a "page key"
	if (_pageKey == null) {
		logs('Creating page');
		api.createPage(args).then(function(key) {
			logs('Page created. Key: '+key);
			// Set the global variable with the page key
			_pageKey = key;
			// Disable the button save and change text
			//$("#btnSave").attr("disabled", true).html("Saved");
		});
	} else {
		logs('Saving page');
		args['pageKey'] = _pageKey;
		api.savePage(args).then(function(key) {
			logs('Page saved. Old key: '+_pageKey+' / New key: '+key);
			// Set the global variable with the page key
			// The page key can change after save the page so you need to set again the variable
			_pageKey = key;
			// Disable the button save and change text
			//$("#btnSave").attr("disabled", true).html("Saved");
		});
	}

	// Close all modals
	$('.modal').modal('hide');
	return true;
}

// Open the modal and store the current value
// The current value is store to recover it if the user click on the button "Cancel"
function openModal(fieldName) {
	var value = $('#'+fieldName).val();
	localStorage.setItem(fieldName, value);
	$('#modal-'+fieldName).modal('show');
}

// Close the modal when the user click in the button "Cancel"
// The function also recover the old value
function closeModal(fieldName) {
	var value = localStorage.getItem(fieldName);
	$('#'+fieldName).val(value);
	$('#modal-'+fieldName).modal('hide');
}

// This function is to catch all key press
// Provides Shortcuts
// The editor plugin need to call this function for the event "keydown"
function keypress(event) {
	logs(event);

	// Shortcuts
	// ------------------------------------------------------------------------
	// Ctrl+S or Command+S
	if ((event.ctrlKey || event.metaKey) && event.which == 83) {
		var args = {
			title: $('#title').val(),
			content: editorGetContent(),
			category: $('#category option:selected').val(),
			tags: $('#tags').val()
		}
		save(args);
		$('#btnSave').addClass('btn-primary-disabled').html('<?php $L->p('Saved') ?>');
		event.preventDefault();
		return false;
	}

	$('#btnSave').removeClass('btn-primary-disabled').html('<?php $L->p('Save') ?>');
}

// ----------------------------------------------------------------------------
// Events for the view
// ----------------------------------------------------------------------------
$(document).ready(function() {

	// Main interface events
	// ------------------------------------------------------------------------
	$(this).keydown(function(event){
		keypress(event);
	});

	$('#btnSave').on('click', function() {
		var args = {
			title: $('#title').val(),
			content: editorGetContent(),
			category: $('#category option:selected').val(),
			tags: $('#tags').val()
		}
		save(args);
		$(this).addClass('btn-primary-disabled').html('<?php $L->p('Saved') ?>');
	});

	$("#btnPreview").on("click", function() {
		var title = $("#jstitle").val();
		var content = editorGetContent();
		bluditAjax.saveAsDraft(uuid, title, content).then(function(data) {
			var preview = window.open("<?php echo DOMAIN_PAGES.'autosave-'.$uuid.'?preview='.md5('autosave-'.$uuid) ?>", "bludit-preview");
			preview.focus();
		});
	});

	$('#btnCurrenStatus').on('click', function() {
		openModal('status');
	});

	$('#category').on("change", function() {
		$('#btnSave').html('<?php $L->p('Save') ?>');
	});

	// Modal description events
	// ------------------------------------------------------------------------
	$('#btnSaveDescription').on('click', function() {
		var args = {
			description: $('#description').val()
		};
		save(args);
	});

	$('#btnCancelDescription').on('click', function() {
		closeModal('description');
	});

	// Modal date events
	// ------------------------------------------------------------------------
	$('#btnSaveDate').on('click', function() {
		var args = {
			date: $('#date').val()
		};
		save(args);
	});

	$('#btnCancelDate').on('click', function() {
		closeModal('date');
	});

	// Modal friendly-url events
	// ------------------------------------------------------------------------
	$('#btnSaveFriendlyURL').on('click', function() {
		var args = {
			slug: $('#friendlyURL').val()
		};
		save(args);
	});

	$('#btnCancelFriendlyURL').on('click', function() {
		closeModal('friendlyURL');
	});

	$('#btnGenURLFromTitle').on('click', function() {
		var args = {
			text: $('#title').val(),
			parentKey: $('#parent').val(),
			pageKey: _pageKey
		}
		api.friendlyURL(args).then(function(slug) {
			$('#friendlyURL').val(slug);
		});
	});

	// Modal status events
	// ------------------------------------------------------------------------
	$('#btnSaveStatus').on('click', function() {
		var args = {
			type: $('input[name="status"]:checked').val()
		};
		save(args);

		if (args['type']=='draft') {
			$('#btnCurrenStatus').html('<i class="bi-square-fill"></i> <?php $L->p('Draft') ?>');
		} else if (args['type']=='published') {
			$('#btnCurrenStatus').html('<i class="bi-check-square-fill"></i> <?php $L->p('Published') ?>');
		} else if (args['type']=='unlisted') {
			$('#btnCurrenStatus').html('<i class="bi-check-square-fill"></i> <?php $L->p('Unlisted') ?>');
		} else if (args['type']=='sticky') {
			$('#btnCurrenStatus').html('<i class="bi-check-square-fill"></i> <?php $L->p('Sticky') ?>');
		} else if (args['type']=='static') {
			$('#btnCurrenStatus').html('<i class="bi-check-square-fill"></i> <?php $L->p('Static') ?>');
		}
	});

	$('#btnCancelStatus').on('click', function() {
		closeModal('status');
	});

	// Modal SEO events
	// ------------------------------------------------------------------------
	$('#btnSaveSeo').on('click', function() {
		var args = {
			parent: $('#parent').val()
		};
		save(args);
	});

	$('#btnCancelSeo').on('click', function() {
		closeModal('parent');
	});

	// Modal parent events
	// ------------------------------------------------------------------------
	$('#btnSaveParent').on('click', function() {
		var args = {
			parent: $('#parent').val()
		};
		save(args);
	});

	$('#btnCancelParent').on('click', function() {
		closeModal('parent');
	});

});

// ----------------------------------------------------------------------------
// Initlization for the view
// ----------------------------------------------------------------------------
$(document).ready(function() {
	// nothing here yet
	// how do you hang your toilet paper ? over or under ?
});
</script>

<!-- Modal Description -->
<div class="modal" id="modal-description" tabindex="-1" aria-labelledby="modal-description" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label for="parent" class="font-weight-bold">Page description</label>
					<textarea id="description" name="description" class="form-control" rows="3"></textarea>
					<small class="form-text text-muted"><?php echo $L->get('this-field-can-help-describe-the-content') ?></small>
				</div>
			</div>
			<div class="modal-footer modal-footer pl-2 pr-2 pt-1 pb-1">
				<button id="btnCancelDescription" type="button" class="btn btn-cancel font-weight-bold mr-auto"><i class="fa fa-times"></i> Cancel</button>
				<button id="btnSaveDescription" type="button" class="btn btn-save font-weight-bold"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal Description -->

<!-- Modal Date -->
<div class="modal" id="modal-date" aria-labelledby="modal-date" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label for="date" class="font-weight-bold">Publish date</label>
					<input id="date" name="date" type="text" class="form-control" value="<?php echo Date::current(DB_DATE_FORMAT) ?>">
					<small class="form-text text-muted"><?php echo $L->g('date-format-format') ?></small>
				</div>
			</div>
			<div class="modal-footer modal-footer pl-2 pr-2 pt-1 pb-1">
				<button id="btnCancelDate" type="button" class="btn btn-cancel font-weight-bold mr-auto"><i class="fa fa-times"></i> Cancel</button>
				<button id="btnSaveDate" type="button" class="btn btn-save font-weight-bold"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	$("#date").datetimepicker({format:DB_DATE_FORMAT});
});
</script>
<!-- End Modal Date -->

<!-- Modal friendly URL -->
<div class="modal" id="modal-friendlyURL" tabindex="-1" aria-labelledby="modal-friendlyURL" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<div class="d-flex mb-2">
						<label for="friendlyURL" class="p-0 m-0 mr-auto font-weight-bold">Page URL</label>
						<button id="btnGenURLFromTitle" type="button" class="btn p-0 m-0"><i class="fa fa-magic"></i> Generate from page title</button>
					</div>
					<input id="friendlyURL" name="friendlyURL" type="text" class="form-control" value="">
					<small class="form-text text-muted">https://www.varlogdiego.com/my-page-about-k8s</small>
				</div>
			</div>
			<div class="modal-footer modal-footer pl-2 pr-2 pt-1 pb-1">
				<button id="btnCancelFriendlyURL" type="button" class="btn btn-cancel font-weight-bold mr-auto"><i class="fa fa-times"></i> Cancel</button>
				<button id="btnSaveFriendlyURL" type="button" class="btn btn-save font-weight-bold"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal friendly URL -->

<!-- Modal Parent -->
<div class="modal" id="modal-parent" aria-labelledby="modal-parent" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label for="parent" class="font-weight-bold">Parent page</label>
					<select id="parent" name="parent" class="custom-select"></select>
					<small class="form-text text-muted"><?php echo $L->g('Start typing a page title to see a list of suggestions.') ?></small>
				</div>
			</div>
			<div class="modal-footer modal-footer pl-2 pr-2 pt-1 pb-1">
				<button id="btnCancelParent" type="button" class="btn btn-cancel font-weight-bold mr-auto"><i class="fa fa-times"></i> Cancel</button>
				<button id="btnSaveParent" type="button" class="btn btn-save font-weight-bold"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	var parent = $("#parent").select2({
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
<!-- End Modal Parent -->

<!-- Modal Status -->
<div class="modal" id="modal-status" tabindex="-1" aria-labelledby="modal-status" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label class="font-weight-bold">Page status</label>
				</div>
				<div class="form-check mb-2">
					<input id="statusDraft" name="status" class="form-check-input" type="radio" value="draft" checked>
					<label class="form-check-label" for="statusDraft">Draft</label>
					<small class="form-text text-muted">Page as draft, is not visible for visitors.</small>
				</div>
				<div class="form-check mb-2">
					<input id="statusPublish" name="status" class="form-check-input" type="radio" value="published">
					<label class="form-check-label" for="statusPublish">Publish</label>
					<small class="form-text text-muted">Publish the page, everyone can see it.</small>
				</div>
				<hr>
				<div class="form-check mb-2">
					<input id="statusSticky" name="status" class="form-check-input" type="radio" value="sticky">
					<label class="form-check-label" for="statusSticky">Publish as sticky</label>
					<small class="form-text text-muted">The page can be seen by everyone in the top of the main page.</small>
				</div>
				<div class="form-check mb-2">
					<input id="statusStatic" name="status" class="form-check-input" type="radio" value="static">
					<label class="form-check-label" for="statusStatic">Publish as static</label>
					<small class="form-text text-muted">The page can be seen by everyone as static page.</small>
				</div>
				<div class="form-check mb-2">
					<input id="statusUnlisted" name="status" class="form-check-input" type="radio" value="unlisted">
					<label class="form-check-label" for="statusUnlisted">Publish as unlisted</label>
					<small class="form-text text-muted">The page can be seen and shared by anyone with the link.</small>
				</div>
			</div>
			<div class="modal-footer modal-footer pl-2 pr-2 pt-1 pb-1">
				<button id="btnCancelStatus" type="button" class="btn btn-cancel font-weight-bold mr-auto" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
				<button id="btnSaveStatus" type="button" class="btn btn-save font-weight-bold"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal Status -->

<!-- Modal SEO -->
<div class="modal" id="modal-seo" tabindex="-1" aria-labelledby="modal-seo" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label class="font-weight-bold">SEO features</label>
				</div>
				<div class="form-check mb-2">
					<input id="noindex" name="noindex" class="form-check-input" type="checkbox" value="noindex">
					<label class="form-check-label" for="noindex"><?php echo $L->g('apply-code-noindex-code-to-this-page') ?></label>
					<small class="form-text text-muted"><?php echo $L->g('This tells search engines not to show this page in their search results.') ?></small>
				</div>
				<div class="form-check mb-2">
					<input id="nofollow" name="nofollow" class="form-check-input" type="checkbox" value="nofollow">
					<label class="form-check-label" for="nofollow"><?php echo $L->g('apply-code-nofollow-code-to-this-page') ?></label>
					<small class="form-text text-muted"><?php echo $L->g('This tells search engines not to follow links on this page.') ?></small>
				</div>
				<div class="form-check mb-2">
					<input id="noarchive" name="noarchive" class="form-check-input" type="checkbox" value="noarchive">
					<label class="form-check-label" for="noarchive"><?php echo $L->g('apply-code-noarchive-code-to-this-page') ?></label>
					<small class="form-text text-muted"><?php echo $L->g('This tells search engines not to save a cached copy of this page.') ?></small>
				</div>
			</div>
			<div class="modal-footer modal-footer pl-2 pr-2 pt-1 pb-1">
				<button id="btnCancelSeo" type="button" class="btn btn-cancel font-weight-bold mr-auto" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
				<button id="btnSaveSeo" type="button" class="btn btn-save font-weight-bold"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal SEO -->

<!-- Modal Files / Images -->
<div class="modal" id="modal-files" tabindex="-1" aria-labelledby="modal-files" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="m-0">
					<label class="font-weight-bold">Files</label>
				</div>

			</div>
			<div class="modal-footer modal-footer pl-2 pr-2 pt-1 pb-1">
				<button id="btnCancelSeo" type="button" class="btn btn-cancel font-weight-bold mr-auto" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
				<button id="btnSaveSeo" type="button" class="btn btn-save font-weight-bold"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal SEO -->

<div class="container-fluid h-100">
<div class="row h-100">
<div class="col-sm-9 d-flex flex-column h-100">

<!-- Toolbar > Save, Preview, Status and Options -->
<div id="editorToolbar">
	<div id="editorToolbarRight" class="btn-group btn-group-sm float-end" role="group" aria-label="Toolbar right">
		<div class="dropdown">
			<button type="button" class="btn dropdown-toggle" type="button" id="dropdownMenuOptions" data-bs-toggle="dropdown" aria-expanded="false">
				<span class="fa fa-cog"></span><?php $L->p('Options') ?>
			</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuOptions">
				<a onclick="openModal('description')" 	class="dropdown-item" href="#"><i class="fa fa-comment"></i> Description</a>
				<a onclick="openModal('date')" 			class="dropdown-item" href="#"><i class="fa fa-calendar"></i> Publish date</a>
				<a onclick="openModal('friendlyURL')" 	class="dropdown-item" href="#"><i class="fa fa-link"></i> Change URL</a>
				<a onclick="openModal('status')" 		class="dropdown-item" href="#"><i class="fa fa-eye"></i> Status</a>
				<a onclick="openModal('seo')" 			class="dropdown-item" href="#"><i class="fa fa-compass"></i> SEO features</a>
				<a onclick="openModal('parent')" 		class="dropdown-item" href="#"><i class="fa fa-sitemap"></i> Parent page</a>
			</div>
		</div>
	</div>

	<div id="editorToolbarLeft">
		<button id="btnSave" type="button" class="btn btn-sm btn-primary" ><?php $L->p('Save') ?></button>
		<button id="btnPreview" type="button" class="btn btn-sm btn-secondary"><?php $L->p('Preview') ?></button>
		<span   id="btnCurrenStatus"><i class="bi-square-fill ms-1 me-1"></i><span><?php $L->p('Draft') ?></span></span>
	</div>
</div>
<!-- End Toolbar > Save, Preview, Status and Options -->

<!-- Title -->
<div class="mb-1">
	<input id="title" name="title" type="text" class="form-control form-control-lg" value="" placeholder="<?php $L->p('Enter title') ?>">
</div>
<!-- End Title -->

<!-- Editor -->
<textarea class="form-control flex-grow-1" placeholder="" id="editor"></textarea>
<!-- End Editor -->

</div> <!-- End <div class="col-sm-9 h-100"> -->

<div class="col-sm-3 h-100">

	<!-- Cover Image -->
	<h6 class="mt-1 mb-2 pb-2 text-uppercase"><?php $L->p('Cover Image') ?></h6>
	<div>
		<img id="jscoverImagePreview" class="mx-auto d-block w-100" alt="Cover image preview" src="<?php echo HTML_PATH_CORE_IMG ?>default.svg" />
	</div>
	<!-- End Cover Image -->

	<!-- Images -->
	<h6 class="mt-4 mb-2 pb-2 text-uppercase"><?php $L->p('Images') ?></h6>

    <div class="media text-muted pt-3">
      <svg class="align-self-center me-3 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 32x32"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"></rect><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
      <div class="media-body">
        <div class="mt-0">
			photo1.jpg
        </div>
      </div>
    </div>

    <div class="media text-muted pt-3">
      <svg class="align-self-center me-3 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 32x32"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"></rect><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
      <div class="media-body">
        <div class="mt-0">
			photo2.jpg
        </div>
      </div>
    </div>

    <small class="d-block text-end mt-3">
      <a href="#">All images</a>
    </small>
  <!-- End Images -->

	<!-- Category -->
	<div class="m-0">
		<h6 class="mt-4 mb-2 pb-2 text-uppercase">Category</h6>
		<select id="category" name="category" class="custom-select">
			<option value="">- Uncategorized -</option>
			<?php foreach ($categories->db as $key=>$fields): ?>
			<option value="<?php echo $key ?>"><?php echo $fields['name']?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<!-- End Category -->

	<!-- Tags -->
	<h6 class="mt-4 mb-2 pb-2 text-uppercase">Tags</h6>
	<input id="tags" name="tags" type="text" value="">
	<script>
	$(document).ready(function() {
		$('#tags').tagsInput({
			placeholder:'Add a tag',
			delimiter:',',
			removeWithBackspace:true,
			'autocomplete': {
				source: [
				<?php
					foreach ($tags->db as $key=>$fields) {
						echo '"'.$fields['name'].'",';
					}
				?>
				]
			}
		});
	});
		</script>
	<!-- End Tags -->

</div> <!-- End <div class="col-sm-3 h-100"> -->
</div> <!-- End <div class="row h-100"> -->
</div> <!-- End <div class="container-fluid h-100"> -->

<script>
$(document).ready(function() {


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
