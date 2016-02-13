<script>

var coverImage = new function() {

	this.set = function(filename) {

		var imageSrc = HTML_PATH_UPLOADS_THUMBNAILS + filename;

		// Cover image background
		$("#cover-image-thumbnail").attr("style", "background-image: url("+imageSrc+")");

		// Form attribute
		$("#cover-image-upload-filename").attr("value", filename);

		// Show delete button.
		$("#cover-image-delete").show();

		// Hide Cover image text.
		$("#cover-image-upload").hide();

		// Hide box "There are no images"
		$(".empty-images").hide();

	}

	this.remove = function () {

		// Remove the filename from the form.
		$("#cover-image-upload-filename").attr("value","");

		// Remove the image from background-image.
		$("#cover-image-thumbnail").attr("style","");

		// Hide delete button.
		$("#cover-image-delete").hide();

		// Show Cover image text.
		$("#cover-image-upload").show();

	}

}

$(document).ready(function() {

	// Click on delete cover image.
	$("#cover-image-delete").on("click", function() {

		// Remove the cover image.
		coverImage.remove();

	});

	var settings =
	{
		type: "json",
		action: HTML_PATH_ADMIN_ROOT+"ajax/uploader",
		allow : "*.(jpg|jpeg|gif|png)",
		params: {"type":"cover-image"},

		loadstart: function() {
			$("#cover-image-progressbar").find(".uk-progress-bar").css("width", "0%").text("0%");
			$("#cover-image-progressbar").show();
			$("#cover-image-delete").hide();
			$("#cover-image-upload").hide();
		},

		progress: function(percent) {
			percent = Math.ceil(percent);
			$("#cover-image-progressbar").find(".uk-progress-bar").css("width", percent+"%").text(percent+"%");
		},

		allcomplete: function(response) {
			$("#cover-image-progressbar").find(".uk-progress-bar").css("width", "100%").text("100%");
			$("#cover-image-progressbar").hide();

			// Add Cover Image
			coverImage.set( response.filename );

			// Add thumbnail to Quick Images
			quickImages.addThumbnail( response.filename );

			// Add thumbnail to Bludit Images V8
			imagesV8.addThumbnail( response.filename );
		},

		notallowed: function(file, settings) {
			alert("'.$L->g('Supported image file types').' "+settings.allow);
		}
	};

	UIkit.uploadSelect($("#cover-image-file-select"), settings);
	UIkit.uploadDrop($("#cover-image-thumbnail"), settings);

});
</script>