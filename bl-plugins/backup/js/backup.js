jQuery(document).ready(function($) {
	$('#backupFile').change(function() {
		var file = this.files.length >= 1? this.files[0]: null;
		if (file === null) {
			return false;
		}

		// Build Form Data
		var url = $('#jsform').attr("action") || window.location.href;
		var form = new FormData();
		form.append("tokenCSRF", $('[name="tokenCSRF"]').val());
		form.append("backupFile", file);

		// Apply Form
		$.ajax({
			url: url,
			data: form,
			type: "POST",
			dataType: "json",
			mimeType: "multipart/form-data",
			contentType: false,
			processData: false,
			error: function (jqXHR, status, error) {
				var data = jqXHR.responseJSON;
				var alert = $("<div></div>").addClass("alert alert-danger").text(data.message);

				$("#jsform .alert:not(.alert-primary)").remove();
				$("#jstokenCSRF").after(alert);
			},
			success: function (data, status, jqXHR) {
				window.location.reload();
			}
		});
	});
});
