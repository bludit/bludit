<?php
// Preload the first page of files to avoid an AJAX round-trip the first time
// the Media Manager is opened. Scans originals and resolves each thumbnail,
// mirroring the shape returned by ajax/list-images.
if (IMAGE_RESTRICT) {
	$mediaImagesPath = PATH_UPLOADS_PAGES.PAGE_IMAGES_KEY.DS;
	$mediaThumbnailsPath = PATH_UPLOADS_PAGES.PAGE_IMAGES_KEY.DS.'thumbnails'.DS;
	$mediaAbsoluteUrlPrefix = DOMAIN_UPLOADS_PAGES.PAGE_IMAGES_KEY.'/';
} else {
	$mediaImagesPath = PATH_UPLOADS;
	$mediaThumbnailsPath = PATH_UPLOADS_THUMBNAILS;
	$mediaAbsoluteUrlPrefix = DOMAIN_UPLOADS;
}
$mediaList = mediaManagerListImages($mediaImagesPath, $mediaThumbnailsPath, MEDIA_MANAGER_NUMBER_OF_FILES, PAGE_IMAGES_KEY, 1);
$preLoadFiles = $mediaList['files'];
$numberOfPages = $mediaList['numberOfPages'];
?>

<div id="jsmediaManagerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="jsmediaManagerTitle" aria-hidden="true">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="jsmediaManagerTitle"><i class="fa fa-image"></i> <span id="jsmediaManagerTitleText"><?php $L->p('Images'); ?></span></h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php $L->p('Close'); ?>"></button>
	</div>
	<div class="modal-body">

		<div id="jsalertMedia" class="alert alert-warning d-none" role="alert"></div>

		<div class="media-manager">
			<div class="media-manager-list">
				<div class="d-flex align-items-center gap-2 mb-2">
					<button type="button" id="jsmediaUploadButton" class="btn btn-sm btn-primary ms-auto"><i class="fa fa-upload"></i> <?php $L->p('Upload'); ?></button>
				</div>

				<!-- Upload input, the label below opens it and also receives drops -->
				<form name="bluditFormUpload" id="jsbluditFormUpload" enctype="multipart/form-data">
					<input type="file" id="jsimages" name="images[]" class="visually-hidden" multiple accept="<?php echo implode(',', array_map(function ($ext) { return '.'.$ext; }, $GLOBALS['ALLOWED_IMG_EXTENSION'])); ?>">
					<label id="jsmediaDropzone" for="jsimages" class="media-dropzone d-block mb-2">
						<span class="fa fa-cloud-upload fa-lg"></span>
						<span class="d-block"><?php $L->p('drop-images-here'); ?></span>
						<span class="d-block small text-muted"><?php echo implode(', ', $GLOBALS['ALLOWED_IMG_EXTENSION']); ?></span>
					</label>
				</form>

				<div id="jsmediaProgress" class="progress mb-2 d-none" style="height:6px;">
					<div id="jsbluditProgressBar" class="progress-bar bg-primary" role="progressbar" style="width:0%" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
				</div>

				<div id="jsmediaGrid" class="media-grid"></div>
				<nav id="jsmediaPagination" class="mt-3"></nav>
			</div>

			<div id="jsmediaDetails" class="media-details">
				<div id="jsmediaDetailsEmpty" class="text-secondary small"><?php $L->p('select-an-image'); ?></div>
				<div id="jsmediaDetailsContent" class="d-none">
					<img id="jsmediaPreview" class="media-details-preview mb-2" alt="">
					<div class="d-flex align-items-start justify-content-between gap-2">
						<div id="jsmediaFilename" class="fw-semibold text-break"></div>
						<span id="jsmediaCoverBadge" class="badge bg-primary d-none"><?php $L->p('Cover image'); ?></span>
					</div>
					<div id="jsmediaMeta" class="media-details-meta mb-3"></div>

					<label for="jsmediaAlt" class="form-label"><?php $L->p('Alt text'); ?></label>
					<textarea id="jsmediaAlt" class="form-control form-control-sm" rows="3" maxlength="500"></textarea>
					<div class="form-text"><?php $L->p('alt-text-tip'); ?></div>

					<div class="d-grid gap-2 mt-3">
						<button type="button" id="jsmediaInsert" class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> <?php $L->p('Insert'); ?></button>
						<button type="button" id="jsmediaSetCover" class="btn btn-sm btn-outline-primary"><i class="fa fa-desktop"></i> <?php $L->p('Set as cover image'); ?></button>
						<button type="button" id="jsmediaRemoveCover" class="btn btn-sm btn-outline-secondary d-none"><i class="fa fa-times"></i> <?php $L->p('Remove cover image'); ?></button>
						<button type="button" id="jsmediaInsertThumbnail" class="btn btn-sm btn-outline-primary"><i class="fa fa-image"></i> <?php $L->p('Insert thumbnail'); ?></button>
						<button type="button" id="jsmediaInsertLinked" class="btn btn-sm btn-outline-primary"><i class="fa fa-link"></i> <?php $L->p('Insert linked thumbnail'); ?></button>
						<button type="button" id="jsmediaCopyUrl" class="btn btn-sm btn-outline-secondary"><i class="fa fa-copy"></i> <?php $L->p('Copy URL'); ?></button>
						<button type="button" id="jsmediaReplace" class="btn btn-sm btn-outline-secondary" title="<?php $L->p('replace-image-tip'); ?>"><i class="fa fa-refresh"></i> <?php $L->p('Replace image'); ?></button>
						<button type="button" id="jsmediaDelete" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash-o"></i> <?php $L->p('Delete'); ?></button>
					</div>
					<div id="jsmediaCopyFeedback" class="small text-success mt-2 d-none" aria-live="polite"></div>
				</div>
			</div>
		</div>

	</div>
</div>
</div>
</div>

<input type="file" id="jsmediaReplaceInput" class="visually-hidden" accept="image/*">

<!-- Modal for Delete image -->
<div id="jsmediaDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="jsmediaDeleteTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="jsmediaDeleteTitle"><?php $L->p('Delete image'); ?></h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php $L->p('Close'); ?>"></button>
	</div>
	<div class="modal-body">
		<p id="jsmediaDeleteFilename" class="fw-semibold text-break"></p>
		<p><?php $L->p('are-you-sure-you-want-to-delete-this-image'); ?></p>
		<div id="jsmediaDeleteWarning" class="alert alert-warning d-none mb-0" role="alert"></div>
		<p class="text-muted small mb-0 mt-3"><?php $L->p('this-action-cannot-be-undone'); ?></p>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-link" data-bs-dismiss="modal"><?php $L->p('Cancel'); ?></button>
		<button type="button" class="btn btn-danger" id="jsmediaDeleteConfirm"><?php $L->p('Delete'); ?></button>
	</div>
</div>
</div>
</div>

<script>

<?php
echo 'var preLoadFiles = '.json_encode($preLoadFiles).';'.PHP_EOL;
echo 'var preLoadNumberOfPages = '.(int)$numberOfPages.';'.PHP_EOL;
echo 'var mediaAbsoluteUrlPrefix = '.json_encode($mediaAbsoluteUrlPrefix).';'.PHP_EOL;
echo 'var mediaTexts = '.json_encode(array(
	'imagesTitle' => $L->g('Images'),
	'coverTitle' => $L->g('Select cover image'),
	'noImages' => IMAGE_RESTRICT ? $L->g('There are no images for the page') : $L->g('There are no images'),
	'deleteCoverWarning' => $L->g('this-image-is-used-as-the-cover-image'),
	'deleteContentWarning' => $L->g('this-image-appears-in-the-content'),
	'urlCopied' => $L->g('url-copied'),
	'copyFailed' => $L->g('copy-url-failed'),
	'replaceSameType' => $L->g('replace-image-same-type'),
	'fileTypeNotSupported' => $L->g('File type is not supported. Allowed types:').' '.implode(', ', $GLOBALS['ALLOWED_IMG_EXTENSION']),
	'maxFileSize' => $L->g('Maximum load file size allowed:').' '.ini_get('upload_max_filesize'),
	'requestError' => $L->g('request-error'),
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).';'.PHP_EOL;
?>

var mediaManagerContext = 'insert';
var mediaCurrentPage = 1;
// Incremented on each listing request, older responses are ignored
var mediaListRequest = 0;
var mediaFiles = {};
var selectedMedia = null;
var mediaAltDirty = false;
var mediaAltTimer = null;
var mediaSelectAfterLoad = null;
var mediaCacheBuster = 0;
var pendingDeleteMedia = null;

function openMediaManager(context) {
	mediaManagerContext = (context === 'cover') ? 'cover' : 'insert';
	updateMediaManagerContext();
	bootstrap.Modal.getOrCreateInstance(document.getElementById('jsmediaManagerModal')).show();
}

function closeMediaManager() {
	bootstrap.Modal.getOrCreateInstance(document.getElementById('jsmediaManagerModal')).hide();
}

function showMediaAlert(message) {
	$("#jsalertMedia").text(message).removeClass('d-none');
}

function hideMediaAlert() {
	$("#jsalertMedia").addClass('d-none').text('');
}

function mediaImageUrl(filename) {
	return "<?php echo PAGE_IMAGES_URL; ?>" + filename;
}

function mediaThumbnailUrl(item) {
	return item.thumbnail ? ("<?php echo PAGE_THUMBNAILS_URL; ?>" + item.thumbnail) : mediaImageUrl(item.filename);
}

// Absolute URLs are used for previews and copy, because PAGE_IMAGES_URL can be
// relative to the editor document when IMAGE_RELATIVE_TO_ABSOLUTE is enabled.
function mediaAbsoluteUrl(filename) {
	return mediaAbsoluteUrlPrefix + filename;
}

function mediaCacheBust(url) {
	if (!mediaCacheBuster) {
		return url;
	}
	return url + (url.indexOf('?') === -1 ? '?' : '&') + 'v=' + mediaCacheBuster;
}

function mediaPreviewUrl(item) {
	var url = item.thumbnail ? ("<?php echo PAGE_THUMBNAILS_URL; ?>" + item.thumbnail) : mediaAbsoluteUrl(item.filename);
	return mediaCacheBust(url);
}

function formatBytes(bytes) {
	if (!bytes || bytes < 0) {
		return '';
	}
	var units = ['B', 'KB', 'MB', 'GB'];
	var i = 0;
	var value = bytes;
	while (value >= 1024 && i < units.length - 1) {
		value /= 1024;
		i++;
	}
	return (i === 0 ? value : value.toFixed(value >= 10 ? 0 : 1)) + ' ' + units[i];
}

function formatMime(mime) {
	if (!mime) {
		return '';
	}
	var parts = mime.split('/');
	var subtype = parts.length > 1 ? parts[1] : mime;
	return subtype.replace('+xml', '').toUpperCase();
}

function updateMediaManagerContext() {
	var cover = mediaManagerContext === 'cover';
	$("#jsmediaManagerTitleText").text(cover ? mediaTexts.coverTitle : mediaTexts.imagesTitle);
	$("#jsmediaInsert, #jsmediaInsertThumbnail, #jsmediaInsertLinked").toggleClass('d-none', cover);
	$("#jsmediaSetCover").toggleClass('btn-primary', cover).toggleClass('btn-outline-primary', !cover);
	updateSelectedDetails();
}

function clearMediaSelection() {
	selectedMedia = null;
	mediaAltDirty = false;
	$("#jsmediaDetailsEmpty").removeClass('d-none');
	$("#jsmediaDetailsContent").addClass('d-none');
	$("#jsmediaAlt").val('');
	$("#jsmediaCopyFeedback").addClass('d-none').text('');
}

function updateSelectedDetails() {
	var item = (selectedMedia && mediaFiles[selectedMedia]) ? mediaFiles[selectedMedia] : null;
	$("#jsmediaDetailsEmpty").toggleClass('d-none', item !== null);
	$("#jsmediaDetailsContent").toggleClass('d-none', item === null);

	if (!item) {
		return;
	}

	$("#jsmediaPreview").attr('src', mediaCacheBust(mediaAbsoluteUrl(item.filename)));
	$("#jsmediaFilename").text(item.filename).attr('title', item.filename);
	// Do not overwrite alt text the user is still editing
	if (!mediaAltDirty) {
		$("#jsmediaAlt").val(item.alt || '');
	}
	$("#jsmediaCopyFeedback").addClass('d-none').text('');

	var meta = [];
	if (item.width && item.height) {
		meta.push(item.width + ' × ' + item.height);
	}
	if (item.bytes) {
		meta.push(formatBytes(item.bytes));
	}
	if (item.mime) {
		meta.push(formatMime(item.mime));
	}
	$("#jsmediaMeta").text(meta.join(' · '));

	// Highlight the tile, and keep aria-pressed in sync
	$("#jsmediaGrid .media-tile").each(function () {
		var selected = ($(this).attr('data-filename') === item.filename);
		$(this).toggleClass('is-selected', selected).attr('aria-pressed', selected ? 'true' : 'false');
	});

	var isCover = ($("#jscoverImage").val() === item.filename);
	$("#jsmediaCoverBadge").toggleClass('d-none', !isCover);
	$("#jsmediaSetCover").toggleClass('d-none', isCover);
	$("#jsmediaRemoveCover").toggleClass('d-none', !isCover);
}

function selectMedia(filename) {
	if (!mediaFiles[filename]) {
		return;
	}
	if (selectedMedia === filename) {
		updateSelectedDetails();
		return;
	}
	if (selectedMedia) {
		saveSelectedMetadata();
	}
	selectedMedia = filename;
	mediaAltDirty = false;
	hideMediaAlert();
	updateSelectedDetails();

	// On narrow screens the details are below the grid: bring them into view
	// so tapping a tile gives visible feedback.
	if (window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches) {
		document.getElementById('jsmediaDetails').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
	}
}

function saveSelectedMetadata() {
	if (!selectedMedia || !mediaAltDirty) {
		return;
	}
	var filename = selectedMedia;
	var alt = $("#jsmediaAlt").val();
	mediaAltDirty = false;

	$.post(HTML_PATH_ADMIN_ROOT + "ajax/save-image-meta", {
		tokenCSRF: tokenCSRF,
		uuid: "<?php echo PAGE_IMAGES_KEY; ?>",
		filename: filename,
		alt: alt
	}, function (data) {
		if (data.status == 0) {
			if (mediaFiles[filename]) {
				mediaFiles[filename].alt = data.alt;
			}
		} else {
			mediaAltDirty = true;
			showMediaAlert(data.message);
		}
	}).fail(function () {
		mediaAltDirty = true;
		showMediaAlert(mediaTexts.requestError);
	});
}

function displayFiles(files, numberOfPages) {
	if (!Array.isArray(files)) {
		return false;
	}

	var grid = $("#jsmediaGrid").empty();
	mediaFiles = {};

	$.each(files, function (key, item) {
		mediaFiles[item.filename] = item;

		var tile = $('<button>', {
			type: 'button',
			'class': 'media-tile',
			'data-filename': item.filename,
			'aria-pressed': 'false',
			title: item.filename
		});
		$('<img>', {
			src: mediaPreviewUrl(item),
			alt: '',
			loading: 'lazy'
		}).appendTo(tile);
		$('<span>', {
			'class': 'media-tile-name',
			text: item.filename
		}).appendTo(tile);
		grid.append(tile);
	});

	if (files.length === 0) {
		$('<div>', {
			'class': 'text-secondary small py-3',
			text: mediaTexts.noImages
		}).appendTo(grid);
	}
	$("#jsmediaPagination").empty();
	if (numberOfPages > 1) {
		var pagination = $('<ul>', { 'class': 'pagination pagination-sm justify-content-center flex-wrap mb-0' });
		for (var i = 1; i <= numberOfPages; i++) {
			var pageItem = $('<li>', { 'class': 'page-item' + (i === mediaCurrentPage ? ' active' : '') });
			$('<button>', {
				type: 'button',
				'class': 'page-link',
				'data-page': i,
				text: i,
				'aria-label': 'Page ' + i
			}).appendTo(pageItem);
			pagination.append(pageItem);
		}
		$("#jsmediaPagination").append(pagination);
	}

	// The selected image is not in the new list (page change or delete)
	if (selectedMedia && !mediaFiles[selectedMedia]) {
		saveSelectedMetadata();
		clearMediaSelection();
	}

	if (mediaSelectAfterLoad && mediaFiles[mediaSelectAfterLoad]) {
		var filename = mediaSelectAfterLoad;
		mediaSelectAfterLoad = null;
		selectMedia(filename);
	} else if (selectedMedia && mediaFiles[selectedMedia]) {
		updateSelectedDetails();
	} else {
		clearMediaSelection();
	}
}

// Get the list of files via AJAX, paginated
function getFiles(pageNumber, callback) {
	mediaCurrentPage = parseInt(pageNumber, 10) || 1;
	var request = ++mediaListRequest;

	$.post(HTML_PATH_ADMIN_ROOT + "ajax/list-images", {
		tokenCSRF: tokenCSRF,
		pageNumber: mediaCurrentPage,
		uuid: "<?php echo PAGE_IMAGES_KEY; ?>",
		path: "thumbnails"
	}, function (data) {
		if (request !== mediaListRequest) {
			return;
		}
		if (data.status == 0) {
			displayFiles(data.files, data.numberOfPages);
			if (typeof callback === 'function') {
				callback(data.files, data.numberOfPages);
			}
		} else {
			showMediaAlert(data.message || mediaTexts.requestError);
		}
	}).fail(function () {
		if (request === mediaListRequest) {
			showMediaAlert(mediaTexts.requestError);
		}
	});
}

function deleteMedia(filename, onSuccess, onError) {
	$.post(HTML_PATH_ADMIN_ROOT + "ajax/delete-image", {
		tokenCSRF: tokenCSRF,
		filename: filename,
		uuid: "<?php echo PAGE_IMAGES_KEY; ?>"
	}, function (data) {
		if (data.status == 0) {
			if (typeof onSuccess === 'function') {
				onSuccess();
			}
		} else {
			if (typeof onError === 'function') {
				onError(data.message || mediaTexts.requestError);
			}
		}
	}).fail(function () {
		if (typeof onError === 'function') {
			onError(mediaTexts.requestError);
		}
	});
}

function setCoverImage(filename) {
	$("#jscoverImage").val(filename);
	$("#jsexternalCoverImage").val('');
	$("#jscoverImagePreview").attr("src", mediaAbsoluteUrl(filename));
}

function removeCoverImage() {
	$("#jscoverImage").val('');
	$("#jsexternalCoverImage").val('');
	$("#jscoverImagePreview").attr("src", "<?php echo HTML_PATH_CORE_IMG; ?>default.svg");
	updateSelectedDetails();
}

function copyText(text, onSuccess, onError) {
	if (navigator.clipboard && window.isSecureContext) {
		navigator.clipboard.writeText(text).then(onSuccess).catch(function () {
			copyTextFallback(text, onSuccess, onError);
		});
		return;
	}
	copyTextFallback(text, onSuccess, onError);
}

function copyTextFallback(text, onSuccess, onError) {
	var textarea = document.createElement('textarea');
	textarea.value = text;
	textarea.style.position = 'fixed';
	textarea.style.opacity = '0';
	document.body.appendChild(textarea);
	textarea.select();
	try {
		if (document.execCommand('copy')) {
			onSuccess();
		} else {
			onError();
		}
	} catch (error) {
		onError();
	}
	document.body.removeChild(textarea);
}

function imageIsUsedInContent(filename) {
	try {
		if (typeof editorGetContent != 'function') {
			return false;
		}
		return editorGetContent().indexOf(filename) !== -1;
	} catch (error) {
		return false;
	}
}

function uploadImages(files) {
	hideMediaAlert();

	var images = files || $("#jsimages")[0].files;
	if (!images || images.length === 0) {
		return false;
	}

	var validImageTypes = <?php echo json_encode($GLOBALS['ALLOWED_IMG_MIMETYPES']); ?>;
	for (var i = 0; i < images.length; i++) {
		// Check file type/extension
		if (!validImageTypes.includes(images[i].type)) {
			showMediaAlert(mediaTexts.fileTypeNotSupported + ' (' + images[i].name + ')');
			$("#jsimages").val('');
			return false;
		}

		// Check file size and compare with PHP upload_max_filesize
		if (images[i].size > UPLOAD_MAX_FILESIZE) {
			showMediaAlert(mediaTexts.maxFileSize + ' (' + images[i].name + ')');
			$("#jsimages").val('');
			return false;
		}
	}

	// Clean and show the progress bar
	$("#jsmediaProgress").removeClass("d-none");
	$("#jsbluditProgressBar").removeClass("bg-success bg-danger").addClass("bg-primary").width("0%").attr("aria-valuenow", 0);

	var formData = new FormData();
	for (var j = 0; j < images.length; j++) {
		formData.append("images[]", images[j]);
	}
	formData.append("uuid", "<?php echo PAGE_IMAGES_KEY; ?>");
	formData.append("tokenCSRF", tokenCSRF);

	$.ajax({
		url: HTML_PATH_ADMIN_ROOT + "ajax/upload-images",
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		xhr: function () {
			var xhr = $.ajaxSettings.xhr();
			if (xhr.upload) {
				xhr.upload.addEventListener("progress", function (e) {
					if (e.lengthComputable) {
						var percentComplete = (e.loaded / e.total) * 100;
						$("#jsbluditProgressBar").width(percentComplete + "%").attr("aria-valuenow", Math.round(percentComplete));
					}
				}, false);
			}
			return xhr;
		}
	}).done(function (data) {
		$("#jsimages").val('');
		if (data.status == 0) {
			$("#jsbluditProgressBar").removeClass("bg-primary").addClass("bg-success").width("100%").attr("aria-valuenow", 100);
			mediaSelectAfterLoad = (data.images && data.images.length) ? data.images[0] : null;
			getFiles(1);
			setTimeout(function () {
				$("#jsmediaProgress").addClass("d-none");
			}, 900);
		} else {
			$("#jsbluditProgressBar").removeClass("bg-primary").addClass("bg-danger");
			showMediaAlert(data.message || mediaTexts.requestError);
		}
	}).fail(function () {
		$("#jsbluditProgressBar").removeClass("bg-primary").addClass("bg-danger");
		showMediaAlert(mediaTexts.requestError);
	});
}

$(document).ready(function () {
	// Display the files preloaded for the first time
	mediaCurrentPage = 1;
	displayFiles(preLoadFiles, preLoadNumberOfPages);

	// The media manager is a modal: when it closes, save the alt text that is
	// still being typed.
	var mediaModal = document.getElementById('jsmediaManagerModal');
	mediaModal.addEventListener('show.bs.modal', function () {
		updateMediaManagerContext();
	});
	mediaModal.addEventListener('hidden.bs.modal', function () {
		saveSelectedMetadata();
	});

	// Toolbar upload button
	$("#jsmediaUploadButton").on("click", function () {
		$("#jsimages").trigger("click");
	});

	// Upload when files are selected
	$("#jsimages").on("change", function () {
		uploadImages();
	});

	// Drag and drop files on the drop zone, only files are accepted
	var dropzone = $("#jsmediaDropzone");
	dropzone.on("dragenter dragover", function (e) {
		e.preventDefault();
		e.stopPropagation();
		dropzone.addClass("is-dragover");
	});
	dropzone.on("dragleave", function (e) {
		e.preventDefault();
		e.stopPropagation();
		dropzone.removeClass("is-dragover");
	});
	dropzone.on("drop", function (e) {
		e.preventDefault();
		e.stopPropagation();
		dropzone.removeClass("is-dragover");
		var files = e.originalEvent.dataTransfer.files;
		if (files && files.length) {
			uploadImages(files);
		}
	});

	// Select a tile
	$("#jsmediaGrid").on("click", ".media-tile", function () {
		selectMedia($(this).attr("data-filename"));
	});

	// Pagination
	$("#jsmediaPagination").on("click", ".page-link", function (e) {
		e.preventDefault();
		getFiles(parseInt($(this).attr("data-page"), 10));
	});

	// Alt text: save after a pause, and when the field loses focus
	$("#jsmediaAlt").on("input", function () {
		mediaAltDirty = true;
		clearTimeout(mediaAltTimer);
		mediaAltTimer = setTimeout(saveSelectedMetadata, 700);
	}).on("change", function () {
		clearTimeout(mediaAltTimer);
		saveSelectedMetadata();
	});

	// Insert the original image, its thumbnail or a linked thumbnail
	$("#jsmediaInsert").on("click", function () {
		if (!selectedMedia || !mediaFiles[selectedMedia]) {
			return;
		}
		var item = mediaFiles[selectedMedia];
		var alt = $("#jsmediaAlt").val() || '';
		saveSelectedMetadata();
		if (typeof editorInsertMedia != "function") {
			return;
		}
		editorInsertMedia(mediaImageUrl(item.filename), alt);
		closeMediaManager();
	});

	$("#jsmediaInsertThumbnail").on("click", function () {
		if (!selectedMedia || !mediaFiles[selectedMedia]) {
			return;
		}
		var item = mediaFiles[selectedMedia];
		var alt = $("#jsmediaAlt").val() || '';
		saveSelectedMetadata();
		if (typeof editorInsertMedia != "function") {
			return;
		}
		editorInsertMedia(mediaThumbnailUrl(item), alt);
		closeMediaManager();
	});

	$("#jsmediaInsertLinked").on("click", function () {
		if (!selectedMedia || !mediaFiles[selectedMedia]) {
			return;
		}
		var item = mediaFiles[selectedMedia];
		var alt = $("#jsmediaAlt").val() || '';
		saveSelectedMetadata();
		if (typeof editorInsertLinkedMedia != "function") {
			return;
		}
		editorInsertLinkedMedia(mediaThumbnailUrl(item), mediaImageUrl(item.filename), alt);
		closeMediaManager();
	});

	// Set or remove the cover image
	$("#jsmediaSetCover").on("click", function () {
		if (!selectedMedia) {
			return;
		}
		saveSelectedMetadata();
		setCoverImage(selectedMedia);
		closeMediaManager();
	});

	$("#jsmediaRemoveCover").on("click", function () {
		removeCoverImage();
	});

	// Copy the absolute URL of the original image
	$("#jsmediaCopyUrl").on("click", function () {
		if (!selectedMedia) {
			return;
		}
		var url = mediaAbsoluteUrl(selectedMedia);
		copyText(url, function () {
			$("#jsmediaCopyFeedback").text(mediaTexts.urlCopied).removeClass("d-none");
			setTimeout(function () {
				$("#jsmediaCopyFeedback").addClass("d-none");
			}, 2000);
		}, function () {
			showMediaAlert(mediaTexts.copyFailed);
		});
	});

	// Replace the file, keeping the filename
	$("#jsmediaReplace").on("click", function () {
		if (!selectedMedia) {
			return;
		}
		var extension = selectedMedia.split('.').pop();
		$("#jsmediaReplaceInput").val('').attr("accept", "." + extension).trigger("click");
	});

	$("#jsmediaReplaceInput").on("change", function () {
		var input = this;
		if (!selectedMedia || !input.files || !input.files.length) {
			return;
		}
		var filename = selectedMedia;
		var file = input.files[0];
		var extension = filename.split('.').pop().toLowerCase();
		var fileExtension = file.name.split('.').pop().toLowerCase();

		if (fileExtension !== extension) {
			showMediaAlert(mediaTexts.replaceSameType);
			input.value = '';
			return;
		}

		hideMediaAlert();
		var formData = new FormData();
		formData.append("image", file);
		formData.append("filename", filename);
		formData.append("uuid", "<?php echo PAGE_IMAGES_KEY; ?>");
		formData.append("tokenCSRF", tokenCSRF);

		$("#jsmediaReplace").prop("disabled", true);
		$.ajax({
			url: HTML_PATH_ADMIN_ROOT + "ajax/replace-image",
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false
		}).done(function (data) {
			input.value = '';
			$("#jsmediaReplace").prop("disabled", false);
			if (data.status == 0) {
				// The URL is the same, bust the browser cache for previews
				mediaCacheBuster = Date.now();
				mediaSelectAfterLoad = filename;
				getFiles(mediaCurrentPage);
			} else {
				showMediaAlert(data.message || mediaTexts.requestError);
			}
		}).fail(function () {
			input.value = '';
			$("#jsmediaReplace").prop("disabled", false);
			showMediaAlert(mediaTexts.requestError);
		});
	});

	// Delete: ask first, and warn when the image is used as cover or in content
	$("#jsmediaDelete").on("click", function () {
		if (!selectedMedia) {
			return;
		}
		pendingDeleteMedia = selectedMedia;
		$("#jsmediaDeleteFilename").text(pendingDeleteMedia);

		var warnings = [];
		if ($("#jscoverImage").val() === pendingDeleteMedia) {
			warnings.push(mediaTexts.deleteCoverWarning);
		}
		if (imageIsUsedInContent(pendingDeleteMedia)) {
			warnings.push(mediaTexts.deleteContentWarning);
		}
		$("#jsmediaDeleteWarning").text(warnings.join(' ')).toggleClass("d-none", warnings.length === 0);
		$("#jsmediaDeleteConfirm").prop("disabled", false);
		bootstrap.Modal.getOrCreateInstance(document.getElementById('jsmediaDeleteModal')).show();
	});

	$("#jsmediaDeleteConfirm").on("click", function () {
		if (!pendingDeleteMedia) {
			return;
		}
		var filename = pendingDeleteMedia;
		var confirmButton = $(this).prop("disabled", true);

		deleteMedia(filename, function () {
			confirmButton.prop("disabled", false);
			bootstrap.Modal.getOrCreateInstance(document.getElementById('jsmediaDeleteModal')).hide();

			if (selectedMedia === filename) {
				clearMediaSelection();
			}
			if ($("#jscoverImage").val() === filename) {
				removeCoverImage();
			}

			var page = mediaCurrentPage;
			getFiles(page, function (files) {
				if (files.length === 0 && page > 1) {
					getFiles(page - 1);
				}
			});
		}, function (message) {
			confirmButton.prop("disabled", false);
			bootstrap.Modal.getOrCreateInstance(document.getElementById('jsmediaDeleteModal')).hide();
			showMediaAlert(message);
		});
	});

	document.getElementById('jsmediaDeleteModal').addEventListener('hidden.bs.modal', function () {
		pendingDeleteMedia = null;
		$("#jsmediaDeleteConfirm").prop("disabled", false);
		// Bootstrap removes the body scroll lock when a modal closes; keep it
		// while the Media Manager is still open behind the confirmation.
		if (document.getElementById('jsmediaManagerModal').classList.contains('show')) {
			document.body.classList.add('modal-open');
		}
	});
});

</script>
