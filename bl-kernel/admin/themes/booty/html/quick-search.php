<?php defined('BLUDIT') or die('Bludit CMS.'); ?>
<!--
	Quick search of the pages, used by the dashboard and the content manager.
	A field that opens the search modal, also with Ctrl+K or Cmd+K.
	The shortcut only works on the pages that include this file, the editor uses Ctrl+K for links.
-->
<button type="button" class="quick-search-trigger mb-4" id="searchTrigger">
	<span class="fa fa-search"></span>
	<span class="quick-search-hint"><?php $L->p('search-content-placeholder') ?></span>
	<span class="quick-search-shortcut" id="searchShortcut">Ctrl K</span>
</button>

<div class="quick-search-modal" id="searchModal">
	<div class="quick-search-overlay" id="searchOverlay"></div>
	<div class="quick-search-content">
		<div class="quick-search-header">
			<span class="fa fa-search"></span>
			<input type="text" id="jsclippy" class="quick-search-input" placeholder="<?php $L->p('search-content-placeholder') ?>" autocomplete="off">
		</div>
		<div id="searchResults" class="quick-search-results"></div>
	</div>
</div>

<script>
	$(document).ready(function() {
		var searchInput = $('#jsclippy');
		var searchResults = $('#searchResults');
		var modal = $('#searchModal');
		var searchTimeout;
		var searchRequest;
		var labels = <?php echo json_encode(array(
			'view' => $L->g('View'),
			'edit' => $L->g('Edit'),
			'noResults' => $L->g('no-results-found')
		), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
		var statusClass = {
			published: 'status-published',
			sticky: 'status-published',
			static: 'status-static',
			draft: 'status-draft',
			scheduled: 'status-scheduled'
		};

		// The shortcut is Cmd+K on Mac and Ctrl+K on the other systems
		if (/Mac|iPhone|iPad/.test(navigator.platform)) {
			$('#searchShortcut').text('⌘K');
		}

		function openSearch() {
			modal.addClass('active');
			$('body').css('overflow', 'hidden');
			setTimeout(function() {
				searchInput.focus();
			}, 150);
		}

		function closeSearch() {
			// Stop a pending search so it does not fill the results after closing
			clearTimeout(searchTimeout);
			if (searchRequest) {
				searchRequest.abort();
			}
			modal.removeClass('active');
			$('body').css('overflow', '');
			searchInput.val('');
			searchResults.empty();
		}

		// Results are built with .text() so titles are never read as HTML
		function renderItem(item) {
			var row = $('<div class="search-suggestion">');
			$('<div class="search-suggestion-item">')
				.append($('<span class="status-badge">').addClass(statusClass[item.type] || '').text(item.typeLabel))
				.append($('<a>').attr('href', DOMAIN_ADMIN + 'edit-content/' + item.id).text(item.text))
				.appendTo(row);

			var options = $('<div class="search-suggestion-options">').appendTo(row);
			if (item.public) {
				$('<a target="_blank">').attr('href', DOMAIN_PAGES + item.id).text(labels.view).appendTo(options);
			}
			$('<a>').attr('href', DOMAIN_ADMIN + 'edit-content/' + item.id).text(labels.edit).appendTo(options);
			return row;
		}

		function performSearch(query) {
			if (searchRequest) {
				searchRequest.abort();
			}
			if (!query.trim()) {
				searchResults.empty();
				return;
			}

			searchRequest = $.ajax({
				url: HTML_PATH_ADMIN_ROOT + "ajax/clippy",
				data: { query: query },
				dataType: "json",
				success: function(data) {
					searchResults.empty();
					if (data.results && data.results.length > 0) {
						data.results.forEach(function(item) {
							searchResults.append(renderItem(item));
						});
					} else {
						$('<div class="search-no-results">').text(labels.noResults).appendTo(searchResults);
					}
				},
				error: function(xhr, status) {
					// Do not keep the results of the previous query
					if (status !== 'abort') {
						searchResults.empty();
					}
				}
			});
		}

		searchInput.on('input', function() {
			clearTimeout(searchTimeout);
			var query = $(this).val();
			searchTimeout = setTimeout(function() {
				performSearch(query);
			}, 300);
		});

		$('#searchTrigger').on('click', openSearch);
		$('#searchOverlay').on('click', closeSearch);

		$(document).on('keydown', function(e) {
			if (e.key === 'Escape' && modal.hasClass('active')) {
				closeSearch();
			}
			if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
				e.preventDefault();
				openSearch();
			}
		});
	});
</script>
