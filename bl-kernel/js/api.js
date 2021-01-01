/*
	Javascript wrapper for the Bludit API
*/

class API {

	constructor(apiURL, apiToken, apiAuth) {
		this.apiURL = "http://localhost:9000/api/";
		this.body = {
			token: '45643a4071fad6a12261bb0763550feb',
			authentication: '18a8410f0043d004c2e87f404170e112'
		}
	}

	async createPage(args={}) {
		var url = this.apiURL + "pages";
		var body = Object.assign({}, this.body, args);
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "POST",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json.data.key;
		} catch (err) {
			console.log(err);
			return true;
		}
	}

	/*	Save page fields

		@args['pageKey']		string		Page key from the page to edit
		@args					array		Arguments can be any of the fields from a page
		@returns				string		New page key
	*/
	async savePage(args) {
		var url = this.apiURL + "pages/" + args['pageKey'];
		var body = Object.assign({}, this.body, args);
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "PUT",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json.data.key;
		} catch (err) {
			console.log(err);
			return true;
		}
	}

	/*	Generates unique slug text for the a page

		@args['pageKey']		string		Page key for the page to generate the slug url
		@args['text']			string		Text that you want to generate the slug url
		@args['parentKey']		string		Parent page key if the page has one, if not empty string
		@returns				string		Slug text
	*/
	async friendlyURL(args) {
		var url = this.apiURL + "helper/friendly-url/";
		var parameters = "?token=" + this.body.token + "&authentication=" + this.body.authentication;
		parameters = parameters + "&pageKey=" + args['pageKey'];
		parameters = parameters + "&text=" + args['text'];
		parameters = parameters + "&parentKey=" + args['parentKey'];
		try {
			const response = await fetch(url + parameters, {
				method: "GET"
			});
			var json = await response.json();
			return json.data;
		} catch (err) {
			console.log(err);
			return true;
		}
	}

	/*	Get all files uploaded for the page

		@args['pageKey']		string
		@returns				array
	*/
	async getPageFiles(args) {
		var url = this.apiURL + "files/" + args['pageKey'];
		var parameters = "?token=" + this.body.token + "&authentication=" + this.body.authentication;
		try {
			const response = await fetch(url + parameters, {
				method: "GET"
			});
			var json = await response.json();
			return json.data;
		} catch (err) {
			console.log(err);
			return true;
		}
	}

	/*	Upload files

		@args['pageKey']		string
		@returns				array
	*/
	async uploadPageFiles(args) {
		var url = this.apiURL + "files/" + args['pageKey'];
		var body = Object.assign({}, this.body, args);
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "POST",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json.data.key;
		} catch (err) {
			console.log(err);
			return true;
		}
	}


	/*	Save settings

		@args					array		Arguments can be any of the fields from settings
		@returns				array
	*/
	async saveSettings(args) {
		var url = this.apiURL + "settings";
		var body = Object.assign({}, this.body, args);
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "PUT",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json.data;
		} catch (err) {
			console.log(err);
			return true;
		}
	}

}