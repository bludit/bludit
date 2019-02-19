<div id="toolbar" class="d-flex p-1">
  <div id="message" class="mr-auto"></div>
  <div class="pr-2">Draft</div>
  <div>Delete</div>
</div>
<script>
var _options = {
	'alertTimeout': 5, // Second in dissapear the alert
	'autosaveTimeout': 5 // Second to activate before call the autosave
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


function editorInitialize(content) {
	_editor = new EasyMDE({
		autofocus: true,
		toolbar: false,
		spellChecker: false,
		status: false,
		tabSize: 4,
		initialValue: content
	});

	// Editor event change
	_editor.codemirror.on("change", function(){
		// If the content doesn't changed is not need to autosave
		if (_content == editorGetContent()) {
			return true;
		}

		// Reset timer
		if (_autosaveTimer != null) {
			clearTimeout(_autosaveTimer);
		}

		// Activate timer
		_autosaveTimer = setTimeout(function() {
			log('Autosave running', '');
			_content = editorGetContent();
			var tags = parser.tags(_content);
			var title = parser.title(_content);
			var newContent = parser.removeFirstLine(_content);

			// Update the page because was a change in the content
			ajax.updatePage(_key, title, newContent, tags);

			// Check if there are new tags in the editor
			// If there are new tags get the new tags for the sidebar
			if (JSON.stringify(_tags) != JSON.stringify(tags)) {
				_tags = tags;
				displayTags();
			}
		}, _options['autosaveTimeout']*1000);
	});
}

function editorSetContent(text) {
	// Get the tags from the content
	// When the content is setted the tags need to be setted
	_tags = parser.tags(text);

	// Set the current content to the variable
	// This variable helps to know when the content was changed
	_content = text;

	// Set the new content into the editor
	_editor.value(text);
}

function editorGetContent() {
	return _editor.value();
}

// MAIN

// Init editor area
editorInitialize("# Title \n");

$(document).ready(function() {
	showAlert("Welcome to Bludit");
});

</script>