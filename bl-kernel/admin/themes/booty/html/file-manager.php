<div class="modal" id="modal-fileManager" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col">

							<div class="d-flex align-items-center mb-2">
								<h3 class="me-auto m-0 p-0"><i class="bi bi-image"></i><?php $L->p('File Manager'); ?></h3>
								<label class="btn btn-primary"><i class="bi bi-upload"></i><?php $L->p('Upload file'); ?><input type="file" id="filesToUpload" name="filesToUpload[]" multiple hidden></label>
								<div class="progress d-none">
									<div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>

							<table class="table">
								<thead>
									<tr>
										<th scope="col">Preview</th>
										<th scope="col">Filename</th>
										<th scope="col">Type</th>
										<th scope="col">Size</th>
										<th scope="col"></th>
									</tr>
								</thead>
								<tbody id="fmFiles">
									<tr>
										<td class="align-middle">
											<img style="width: 32px" src="<?php echo HTML_PATH_CORE_IMG ?>default.svg" />
										</td>
										<td class="align-middle">photo.jpg</td>
										<td class="align-middle">image/jpeg</td>
										<td class="align-middle">300Kb</td>
										<td class="align-middle">
											<i class="bi bi-trash"></i><span><?php $L->p('Delete') ?></span>
										</td>
									</tr>
								</tbody>
							</table>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	// Open File Manager modal
	function fmOpen() {
		$('#modal-fileManager').modal('show');
	}

	// Get files for the current page and show them
	function fmGetFiles() {
		logs('File Manager. Get files for page: ' + _pageKey);
		api.getPageFiles({
			'pageKey': _pageKey
		}).then(function(files) {
			fmDisplayFiles(files);
		});
	}

	// Show the files in the table
	function fmDisplayFiles(files) {
		$('#fmFiles').empty();

		if (files.length == 0) {
			logs('File Manager. File list empty.');
			return false;
		}

		$.each(files, function(key, file) {
			var row = '<tr>' +
				'						<td class="align-middle">' +
				'							<img style="width: 32px" src="<?php echo HTML_PATH_CORE_IMG ?>default.svg" />' +
				'						</td>' +
				'						<td class="align-middle">' + file.filename + '</td>' +
				'						<td class="align-middle">' + file.mime + '</td>' +
				'						<td class="align-middle">' + formatBytes(file.size) + '</td>' +
				'						<td class="align-middle">' +
				'							<i class="bi bi-trash"></i><span><?php $L->p('Delete') ?></span>' +
				'						</td>' +
				'					</tr>';

			$('#fmFiles').append(row);
		});

		return true;
	}

	// Upload a file for the current page
	function fmUploadFile(file) {
		logs('File Manager. Uploading file.');

		// Check file type/extension
		const validImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/svg+xml'];
		if (!validImageTypes.includes(file.type)) {
			logs("File Manager. File type is not supported.");
			showAlert("<?php echo $L->g('File type is not supported. Allowed types:') . ' ' . implode(', ', $GLOBALS['ALLOWED_IMG_EXTENSION']) ?>");
			return false;
		}

		// Check file size and compare with PHP upload_max_filesize
		if (file.size > UPLOAD_MAX_FILESIZE) {
			logs("File Manager. File size to big for PHP configuration.");
			showAlert("<?php echo $L->g('Maximum load file size allowed:') . ' ' . ini_get('upload_max_filesize') ?>");
			return false;
		}

		// Data to send via AJAX
		var formData = new FormData();
		formData.append("file", file);
		formData.append("token", api.body.token);
		formData.append("authentication", api.body.authentication);

		$.ajax({
			url: api.apiURL + 'files/' + _pageKey,
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
							var percentComplete = (e.loaded / e.total) * 100;
							logs("File Manager. Uploading file, percent complete: " + percentComplete + "%");
						}
					}, false);
				}
				return xhr;
			}
		}).done(function(data) {
			logs("File Manager. File uploaded.")
			logs(data);
			fmGetFiles();
		});
	}

	$(document).ready(function() {

		// Input file change event
		$("#filesToUpload").on("change", function(e) {
			var filesToUpload = $("#filesToUpload")[0].files;
			for (var i = 0; i < filesToUpload.length; i++) {
				fmUploadFile(filesToUpload[i]);
			}
		});

	});
</script>