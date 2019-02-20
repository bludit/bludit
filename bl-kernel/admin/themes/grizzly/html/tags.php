<div class="sidebar col-lg-2 p-0 pt-4">
	<ul id="menu" class="list-group list-group-flush">
		<li id="newPage" class="list-group-item" data-action="untagged"><i class="fa fa-edit"></i> New page</li>
	</ul>
	<ul id="currentTags" class="list-group list-group-flush pt-4">
	</ul>
</div>
<script>
// Array with all current tags in the system
// array[ (string) tagKey => (array) pagesKeys ]
var _currentTags = [];

// Display all the current tags to the <ul> list
function displayTags() {
	let response = ajax.getTags();
	response.then(function(tags) {
		// Log
		log('displayTags() => ajax.getTags => tags',tags);
		// Get the tag selected
		let tagSelected = $("li.tagSelected").data("key");
		// Init array for current tags
		_currentTags = [];
		// Remove all tags from the <ul>
		$("#currentTags").html('<li class="tagItem list-group-item tagSelected" data-action="untagged"><i class="fa fa-star-o"></i> Untagged</li>');
		// Add all tags to the <ul>
		tags.forEach(function(tag) {
			_currentTags[tag.key] = tag.list;
			if (tagSelected == tag.key) {
				$("#currentTags").append('<li class="tagItem list-group-item tagSelected" data-action="tag" data-key="'+tag.key+'"># '+tag.name+'</li>');
			} else {
				$("#currentTags").append('<li class="tagItem list-group-item" data-action="tag" data-key="'+tag.key+'"># '+tag.name+'</li>');
			}
		});
	});
}

$(document).ready(function() {
	// Click on tags
	$(document).on("click", "li.tagItem", function() {
		// Add class to the tag selected
		$("li.tagItem").removeClass("tagSelected");
		$(this).addClass("tagSelected");
		// Get the tag key clicked
		let tagKey = $(this).data("key");
		let action = $(this).data("action");

		// Log
		log('click li.tagItem => action',action);
		log('click li.tagItem => tagKey',tagKey);

		if (action=="untagged") {
			displayPagesUntagged();
		} else {
			// Display pages by the tag
			displayPagesByTag(tagKey);
		}
	});

	// Click on new page
	$(document).on("click", "#newPage", function() {
		createPage();
	});

	// Retrive and show the tags
	displayTags();
});
</script>

<div class="pages-list col-lg-2 p-0">
	<ul id="currentPages" class="list-group list-group-flush">
	</ul>
</div>
<script>
// Array with all current pages in the system
// array[ (string) pageKey => (array) { key, title, content, contentRaw, description, date } ]
var _currentPages = [];

// Display all the pages by the tag selected
// This function is called when the user click on a tag
function displayPagesByTag(tagKey) {
	let response = ajax.getTag(tagKey);
	response.then(function(tag) {
		// Log
		log('displayPagesByTag() => ajax.getTag => tag',tag);
		// Init array for current pages by tag
		_currentPages = [];
		// Remove all pages from the <ul>
		$("#currentPages").html("");
		tag.pages.forEach(function(page) {
			_currentPages[page.key] = page;
			// Add all pages to the <ul>
			$("#currentPages").append('<li class="pageItem list-group-item" data-key="'+page.key+'"><div class="pageItemTitle">'+page.title+'</div><div class="pageItemContent">'+page.contentRaw.substring(0, 50)+'</div></li>');
		});
	});
}

function displayPagesUntagged() {
	let response = ajax.getPagesUntagged();
	response.then(function(pages) {
		// Log
		log('displayPagesUntagged() => ajax.getPagesUntagged => pages',pages);
		// Init array for current pages by tag
		_currentPages = [];
		// Remove all pages from the <ul>
		$("#currentPages").html("");
		pages.forEach(function(page) {
			_currentPages[page.key] = page;
			// Add all pages to the <ul>
			$("#currentPages").append('<li class="pageItem list-group-item" data-key="'+page.key+'"><div class="pageItemTitle">'+page.title+'</div><div class="pageItemContent">'+page.contentRaw.substring(0, 50)+'</div></li>');
		});
	});
}

// Set the page selected
function loadPage(pageKey) {
	// Check the current key if the same as the page is editing
	if (_key == pageKey) {
		console.log("Page already loaded");
		return true;
	}
	console.log("Loading page by key: "+pageKey);
	// Set the current key
	_key = pageKey;
	// Get the current page
	let response = ajax.getPage(pageKey);
	response.then(function(page) {
		// Log
		log('loadPage() => ajax.getPage => page',page);
		let content = "";
		if (page.title.trim()) {
			content += "# "+page.title.trim()+"\n";
		}
		content += page.contentRaw;
		editorInitialize(content);
	});
}

$(document).ready(function() {
	// Click on pages
	$(document).on("click", "li.pageItem", function() {
		// Add class to the tag selected
		$("li.pageItem").removeClass("pageSelected");
		$(this).addClass("pageSelected");
		// Get the tag key clicked
		var pageKey = $(this).data("key");
		log('click li.pageItem => pageKey',pageKey);
		// Retrive all titles of the pages and show
		loadPage(pageKey);
	});
});
</script>
