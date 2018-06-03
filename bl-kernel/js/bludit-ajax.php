class bluditAjax {

	// Autosave works only when the content has more than 100 characters
	// callBack function need to be showAlert() is the function to display an alert defined in alert.php
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
			url: "<?php echo HTML_PATH_ADMIN_ROOT ?>ajax/save-as-draft"
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
		    url: "<?php echo HTML_PATH_ADMIN_ROOT.'ajax/generate-slug' ?>"
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
