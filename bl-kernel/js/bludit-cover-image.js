<script>

var coverImage = new function() {

	this.set = function(filename) {

		var externalCoverImage = true;
		var imageSrc = "";

		var expression = /^https?:\/\//gi;
		var regex = new RegExp(expression);		
		if (!filename.match(regex)) {
			externalCoverImage = false;
			imageSrc += HTML_PATH_UPLOADS_THUMBNAILS;
		}
		imageSrc += filename;

		// Cover image background
		$("#cover-image-thumbnail").attr("style", "background-image: url("+imageSrc+")");

		// Form attribute
		$("#cover-image-upload-filename").attr("value", filename);

		// Show delete button.
		$("#cover-image-delete").show();

		// Hide Cover image text.
		$("#cover-image-upload").hide();

		// Hide box "There are no images"
		if (!externalCoverImage) {
			$(".empty-images").hide();
		}

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

	var coverImageTypeSelected = false;
	var modal = UIkit.modal("#bludit-cover-image-modal");
	$("#bludit-cover-image").on("click", function(e){
		if (coverImageTypeSelected) {
			coverImageTypeSelected = !coverImageTypeSelected;
			return;
		}
		if ( modal.isActive() ) {
    			modal.hide();
			} else {
				$("#external-cover-image-url").val("");
    			modal.show();
				$("#external-cover-image-url").focus();
			}	
			e.stopPropagation();
			e.preventDefault();	
		});
	// Stores the external cover image url		
	$("#jsExternalCoverImageSave").on("click", function(e){
		e.stopPropagation();
		e.preventDefault();		
		modal.hide();
		if ($("#external-cover-image-url").val().length > 0) {
			var expression = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,4}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)\/\S+\.(jpg|jpeg|gif|png)/gi;
			var regex = new RegExp(expression);
			if ($("#external-cover-image-url").val().match(regex)) {
				coverImage.set($("#external-cover-image-url").val().toLowerCase());
			}
			else {
				alert("<?php echo $L->g('error').'. '.$L->g('Invalid cover image URL')?>.");
			}
		}
	});
	// Proceed with cover image upload
	$("#jsCoverImageUpload").on("click", function(e){
		e.stopPropagation();
		e.preventDefault();				
		modal.hide();
		coverImageTypeSelected = true;
		$("#cover-image-file-select").trigger("click");
	});	

	// Click on delete cover image.
	$("#cover-image-delete").on("click", function(e) {

		// Remove the cover image.
		coverImage.remove();
		e.stopPropagation();
		
	});

	var settings =
	{
		type: "json",
		action: HTML_PATH_ADMIN_ROOT+"ajax/uploader",
		allow : "*.(jpg|jpeg|gif|png)",
		params: {"tokenCSRF":tokenCSRF, "type":"cover-image"},

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
			alert("<?php echo $L->g('error').'. '.$L->g('Supported image file types')?>: "+settings.allow);
		}
	};

	UIkit.uploadSelect($("#cover-image-file-select"), settings);
	UIkit.uploadDrop($("#cover-image-thumbnail"), settings);

});
</script>