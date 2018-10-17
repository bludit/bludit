class bluditAjax {

	// Autosave works only when the content has more than 100 characters
	// callBack function need to be showAlert(), this function is for display alerts to the user, defined in alert.php
	autosave(uuid, title, content, callBack) {
		var ajaxRequest;
		if (ajaxRequest) {
			ajaxRequest.abort();
		}

		if (content.length<100) {
			return false;
		}

		ajaxRequest = $.ajax({
			type: "POST",
			data: {
				tokenCSRF: tokenCSRF, // token from env variables
				uuid: uuid,
				title: title,
				content: content
			},
			url: HTML_PATH_ADMIN_ROOT+"ajax/save-as-draft"
		});

		ajaxRequest.done(function (response, textStatus, jqXHR) {
			console.log("Bludit AJAX: autosave(): done handler");
			callBack("Autosave success");
		});

		ajaxRequest.fail(function (jqXHR, textStatus, errorThrown) {
			console.log("Bludit AJAX: autosave(): fail handler");
			callBack("Autosave failure");
		});

		ajaxRequest.always(function () {
			console.log("Bludit AJAX: autosave(): always handler");
		});
	}

	// Alert the user when the user is not logged
	userLogged(callBack) {
		var ajaxRequest;
		if (ajaxRequest) {
			ajaxRequest.abort();
		}

		console.log("[INFO] [BLUDIT AJAX] [userLogged()] Checking if the user is logged.");

		ajaxRequest = $.ajax({
			type: "GET",
			url: HTML_PATH_ADMIN_ROOT+"ajax/user-logged"
		});

		ajaxRequest.done(function (response, textStatus, jqXHR) {
			console.log("[INFO] [BLUDIT AJAX] [userLogged()] The user is logged.");
		});

		ajaxRequest.fail(function (jqXHR, textStatus, errorThrown) {
			// The fail is produced by admin.php when the user is not logged the ajax request is not possible and returns 401
			console.log("[INFO] [BLUDIT AJAX] [userLogged()] The user is NOT logged.");
			if (jqXHR.status==401) {
				callBack("You are not logged in anymore, so Bludit can't save your settings and content.");
			}
		});
	}

	generateSlug(text, parentKey, currentKey, callBack) {
		var ajaxRequest;
		if (ajaxRequest) {
			ajaxRequest.abort();
		}

		ajaxRequest = $.ajax({
		    type: "POST",
		    data: {
			tokenCSRF: tokenCSRF,
			text: text,
			parentKey: parentKey,
			currentKey: currentKey
		    },
		    url: HTML_PATH_ADMIN_ROOT+"ajax/generate-slug"
		});

		ajaxRequest.done(function (response, textStatus, jqXHR) {
			console.log("Bludit AJAX: generateSlug(): done handler");
			callBack.val(response["slug"]);
		});

		ajaxRequest.fail(function (jqXHR, textStatus, errorThrown) {
			console.log("Bludit AJAX: generateSlug(): fail handler");
		});

		ajaxRequest.always(function () {
			console.log("Bludit AJAX: generateSlug(): always handler");
		});
	    }

}
