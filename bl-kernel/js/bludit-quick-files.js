<script>

var quickFiles = new function() {

	this.addThumbnail = function(filename) {

		// Remove element if there are more than 6 items
		if ($("#bludit-quick-files-items > div").length > 5) {
			$("div:last-child", "#bludit-quick-files-items").remove();
		}

		// Add the new thumbnail to Quick files
		$("#bludit-quick-files-items").prepend(
    "<div class=\"bludit-file-item\" data-filename=\""+filename+"\" >"+filename+"</div>"
    );

	}

	this.removeThumbnail = function(filename) {

		// Remove the thumbnail
		$("#bludit-quick-files-items > div[data-filename=\""+filename+"\"]").remove();

		if($("#bludit-quick-files-items > div").length == 0) {
			// Show box "There are no files"
			$(".empty-files").show();
		}

	}

}

</script>
