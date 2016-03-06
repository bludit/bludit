<script>

function insertTag() {

	var newTag = $("#jstagInput").val();

	if(newTag.trim()=="") {
		return true;
	}

	var findTag = $("span[data-tag]").filter(function() {
		return $(this).attr('data-tag').toLowerCase() == newTag.toLowerCase();
	});

	if( findTag.length > 0 ) {
		findTag.removeClass("unselect").addClass("select");
	}
	else {
		$("#jstagList").append("<span data-tag=\""+newTag+"\" class=\"select\">"+newTag+"</span>");
	}

	// Clean the input.
	$("#jstagInput").val("");

}

$(document).ready(function() {

	// Click on tag unselected.
	$(document).on("click", ".unselect", function() {
		$(this).removeClass("unselect").addClass("select");
	});

	// Click on tag selected.
	$(document).on("click", ".select", function() {
		$(this).removeClass("select").addClass("unselect");
	});

	// Insert tag when click on the button "add".
	$(document).on("click", "#jstagAdd", function(e) {

		// Prevent forum submit.
		e.preventDefault();

		insertTag();

	});

	// Insert tag when press enter key.
	$("#jstagInput").keypress(function(e) {

		if(e.which == 13) {
			insertTag();
		}

	});

	// Before form submit.
	$("form").submit(function(e) {

		// For each span.select make an array then implode with comma glue.
		var list = $("#jstagList > span.select").map(function() {
			return $(this).html();
		}).get().join(",");

		// Insert the tags separated by comma in the input hiden field.
		$("#jstags").val( list );

	});

});

</script>