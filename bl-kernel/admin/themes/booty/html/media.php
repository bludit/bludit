<?php
// Preload the first 10 files to not call via AJAX when the user open the first time the media manager
if (IMAGE_RESTRICT) {
	$imagesDirectory = (IMAGE_RELATIVE_TO_ABSOLUTE? '' : HTML_PATH_UPLOADS_PAGES.$uuid.DS);
	$thumbnailDirectory = PATH_UPLOADS_PAGES.$uuid.DS.'thumbnails'.DS;
	$thumbnailHTML = HTML_PATH_UPLOADS_PAGES.$uuid.'/thumbnails/';
} else {
	$imagesDirectory = (IMAGE_RELATIVE_TO_ABSOLUTE? '' : HTML_PATH_UPLOADS);
	$thumbnailDirectory = PATH_UPLOADS_THUMBNAILS;
	$thumbnailHTML = HTML_PATH_UPLOADS_THUMBNAILS;
}
$listOfFilesByPage = Filesystem::listFiles($thumbnailDirectory, '*', '*', $GLOBALS['MEDIA_MANAGER_SORT_BY_DATE'], $GLOBALS['MEDIA_MANAGER_NUMBER_OF_FILES']);
$preLoadFiles = array();
if (!empty($listOfFilesByPage[0])) {
	foreach ($listOfFilesByPage[0] as $file) {
		$filename = basename($file);
		array_push($preLoadFiles, $filename);
	}
}
// Amount of pages for the paginator
$numberOfPages = count($listOfFilesByPage);
?>

<div id="jsbluditMediaModal" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="container-fluid">
<div class="row">
	<div class="col p-3">

	<!--
		UPLOAD INPUT
	-->
		<h3 class="mt-2 mb-3"><?php $L->p('Upload'); ?></h3>

		<!-- Form and Input file -->
		<form name="bluditFormUpload" id="jsbluditFormUpload" enctype="multipart/form-data">
			<div class="custom-file">
				<input type="file" class="custom-file-input" id="jsbluditInputFiles" name="bluditInputFiles[]" multiple>
				<label class="custom-file-label" for="jsbluditInputFiles"><?php $L->p('Choose images to upload'); ?></label>
			</div>
		</form>

		<!-- Progress bar -->
		<div class="progress mt-2">
			<div id="jsbluditProgressBar" class="progress-bar bg-info" role="progressbar" style="width:0%"></div>
		</div>

	<!--
		MANAGER
	-->
		<h3 class="mt-4 mb-3"><?php $L->p('Manage'); ?></h3>

		<!-- Table for list files -->
		<table id="jsbluditMediaTable" class="table">
			<tr>
				<td><?php $L->p('There are no images'); ?></td>
			</tr>
		</table>

		<!-- Paginator -->
		<nav>
			<ul class="pagination justify-content-center">
				<?php for ($i=1; $i<=$numberOfPages; $i++): ?>
				<li class="page-item"><button type="button" class="btn btn-link page-link" onClick="getFiles(<?php echo $i ?>)"><?php echo $i ?></button></li>
				<?php endfor; ?>
			</ul>
		</nav>

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
	$('#jsbluditMediaModal').modal('show');
}

function closeMediaManager() {
	$('#jsbluditMediaModal').modal('hide');
}

// Remove all files from the table
function cleanFiles() {
	$('#jsbluditMediaTable').empty();
}

// Show the files in the table
function displayFiles(files) {
	// Clean table
	cleanFiles();
	// Regenerate the table
	$.each(files, function(key, filename) {
		var thumbnail = "<?php echo $thumbnailHTML; ?>"+filename;
		var path = "<?php echo $imagesDirectory; ?>";

		tableRow = '<tr id="js'+filename+'">'+
				'<td style="width:80px"><img class="img-thumbnail" alt="200x200" src="'+thumbnail+'" style="width: 50px; height: 50px;"><\/td>'+
				'<td class="information">'+
					'<div class="pb-2">'+filename+'<\/div>'+
					'<div>'+
						'<button type="button" class="btn btn-primary btn-sm mr-2" onClick="editorInsertMedia(\''+path+filename+'\'); closeMediaManager();"><?php $L->p('Insert') ?><\/button>'+
						'<button type="button" class="btn btn-primary btn-sm" onClick="setCoverImage(\''+filename+'\'); closeMediaManager();"><?php $L->p('Set as cover image') ?><\/button>'+
						'<button type="button" class="btn btn-secondary btn-sm float-right" onClick="deleteMedia(\''+filename+'\')"><?php $L->p('Delete') ?><\/button>'+
					'<\/div>'+
				'<\/td>'+
			'<\/tr>';
		$('#jsbluditMediaTable').append(tableRow);
	});
}

// Get the list of files via AJAX, filter by the page number
function getFiles(pageNumber) {
	$.post(HTML_PATH_ADMIN_ROOT+"ajax/list-images",
		{ 	tokenCSRF: tokenCSRF,
			pageNumber: pageNumber,
			uuid: "<?php echo $uuid; ?>",
			path: "thumbnails" // the paths are defined in the list-images.php
		},
		function(data) {
			displayFiles(data.files);
		}
	);
}

// Delete the file and the thumbnail if exist
function deleteMedia(filename) {
	$.post(HTML_PATH_ADMIN_ROOT+"ajax/delete-file",
		{ 	tokenCSRF: tokenCSRF,
			filename: filename
		},
		function(data) {
			getFiles(1);
		}
	);
}

function setCoverImage(filename) {
	$("#jscoverImage").val(filename);
	$("#jscoverImagePreview").attr("src", HTML_PATH_UPLOADS_THUMBNAILS+filename);
}

$(document).ready(function() {
	// Display the files preloaded for the first time
	displayFiles(preLoadFiles);

	// Event to wait the selected files
	$("#jsbluditInputFiles").on("change", function() {

		// Check file size ?
		// Check file type/extension ?

		$("#jsbluditProgressBar").width("1%");

		// Data to send via AJAX
		var uuid = $("#jsuuid").val();
		var formData = new FormData($("#jsbluditFormUpload")[0]);
		formData.append('uuid', uuid);
		formData.append('tokenCSRF', tokenCSRF);

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
		}).done(function() {
			// Get the files of the first page, this include the files uploaded
			getFiles(1);
		});
	});
});

</script>
