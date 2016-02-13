<script>

var imagesV8 = new function() {

	this.addThumbnail = function(filename) {

		var imageSrc = HTML_PATH_UPLOADS_THUMBNAILS + filename;

		// Add the new thumbnail to Bludit Images v8
		$("#bludit-images-v8-thumbnails").prepend("<img class=\"bludit-thumbnail\" data-filename=\""+filename+"\" src=\""+imageSrc+"\" alt=\"Thumbnail\">");

	}

	this.removeThumbnail = function(filename) {

		// Remove the thumbnail
		$("#bludit-images-v8-thumbnails > img[data-filename=\""+filename+"\"]").remove();

		if($("#bludit-images-v8-thumbnails > img").length == 0) {
			// Show box "There are no images"
			$(".empty-images").show();
		}
	}

}

$(document).ready(function() {

	var settings =
	{
		type: "json",
		action: HTML_PATH_ADMIN_ROOT+"ajax/uploader",
		allow : "*.(jpg|jpeg|gif|png)",
		params: {"type":"bludit-images-v8"},

		loadstart: function() {
			$("#bludit-images-v8-progressbar").find(".uk-progress-bar").css("width", "0%").text("0%");
			$("#bludit-images-v8-drag-drop").hide();
			$("#bludit-images-v8-progressbar").show();
		},

		progress: function(percent) {
			percent = Math.ceil(percent);
			$("#bludit-images-v8-progressbar").find(".uk-progress-bar").css("width", percent+"%").text(percent+"%");
		},

		allcomplete: function(response) {
			$("#bludit-images-v8-progressbar").find(".uk-progress-bar").css("width", "100%").text("100%");
			$("#bludit-images-v8-progressbar").hide();
			$("#bludit-images-v8-drag-drop").show();
			$(".empty-images").hide();

			// Add thumbnail to Bludit Images V8
			imagesV8.addThumbnail( response.filename );

			// Add thumbnail to Quick Images
			quickImages.addThumbnail( response.filename );
		},

		notallowed: function(file, settings) {
			alert("'.$L->g('Supported image file types').' "+settings.allow);
		}
	};

	UIkit.uploadSelect($("#bludit-images-v8-file-select"), settings);
	UIkit.uploadDrop($("#bludit-images-v8-upload"), settings);
});
</script>