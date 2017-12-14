<script>

var filemenuV8 = new function() {

	this.filenameSelected = null;

	this.getFilename = function() {
		return this.filenameSelected;
	}

	this.setFilename = function(filename) {
		this.filenameSelected = filename;
	}

	this.hideMenu = function() {

		// Check if the menu is visible.
		if($("#bludit-filemenuV8").is(":visible")) {

			// Hide the menu.
			$("#bludit-filemenuV8").hide();

			// Clean thumbnail borders.
			$(".bludit-file-item").removeClass("bludit-border-active");

		}

	}

	this.showMenu = function(filenameSelected, positonX, positonY) {

		// Store the image selected.
		this.setFilename( filenameSelected );

		console.log("File selected: " + this.getFilename());

		// Position the menu v8.
		$("#bludit-filemenuV8").css({
			left: positonX + "px",
			top: positonY + "px"
		});

		// Show the menu v8.
		$("#bludit-filemenuV8").show();

	}

}

// This function is the default to add the image to the textarea.
// Only call when the textarea doesn't have a HTML Editor enabled.
function editorAddLinkDefault(filename) {

	var textarea = $("#jscontent");
	var imgHTML = '<a href="'+HTML_PATH_UPLOADS+filename+'" alt="">'+filename+"</a>";

	textarea.val(textarea.val() + imgHTML);

}

$(document).ready(function() {

	// Click on document.
	$(document).bind("click", function(e) {

		// Deny hide if the click is over the thumbnail.
		if($(e.target).is("div.bludit-file-item")) {
			return false;
		}

		// Hide the menu.
		filemenuV8.hideMenu(e);

	});

	// Click over thumbnail.
	$("body").on("click", "div.bludit-file-item", function(e) {

		console.log("File item click");

		// Clean all thumbnail borders.
		$(".bludit-file-item").removeClass("bludit-border-active");

		// Thumbnail selected.
		var thumbnail = $(this);

		// Add border to the thumbnail selected.
		thumbnail.addClass("bludit-border-active");

		// Filename of the selected image.
		var filenameSelected = thumbnail.attr("data-filename");

		// Show menu in position X and Y of the mouse.
		filemenuV8.showMenu( filenameSelected, e.pageX, e.pageY );

	});

	// Insert file link
	$("body").on("click", "#bludit-filemenuV8-insert", function(e) {

		if(typeof editorAddLink == 'function') {
			// This function is defined in each editor plugin.
			editorAddLink( filemenuV8.getFilename() );
		}
		else {
			editorAddLinkDefault( filemenuV8.getFilename() );
		}

	});

	// Delete file
	$("body").on("click", "#bludit-filemenuV8-delete", function(e) {

		var filenameSelected = filemenuV8.getFilename();

		if(filenameSelected==null) {
			return false;
		}

		ajaxRequest = $.ajax({
			type: "POST",
			data:{ tokenCSRF: tokenCSRF, filename: filenameSelected },
			url: "<?php echo HTML_PATH_ADMIN_ROOT.'ajax/delete-file' ?>"
		});

		// Callback handler that will be called on success
		ajaxRequest.done(function (response, textStatus, jqXHR){

			// Remove the thumbnail from Images v8
			filesV8.removeThumbnail( filenameSelected );

			// Remove the thumbnail from Quick Images
			quickFiles.removeThumbnail( filenameSelected );

			console.log("Delete file: AJAX request done, message: "+response["msg"]);
		});

		// Callback handler that will be called on failure
		ajaxRequest.fail(function (jqXHR, textStatus, errorThrown){
			console.log("Delete file: AJAX request fail");
		});

		// Callback handler that will be called regardless
		// if the request failed or succeeded
		ajaxRequest.always(function () {
			console.log("Delete file: AJAX request always");
		});

	});

});

</script>