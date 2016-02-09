<script>

var quickImages = new function() {

	this.addThumbnail = function(filename) {

		var imageSrc = HTML_PATH_UPLOADS_THUMBNAILS + filename;

		// Remove element if there are more than 6 thumbnails
		if ($("#bludit-quick-images-thumbnails > img").length > 5) {
			$("img:last-child", "#bludit-quick-images-thumbnails").remove();
		}

		// Add the new thumbnail to Quick images
		$("#bludit-quick-images-thumbnails").prepend("<img class=\"bludit-thumbnail\" data-filename=\""+filename+"\" src=\""+imageSrc+"\" alt=\"Thumbnail\">");

	}

	this.removeThumbnail = function(filename) {

		// Remove the thumbnail
		$("#bludit-quick-images-thumbnails > img[data-filename=\""+filename+"\"]").remove();

		if($("#bludit-quick-images-thumbnails > img").length == 0) {
			// Show box "There are no images"
			$(".empty-images").show();
		}

	}

}

</script>