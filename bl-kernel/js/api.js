/*
	Javascript wrapper for the Bludit API
*/

class API {

	constructor(apiURL, apiToken, apiAuth) {
		this.apiURL = apiURL;
		this.body = {
			token: apiToken,
			authentication: apiAuth
		}
	}

	async getPage(args) {
		var url = this.apiURL + "pages/" + args['pageKey'];
		var parameters = "?token=" + this.body.token + "&authentication=" + this.body.authentication;
		try {
			const response = await fetch(url + parameters, {
				method: "GET"
			});
			var json = await response.json();
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Save page fields

		@args['pageKey']		string		Page key from the page to edit
		@args					array		Arguments can be any of the fields from a page
		@return				string		New page key
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Generates unique slug text for the a page

		@args['pageKey']		string		Page key for the page to generate the slug url
		@args['text']			string		Text that you want to generate the slug url
		@args['parentKey']		string		Parent page key if the page has one, if not empty string
		@return				string		Slug text
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Get all files uploaded for the page

		@args				string
		@return				array
	*/
	async getPageFiles(args) {
		var url = this.apiURL + "pages/files/" + args['pageKey'];
		var parameters = "?token=" + this.body.token + "&authentication=" + this.body.authentication;
		try {
			const response = await fetch(url + parameters, {
				method: "GET"
			});
			var json = await response.json();
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Save settings

		@args				array		Arguments can be any of the fields from settings
		@return				array
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Create a new category

		@args				array		Arguments can be any of the fields from a category
		@return				string		New category key
	*/
	async createCategory(args) {
		var url = this.apiURL + "categories";
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Edit a category

		@args				array		Arguments can be any of the fields from a category
		@return				string		The category key
	*/
	async editCategory(args) {
		var url = this.apiURL + "categories/" + args['key'];
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Delete a category

		@args				array		Array => (key: string)
		@return				string		The category key deleted
	*/
	async deleteCategory(args) {
		var url = this.apiURL + "categories/" + args['key'];
		var body = this.body;
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "DELETE",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Delete a page

		@args				array		Array => (key: string)
		@return				string		The page key deleted
	*/
	async deletePage(args) {
		var url = this.apiURL + "pages/" + args['key'];
		var body = this.body;
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "DELETE",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Delete the profile picture from a user

		@args				array		Array => (username: string)
		@return				string		The username
	*/
	async deleteProfilePicture(args) {
		var url = this.apiURL + "users/picture/" + args['username'];
		var body = this.body;
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "DELETE",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Delete the site logo
	*/
	async deleteSiteLogo() {
		var url = this.apiURL + "settings/logo"
		var body = this.body;
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "DELETE",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Create a new user

		@args				array		Arguments can be any of the fields from a user
		@return				string		Returns the username created
	*/
	async createUser(args) {
		var url = this.apiURL + "users";
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Edit an user

		@args				array		Arguments can be any of the fields from an user
		@return				string		The username
	*/
	async editUser(args) {
		var url = this.apiURL + "users/" + args['username'];
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Install and activate a plugin === Bludit v4

		@args				array
		@return				string
	*/
	async activatePlugin(args) {
		var url = this.apiURL + "plugins/" + args['className'];
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Deactivate and uninstall a plugin === Bludit v4

		@args				array
		@return				string
	*/
	async deactivatePlugin(args) {
		var url = this.apiURL + "plugins/" + args['className'];
		var body = Object.assign({}, this.body, args);
		try {
			var response = await fetch(url, {
				credentials: "same-origin",
				method: "DELETE",
				body: JSON.stringify(body),
				headers: new Headers({
					"Content-Type": "application/json"
				})
			});
			var json = await response.json();
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

	/*	Configure a plugin

		@args				array		Arguments can be any of the fields from the plugin database
		@return				string		The plugin class name
	*/
	async configurePlugin(args) {
		var url = this.apiURL + "plugins/" + args['className'];
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
			return json;
		} catch (err) {
			console.log(response);
			console.log(err);
			return {'message': 'Error from API. Open the inspector from the browser for more details.'};
		}
	}

}