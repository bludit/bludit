
function getLatestVersion() {

	console.log("[INFO] [PLUGIN VERSION] Getting list of versions of Bludit.");

	$.ajax({
		url: "https://version.bludit.com",
		method: "GET",
		dataType: 'json',
		success: function(json) {
			// Constant BLUDIT_BUILD is defined on variables.js
			if (json.stable.build > BLUDIT_BUILD) {
				//$("#current-version").hide(); //Uncomment if you want to hide the current version when a new version is available, but this is unhelpful.
				$("#new-version").show();
			}
		},
		error: function(json) {
			console.log("[WARN] [PLUGIN VERSION] There is some issue to get the version status.");
		}
	});
}

getLatestVersion();