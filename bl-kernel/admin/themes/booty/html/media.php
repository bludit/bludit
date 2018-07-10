<?php
// Preload the first 10 files to not call via AJAX when the user open the first time the media manager
$listOfFiles = Filesystem::listFiles(PATH_UPLOADS_THUMBNAILS, '*', '*', $GLOBALS['BLUDIT_MEDIA_MANAGER_SORT_BY_DATE'], false);
$listOfFilesByPage = array_chunk($listOfFiles, $GLOBALS['BLUDIT_MEDIA_MANAGER_AMOUNT_OF_FILES']);
$preLoadFiles = array();
if (!empty($listOfFilesByPage[0])) {
	foreach ($listOfFilesByPage[0] as $file) {
		array_push($preLoadFiles, basename($file));
	}
}
// Amount of pages for the paginator
$amountOfPages = count($listOfFilesByPage);
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
		<h3 class="mt-2 mb-3">Upload</h3>

		<!-- Form and Input file -->
		<form name="bluditFormUpload" id="jsbluditFormUpload" enctype="multipart/form-data">
			<input type="hidden" name="tokenCSRF" value="<?php echo $Security->getTokenCSRF() ?>">
			<div class="custom-file">
				<input type="file" class="custom-file-input" id="jsbluditInputFiles" name="bluditInputFiles[]" multiple>
				<label class="custom-file-label" for="jsbluditInputFiles">Choose images</label>
			</div>
		</form>

		<!-- Progress bar -->
		<div class="progress mt-2">
			<div id="jsbluditProgressBar" class="progress-bar bg-info" role="progressbar" style="width:0%"></div>
		</div>

	<!--
		MANAGER
	-->
		<h3 class="mt-4 mb-3">Manage</h3>

		<!-- Table for list files -->
		<table id="jsbluditMediaTable" class="table">
			<tr>
				<td>There are not images.</td>
			</tr>
		</table>

		<!-- Paginator -->
		<nav>
			<ul class="pagination justify-content-center">
				<?php for ($i=1; $i<=$amountOfPages; $i++): ?>
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
		tableRow = '<tr id="js'+filename+'">'+
				'<td style="width:80px"><img class="img-thumbnail" alt="200x200" src="'+ HTML_PATH_UPLOADS_THUMBNAILS + filename+'" style="width: 50px; height: 50px;"></td>'+
				'<td class="information">'+
					'<div class="pb-2">'+filename+'<\/div>'+
					'<div>'+
						'<button onClick="editorInsertMedia(\''+filename+'\'); closeMediaManager();" type="button" class="btn btn-primary btn-sm mr-2">Insert<\/button>'+
						'<button onClick="setCoverImage(\''+filename+'\'); closeMediaManager();" type="button" class="btn btn-primary btn-sm mr-2">Set as cover image<\/button>'+
						'<button onClick="deleteMedia(\''+filename+'\')" type="button" class="btn btn-sm float-right">Delete<\/button>'+
					'<\/div>'+
				'<\/td>'+
			'<\/tr>';
		$('#jsbluditMediaTable').append(tableRow);
	});
}

// Get the list of files via AJAX, filter by the page number
function getFiles(pageNumber) {
	$.post(HTML_PATH_ADMIN_ROOT+"ajax/list-files",
		{ 	tokenCSRF: tokenCSRF,
			pageNumber: pageNumber,
            //ajax/list-files is changed
			path: HTML_PATH_UPLOADS_THUMBNAILS
		},
		function(data) {
			displayFiles(data.files);
	});
}

// Delete the file and the thumbnail if exist
function deleteMedia(filename) {
	$.post(HTML_PATH_ADMIN_ROOT + "ajax/delete-file",
		{ 	tokenCSRF: tokenCSRF,
			filename: filename
		},
		function(data) {
			getFiles(1);
	});
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

		$.ajax({
			url: HTML_PATH_ADMIN_ROOT+"ajax/upload-files",
			type: "POST",
			data: new FormData($("#jsbluditFormUpload")[0]),
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
