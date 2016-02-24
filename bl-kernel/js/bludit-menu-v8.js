<script>

var menuV8 = new function() {

	this.filenameSelected = null;

	this.getFilename = function() {
		return this.filenameSelected;
	}

	this.setFilename = function(filename) {
		this.filenameSelected = filename;
	}

	this.hideMenu = function() {

		// Check if the menu is visible.
		if($("#bludit-menuV8").is(":visible")) {

			// Hide the menu.
			$("#bludit-menuV8").hide();

			// Clean thumbnail borders.
			$(".bludit-thumbnail").css("border", "1px solid #ddd");

		}

	}

	this.showMenu = function(filenameSelected, positonX, positonY) {

		// Store the image selected.
		this.setFilename( filenameSelected );

		console.log("Image selected: " + this.getFilename());

		// Position the menu v8.
		$("#bludit-menuV8").css({
			left: positonX + "px",
			top: positonY + "px"
		});

		// Show the menu v8.
		$("#bludit-menuV8").show();

	}

}

// This function is the default to add the image to the textarea.
// Only call when the textarea doesn't have a HTML Editor enabled.
function editorAddImageDefault(filename) {

	var textarea = $("#jscontent");
	var imgHTML = '<img src="'+filename+'" alt="">';

	textarea.val(textarea.val() + imgHTML);

}

$(document).ready(function() {

	// Click on document.
	$(document).bind("click", function(e) {

		// Deny hide if the click is over the thumbnail.
		if($(e.target).is("img.bludit-thumbnail")) {
			return false;
		}

		// Hide the menu.
		menuV8.hideMenu(e);

	});

	// Click over thumbnail.
	$("body").on("click", "img.bludit-thumbnail", function(e) {

		console.log("Thumbnail click");

		// Clean all thumbnail borders.
		$(".bludit-thumbnail").css("border", "1px solid #ddd");

		// Thumbnail selected.
		var thumbnail = $(this);

		// Add border to the thumbnail selected.
		thumbnail.css("border", "solid 3px orange");

		// Filename of the selected image.
		var filenameSelected = thumbnail.attr("data-filename");

		// SHow menu in position X and Y of the mouse.
		menuV8.showMenu( filenameSelected, e.pageX, e.pageY );

	});

	// Insert image
	$("body").on("click", "#bludit-menuV8-insert", function(e) {

		if(typeof editorAddImage == 'function') {
			// This function is defined in each editor plugin.
			editorAddImage( menuV8.getFilename() );
		}
		else {
			editorAddImageDefault( menuV8.getFilename() );
		}

	});

	// Set cover image
	$("body").on("click", "#bludit-menuV8-cover", function(e) {

		coverImage.set( menuV8.getFilename() );

	});

	// Delete image
	$("body").on("click", "#bludit-menuV8-delete", function(e) {

		var filenameSelected = menuV8.getFilename();

		if(filenameSelected==null) {
			return false;
		}

		ajaxRequest = $.ajax({
			type: "POST",
			data:{ filename: filenameSelected },
			url: "<?php echo HTML_PATH_ADMIN_ROOT.'ajax/delete-file' ?>"
		});

		// Callback handler that will be called on success
		ajaxRequest.done(function (response, textStatus, jqXHR){

			// Remove the thumbnail from Images v8
			imagesV8.removeThumbnail( filenameSelected );

			// Remove the thumbnail from Quick Images
			quickImages.removeThumbnail( filenameSelected );

			console.log("Delete image: AJAX request done, message: "+response["msg"]);
		});

		// Callback handler that will be called on failure
		ajaxRequest.fail(function (jqXHR, textStatus, errorThrown){
			console.log("Delete image: AJAX request fail");
		});

		// Callback handler that will be called regardless
		// if the request failed or succeeded
		ajaxRequest.always(function () {
			console.log("Delete image: AJAX request always");
		});

	});

});

</script>