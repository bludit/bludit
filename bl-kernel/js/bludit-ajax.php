/*
	DEPRECATED CLASS
	WILL BE REMOVED IN BLUDIT V4
*/

class bluditAjax {

	constructor(apiURL, apiToken, apiAuth, tokenCSRF) {
		this.apiURL = "http://localhost:9000/api/";
		this.apiToken = '45643a4071fad6a12261bb0763550feb';
		this.apiAuth = '18a8410f0043d004c2e87f404170e112';
		this.tokenCSRF = tokenCSRF;
	}

	static async savePage(uuid, title, content) {
		let url = this.apiURL+"pages";
		try {
			const response = await fetch(url, {
				credentials: "same-origin",
				method: "POST",
				body: JSON.stringify({
					tokenCSRF: this.tokenCSRF,
					token: this.apiToken,
					authentication: this.apiAuth,
					uuid: uuid,
					title: title,
					content: content
				}),
				headers: new Headers({
					"Content-Type": "application/json"
				}),
			});
			const json = await response.json();
			return json.data.key;
		}
		catch (err) {
			console.log(err);
			return true;
		}
	}

	static async saveAsDraft(uuid, title, content) {
		let url = HTML_PATH_ADMIN_ROOT+"ajax/save-as-draft"
		try {
			const response = await fetch(url, {
				credentials: 'same-origin',
				method: "POST",
				headers: new Headers({
					'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
				}),
				body: new URLSearchParams({
					'tokenCSRF': tokenCSRF,
					'uuid': "autosave-" + uuid,
					'title': title,
					'content': content,
					'type': 'autosave'
				}),
			});
			const json = await response.json();
			return json;
		}
		catch (err) {
			console.log(err);
			return true;
		}
	}

	static async removeLogo() {
		let url = HTML_PATH_ADMIN_ROOT+"ajax/logo-remove"
		try {
			const response = await fetch(url, {
				credentials: 'same-origin',
				method: "POST",
				headers: new Headers({
					'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
				}),
				body: new URLSearchParams({
					'tokenCSRF': tokenCSRF
				}),
			});
			const json = await response.json();
			return json;
		}
		catch (err) {
			console.log(err);
			return true;
		}
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
