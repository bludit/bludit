<textarea id="editor"></textarea>

<script>
// Returns an array with tags
// The tags are parser from hash-tags
// text = Hello this is a #test of #the function
// returns ['test', 'the']
function getTags(text) {
	var rgx = /#(\w+)\b/gi;
	var tag;
	var tags = [];
	while (tag = rgx.exec(text)){
		tags.push(tag[1])
	}
	// tags is an array, implode with comma ,
	return tags.join(",");
}

// Returns all characters after the hash # and space
// Onlt the first match
// text = # Hello World
// returns "Hello World"
function getTitle(text) {
	var rgx = /# (.*)/;
	title = rgx.exec(text);
	return title[1].trim();
}

function getContent(text, title, tags) {
	var content = "";
	// Remove the title. # Title
	content = text.replace("# "+title+"\n", "");
	return content;
}

function getPages() {
	var params = {token: '790f6f150492ebe24c6197f53ff10010'}
	apiUrl.search = new URLSearchParams(params)
	fetch(apiUrl, {
		method: 'GET'
	})
	.then(function(response) {
		return response.json();
	})
	.then(function(json) {
		return json;
	})
	.catch(err => {
		console.log(err);
	});
}

function createPage() {
	return fetch(apiUrl, {
		credentials: 'same-origin',
		method: 'POST',
		body: JSON.stringify({
			token: "790f6f150492ebe24c6197f53ff10010",
			authentication: "cb75be4a34ce9222914c0555f6faaa8d"
		}),
		headers: new Headers({
			'Content-Type': 'application/json'
		}),
	})
	.then(function(response) {
		return response.json();
	})
	.then(function(json) {
		key = json.data.key;
	})
	.catch(err => {
		console.log(err);
	});
}

function updatePage() {
	var finalUrl = apiUrl+'/'+key;
	return fetch(finalUrl, {
		credentials: 'same-origin',
		method: 'PUT',
		body: JSON.stringify({
			token: "790f6f150492ebe24c6197f53ff10010",
			authentication: "cb75be4a34ce9222914c0555f6faaa8d",
			title: title,
			content: content,
			tags: tags
		}),
		headers: new Headers({
			'Content-Type': 'application/json'
		}),
	})
	.then(function(response) {
		return response.json();
	})
	.then(function(json) {
		key = json.data.key;
	})
	.catch(err => {
		console.log(err);
	});
}

</script>

<script>
var key = "";
var title = "";
var content = "";
var tags = [];
var apiUrl = new URL('http://localhost:8000/api/pages');

var editor = new EasyMDE({
	autofocus: true,
	toolbar: false,
	spellChecker: false,
	status: ["lines", "words"],
	tabSize: 4,
	initialValue: '# Title \n'
});

// Editor event change
editor.codemirror.on("change", function(){
	var editorValue = editor.value();
	tags = getTags(editorValue);
	title = getTitle(editorValue);
	content = getContent(editorValue, title, tags);
	console.log(content);
	//updatePage();
});

//createPage();

</script>