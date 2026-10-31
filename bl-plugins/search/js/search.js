(function () {
	var script = document.currentScript;

	document.querySelectorAll('form[data-search-url]').forEach(function (form) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			var text = form.querySelector('input[type=search]').value.trim();
			if (text && text.length >= Number(form.dataset.minChars)) {
				window.location.href = form.dataset.searchUrl + encodeURIComponent(text);
			}
		});
	});

	var words = script.dataset.highlight.toLowerCase().split(/\s+/).filter(function (word) {
		return word.length >= 2;
	});
	if (words.length === 0) {
		return;
	}

	var escaped = words.map(function (word) {
		return word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	});
	var pattern = new RegExp('(' + escaped.join('|') + ')', 'gi');

	document.querySelectorAll('article, main, [role=main], .content').forEach(function (root) {
		var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
		var nodes = [];
		while (walker.nextNode()) {
			if (!walker.currentNode.parentNode.closest('script, style, mark, form, .search-results-header, .search-no-results')) {
				nodes.push(walker.currentNode);
			}
		}

		nodes.forEach(function (node) {
			// The odd parts of the split are the matches
			var parts = node.nodeValue.split(pattern);
			if (parts.length === 1) {
				return;
			}
			var fragment = document.createDocumentFragment();
			parts.forEach(function (part, index) {
				if (index % 2 === 0) {
					fragment.appendChild(document.createTextNode(part));
				} else {
					var mark = document.createElement('mark');
					mark.className = 'search-highlight';
					mark.textContent = part;
					fragment.appendChild(mark);
				}
			});
			node.parentNode.replaceChild(fragment, node);
		});
	});
})();
