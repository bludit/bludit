<?php
// Preload the first 10 files to not call via AJAX when the user open the first time the media manager
$listOfFilesByPage = Filesystem::listFiles(PAGE_THUMBNAILS_DIRECTORY, '*', '*', MEDIA_MANAGER_SORT_BY_DATE, MEDIA_MANAGER_NUMBER_OF_FILES);
$preLoadFiles = array();
if (!empty($listOfFilesByPage[0])) {
	foreach ($listOfFilesByPage[0] as $file) {
		$filename = Filesystem::filename($file);
		array_push($preLoadFiles, $filename);
	}
}

// Amount of pages for the paginator
$numberOfPages = count($listOfFilesByPage);
?>

<div id="jsmediaManagerModal" class="modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="container-fluid">
<div class="row">
	<div class="col p-3">

	<!--
		UPLOAD INPUT
	-->
		<h3 class="mt-2 mb-3"><i class="fa fa-image"></i> <?php $L->p('Images'); ?></h3>

		<div id="jsalertMedia" class="alert alert-warning d-none" role="alert"></div>

		<!-- Form and Input file -->
		<form name="bluditFormUpload" id="jsbluditFormUpload" enctype="multipart/form-data">
			<div class="custom-file">
				<input type="file" class="custom-file-input" id="jsimages" name="images[]" multiple>
				<label class="custom-file-label" for="jsimages"><?php $L->p('Choose images to upload'); ?></label>
			</div>
		</form>

		<!-- Progress bar -->
		<div class="progress mt-3">
			<div id="jsbluditProgressBar" class="progress-bar bg-primary" role="progressbar" style="width:0%"></div>
		</div>

	<!--
		IMAGES LIST
	-->
		<!-- Table for list files -->
		<table id="jsbluditMediaTable" class="table mt-3">
			<tr>
				<td><?php $L->p('There are no images'); ?></td>
			</tr>
		</table>

		<!-- Paginator -->
		<nav id="jsbluditMediaTablePagination"></nav>

	</div>
</div>
</div>
</div>
</div>
</div>

<script>

<?php
echo 'var preLoadFiles = '.json_encode($preLoadFiles).';';
?>

function openMediaManager() {
	$('#jsmediaManagerModal').modal('show');
}

function closeMediaManager() {
	$('#jsmediaManagerModal').modal('hide');
}

// Remove all files from the table
function cleanTable() {
	$('#jsbluditMediaTable').empty();
}

function showMediaAlert(message) {
	$("#jsalertMedia").html(message).removeClass('d-none');
}

function hideMediaAlert() {
	$("#jsalertMedia").addClass('d-none');
}

// Show the files in the table
function displayFiles(files, numberOfPages = <?= $numberOfPages ?>) {
	if (!Array.isArray(files)) {
		return false;
	}

	// Clean table
	cleanTable();

	// Regenerate the table
	if (files.length > 0) {
		$.each(files, function(key, filename) {
			var thumbnail = "<?php echo PAGE_THUMBNAILS_URL; ?>"+filename;
			var image = "<?php echo PAGE_IMAGES_URL; ?>"+filename;

			tableRow = '<tr id="js'+filename+'">'+
					'<td style="width:80px"><img class="img-thumbnail" alt="200x200" src="'+thumbnail+'" style="width: 50px; height: 50px;"><\/td>'+
					'<td class="information">'+
						'<div class="text-secondary pb-2">'+filename+'<\/div>'+
						'<div>'+
							'<a href="#" class="mr-3 text-primary" onClick="editorInsertMedia(\''+image+'\'); closeMediaManager();"><i class="fa fa-plus-circle"></i><?php $L->p('Insert') ?><\/a>'+
							'<a href="#" class="mr-3 text-primary" onClick="editorInsertMedia(\''+thumbnail+'\'); closeMediaManager();"><i class="fa fa-image"></i><?php $L->p('Insert thumbnail') ?><\/a>'+
							'<a href="#" class="mr-3 text-primary" onClick="editorInsertLinkedMedia(\''+thumbnail+'\',\''+image+'\'); closeMediaManager();"><i class="fa fa-link"></i><?php $L->p('Insert linked thumbnail') ?><\/a>'+
							'<a href="#" class="text-primary" onClick="setCoverImage(\''+filename+'\'); closeMediaManager();"><i class="fa fa-desktop"></i><?php $L->p('Set as cover image') ?><\/button>'+
							'<a href="#" class="float-right text-danger" onClick="deleteMedia(\''+filename+'\')"><i class="fa fa-trash-o"></i><?php $L->p('Delete') ?><\/a>'+
						'<\/div>'+
					'<\/td>'+
				'<\/tr>';
			$('#jsbluditMediaTable').append(tableRow);
		});

		mediaPagination = '<ul class="pagination justify-content-center flex-wrap">';
		for (var i = 1; i <= numberOfPages; i++) {
			mediaPagination += '<li class="page-item"><button type="button" class="btn btn-link page-link" onClick="getFiles('+i+')">'+i+'</button></li>';
		}
		mediaPagination += '</ul>';
		$('#jsbluditMediaTablePagination').html(mediaPagination);

	}

	if (files.length == 0) {
		$('#jsbluditMediaTable').html("<p><?php (IMAGE_RESTRICT ? $L->p('There are no images for the page') : $L->p('There are no images')) ?></p>");
		$('#jsbluditMediaTablePagination').html('');
	}
}

// Get the list of files via AJAX, filter by the page number
function getFiles(pageNumber) {
	$.post(HTML_PATH_ADMIN_ROOT+"ajax/list-images",
		{ 	tokenCSRF: tokenCSRF,
			pageNumber: pageNumber,
			uuid: "<?php echo PAGE_IMAGES_KEY ?>",
			path: "thumbnails" // the paths are defined in ajax/list-images
		},
		function(data) { // success function
			if (data.status==0) {
				displayFiles(data.files, data.numberOfPages);
			} else {
				console.log(data.message);
			}
		}
	);
}

// Delete the file and the thumbnail if exist
function deleteMedia(filename) {
	$.post(HTML_PATH_ADMIN_ROOT+"ajax/delete-image",
		{ 	tokenCSRF: tokenCSRF,
			filename: filename,
			uuid: "<?php echo PAGE_IMAGES_KEY; ?>"
		},
		function(data) { // success function
			if (data.status==0) {
				getFiles(1);
			} else {
				console.log(data.message);
			}
		}
	);
}

function setCoverImage(filename) {
	var image = "<?php echo PAGE_IMAGES_URL; ?>"+filename;
	$("#jscoverImage").val(filename);
	$("#jscoverImagePreview").attr("src", image);
}

function uploadImages() {
	// Remove current alerts
	hideMediaAlert();

	var images = $("#jsimages")[0].files;
	for (var i=0; i < images.length; i++) {
		// Check file type/extension
		const validImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
		if (!validImageTypes.includes(images[i].type)) {
			showMediaAlert("<?php echo $L->g('File type is not supported. Allowed types:').' '.implode(', ',$GLOBALS['ALLOWED_IMG_EXTENSION']) ?>");
			return false;
		}

		// Check file size and compare with PHP upload_max_filesize
		if (images[i].size > UPLOAD_MAX_FILESIZE) {
			showMediaAlert("<?php echo $L->g('Maximum load file size allowed:').' '.ini_get('upload_max_filesize') ?>");
			return false;
		}
	};

	// Clean progress bar
	$("#jsbluditProgressBar").removeClass().addClass("progress-bar bg-primary");
	$("#jsbluditProgressBar").width("0");

	// Data to send via AJAX
	var formData = new FormData($("#jsbluditFormUpload")[0]);
	formData.append("uuid", "<?php echo PAGE_IMAGES_KEY ?>");
	formData.append("tokenCSRF", tokenCSRF);

	$.ajax({
		url: HTML_PATH_ADMIN_ROOT+"ajax/upload-images",
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		xhr: function() {
			var xhr = $.ajaxSettings.xhr();
			if (xhr.upload) {
				xhr.upload.addEventListener("progress", function(e) {
					if (e.lengthComputable) {
						var percentComplete = (e.loaded / e.total)*100;
						$("#jsbluditProgressBar").width(percentComplete+"%");
					}
				}, false);
			}
			return xhr;
		}
	}).done(function(data) {
		if (data.status==0) {
			$("#jsbluditProgressBar").removeClass("bg-primary").addClass("bg-success");
			// Get the files for the first page, this include the files uploaded
			getFiles(1);
		} else {
			$("#jsbluditProgressBar").removeClass("bg-primary").addClass("bg-danger");
			showMediaAlert(data.message);
		}
	});
}

$(document).ready(function() {
	// Display the files preloaded for the first time
	displayFiles(preLoadFiles);

	// Select image event
	$("#jsimages").on("change", function(e) {
		uploadImages();
	});

	// Drag and drop image
	$(window).on("dragover dragenter", function(e) {
		e.preventDefault();
		e.stopPropagation();
		openMediaManager();
	});

	// Drag and drop image
	$(window).on("drop", function(e) {
		e.preventDefault();
		e.stopPropagation();
		$("#jsimages").prop("files", e.originalEvent.dataTransfer.files);
		uploadImages();
	});
});

</script>
