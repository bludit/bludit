
function getLatestVersion() {

	$("#current-version").show();

	$.ajax({
		url: "https://version.bludit.com",
		method: "GET",
		dataType: 'json',
		success: function(json) {
			// Constant BLUDIT_BUILD is defined on functions.js
			if (json.stable.build > BLUDIT_BUILD) {
				$("#current-version").hide();
				$("#new-version").show();
			}
		},
		error: function(json) {
			console.log("Error when try to get the version.");
		}
	});
}

getLatestVersion();