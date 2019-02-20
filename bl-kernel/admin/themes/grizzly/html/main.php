<div id="toolbar" class="d-flex p-1">
	<i class="align-self-center fa fa-terminal pr-1"></i>
	<div id="message" class="mr-auto"></div>
	<div id="draft-button" class="pr-2 selected">Draft</div>
	<div id="delete-button" class="pr-2">Delete</div>
</div>
<script>
var _options = {
	'alertTimeout': 5, // Second in dissapear the alert
	'autosaveTimeout': 3 // Second to activate before call the autosave
};

function showAlert(text) {
	$("#message").html(text).fadeIn();
	setTimeout(function(){
		$("#message").html("");
	},_options['alertTimeout']*1000);
}
</script>

<textarea id="editor"></textarea>
<script>
var _editor = null;
var _key = null; // Current page key in the editor
var _tags = []; // Current tags from the content
var _content = ""; // Current content, this variable helps to know when the content was changed
var _autosaveTimer = null; // Timer object for the autosave
var _draft = true;

function editorInitialize(content) {
	_editor = new EasyMDE({
		autofocus: true,
		toolbar: false,
		spellChecker: false,
		status: false,
		tabSize: 4,
		initialValue: content
	});

	// Get the tags from the content
	// When the content is setted the tags need to be setted
	_tags = parser.tags(content);

	// Editor event change
	_editor.codemirror.on("change", function(){
		// Reset timer
		if (_autosaveTimer != null) {
			clearTimeout(_autosaveTimer);
		}

		// Activate timer
		_autosaveTimer = setTimeout(function() {
			updatePage("Saved");
		}, _options["autosaveTimeout"]*1000);
	});
}

function editorGetContent() {
	return _editor.value();
}

function updatePage(alertMessage) {
	log('Updating page...', '');
	_content = editorGetContent();
	var tags = parser.tags(_content);
	var title = parser.title(_content);
	var newContent = parser.removeFirstLine(_content);

	// Update the page because was a change in the content
	ajax.updatePage(_key, title, newContent, tags, _draft).then(function(key) {
		_key = key;
		showAlert(alertMessage);
	});

	// Check if there are new tags in the editor
	// If there are new tags get the new tags for the sidebar
	if (JSON.stringify(_tags) != JSON.stringify(tags)) {
		_tags = tags;
		displayTags();
	}
}

function createPage() {
	// New pages is draft by default
	setDraft(true);
	let response = ajax.createPage();
	response.then(function(key) {
		// Log
		log('createPage() => ajax.createPage => key',key);
		_key = key;
		editorInitialize('# Title \n');
	});
}

function setDraft(value) {
	let message = "";
	if (value) {
		_draft = true;
		$("#draft-button").addClass("selected");
		message = "Page saved as draft";
	} else {
		_draft = false;
		$("#draft-button").removeClass("selected");
		message = "Page published";
	}

	updatePage(message);
}

// MAIN
$(document).ready(function() {
	// Click on draft button
	$(document).on("click", "#draft-button", function() {
		if (_draft) {
			setDraft(false);
		} else {
			setDraft(true);
		}
	});

	showAlert("Welcome to Bludit");
});

</script>