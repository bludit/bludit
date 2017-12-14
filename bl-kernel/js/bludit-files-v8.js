<script>

var filesV8 = new function() {

	this.addThumbnail = function(filename) {

		// Add the new thumbnail to Bludit Files v8
		$("#bludit-files-v8-items").prepend("<div class=\"bludit-file-item\" data-filename=\""+filename+"\" >"+filename+"</div>");

	}

	this.removeThumbnail = function(filename) {

		// Remove the thumbnail
		$("#bludit-files-v8-items > div[data-filename=\""+filename+"\"]").remove();

		if($("#bludit-files-v8-items > div").length == 0) {
			// Show box "There are no images"
			$(".empty-files").show();
		}
	}

}

$(document).ready(function() {

	var files_settings =
	{
		type: "json",
		action: HTML_PATH_ADMIN_ROOT+"ajax/uploader",
		allow : "*.("+ALLOWED_EXTENSIONS_FILES+")",
		params: {"tokenCSRF":tokenCSRF, "type":"bludit-files-v8"},

		loadstart: function() {
			$("#bludit-files-v8-progressbar").find(".uk-progress-bar").css("width", "0%").text("0%");
			$("#bludit-files-v8-drag-drop").hide();
			$("#bludit-files-v8-progressbar").show();
		},

		progress: function(percent) {
			percent = Math.ceil(percent);
			$("#bludit-files-v8-progressbar").find(".uk-progress-bar").css("width", percent+"%").text(percent+"%");
		},

		allcomplete: function(response) {
			$("#bludit-files-v8-progressbar").find(".uk-progress-bar").css("width", "100%").text("100%");
			$("#bludit-files-v8-progressbar").hide();
			$("#bludit-files-v8-drag-drop").show();
			$(".empty-files").hide();

			// Add thumbnail to Bludit Files V8
      //TODOME
			filesV8.addThumbnail( response.filename );

			// Add thumbnail to Quick Files
      //TODOME
			quickFiles.addThumbnail( response.filename );
		},

		notallowed: function(file, files_settings) {
			alert("<?php echo $L->g('error').'. '.$L->g('Supported file types')?>: "+files_settings.allow);
		}
	};

	UIkit.uploadSelect($("#bludit-files-v8-file-select"), files_settings);
	UIkit.uploadDrop($("#bludit-files-v8-upload"), files_settings);
});
</script>
