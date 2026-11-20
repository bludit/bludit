<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Content'), 'icon'=>'archive'));

function moveTypeIcon($type) {
	$icons = array(
		'published' => 'fa-file-text-o',
		'sticky'    => 'fa-thumb-tack',
		'static'    => 'fa-file',
		'draft'     => 'fa-pencil',
	);
	return isset($icons[$type]) ? $icons[$type] : 'fa-file';
}

function moveTypeLabel($current, $target, $L) {
	if ($target === 'sticky') {
		return $L->g('Sticky');
	}
	if ($current === 'sticky' && $target === 'published') {
		return $L->g('Unstick');
	}
	$labels = array(
		'published' => $L->g('Move to Page'),
		'static'    => $L->g('Move to Static'),
		'draft'     => $L->g('Move to Draft'),
	);
	return isset($labels[$target]) ? $labels[$target] : $target;
}

// Cover thumbnail of the row, or an empty square to keep the titles aligned.
// When the thumbnail does not exist (thumbnails disabled or legacy names) the
// browser falls back to the cover image.
function rowThumbnail($page) {
	$thumbnail = $page->thumbCoverImage();
	if (empty($thumbnail)) {
		return '<span class="content-thumb content-thumb-empty"><i class="fa fa-file-text-o"></i></span>';
	}
	$cover = $page->coverImage();
	return '<img class="content-thumb" loading="lazy" alt="" src="'.Sanitize::html($thumbnail).'"'
		.($cover && $cover !== $thumbnail ? ' data-fallback="'.Sanitize::html($cover).'"' : '')
		.'>';
}

// Second line of the row: date or position, category and author.
// Everything comes from the pages and users databases, already in memory.
function rowMeta($page, $label) {
	global $users;

	$meta = array('<span>'.$label.'</span>');
	$category = $page->category();
	if (!empty($category)) {
		$meta[] = '<span><i class="fa fa-folder-o"></i> '.$category.'</span>';
	}
	// The author is only useful when the site has more than one user
	if (count($users->db) > 1) {
		$username = $page->username();
		$author = !empty($users->db[$username]['nickname']) ? $users->db[$username]['nickname'] : $username;
		$meta[] = '<span><i class="fa fa-user-o"></i> '.$author.'</span>';
	}
	return '<div class="content-meta">'.implode('', $meta).'</div>';
}

// Title cell: thumbnail, title linked to the editor and the meta line
function rowTitleCell($page, $label, $isSticky = false, $isChild = false) {
	global $L;

	$html  = '<td class="content-title-cell'.($isChild ? ' child' : '').'">';
	$html .= '<div class="content-row">';
	$html .= rowThumbnail($page);
	$html .= '<div class="content-row-text">';
	$html .= '<a class="content-title" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">';
	$html .= ($page->title() ? $page->title() : '<span class="label-empty-title">'.$L->g('Empty title').'</span>');
	$html .= '</a>';
	if ($isSticky) {
		$html .= ' <span class="status-badge status-badge-inline status-published ms-1" title="'.$L->g('Sticky').'"><i class="fa fa-thumb-tack"></i> '.$L->g('Sticky').'</span>';
	}
	$html .= rowMeta($page, $label);
	$html .= '</div></div></td>';
	return $html;
}

// URL cell, the icon opens the page in a new tab
function rowURLCell($page) {
	global $url;
	global $L;

	$friendlyURL = Text::isEmpty($url->filters('page')) ? '/'.$page->key() : '/'.$url->filters('page').'/'.$page->key();
	return '<td class="d-none d-xl-table-cell contentURL align-middle">'
		.'<a target="_blank" href="'.$page->permalink().'" title="'.$L->g('View').'">'
		.'<span class="content-url">'.$friendlyURL.'</span> <i class="fa fa-external-link"></i></a></td>';
}

// Actions cell, the menu is not rendered when there are no actions
function rowActionsCell($items) {
	global $L;

	$html = '<td class="contentTools text-center align-middle">';
	if (!empty($items)) {
		$html .= '<div class="dropdown actionsDropdown">';
		$html .= '<button class="btn btn-link text-secondary p-1 actionsDropdownToggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-haspopup="true" aria-expanded="false" title="'.$L->g('Actions').'"><i class="fa fa-bars"></i></button>';
		$html .= '<div class="dropdown-menu dropdown-menu-end">'.implode('<div class="dropdown-divider"></div>', $items).'</div>';
		$html .= '</div>';
	}
	return $html.'</td>';
}

function deleteItem($page) {
	global $L;
	return '<a href="#" class="dropdown-item text-danger deletePageButton" data-bs-toggle="modal" data-bs-target="#jsdeletePageModal" data-key="'.$page->key().'"><i class="fa fa-trash fa-fw me-2"></i>'.$L->g('Delete').'</a>';
}

// Render a single row (or row + nested children) for a page key.
// $type controls which columns/buttons are shown; $isSticky adds the Sticky badge
// and flips the toggle button into "Unstick" mode.
// The title opens the editor and the URL opens the page, so the menu only has
// the actions that change the page.
function tableRow($pageKey, $type, $isSticky = false, $renderChildren = false) {
	global $L;

	try {
		$page = new Page($pageKey);
	} catch (Exception $e) {
		return;
	}

	$showURL = ($type === 'published' || $type === 'static' || $type === 'sticky');

	// Allowed "Move to" transitions per current type.
	$moves = array(
		'published' => array('sticky', 'static', 'draft'),
		'sticky'    => array('published', 'static', 'draft'),
		'draft'     => array('published', 'static'),
		'static'    => array('published', 'draft'),
	);

	$dateLabel = '';
	if ($type === 'scheduled') {
		$dateLabel = $L->g('Scheduled').': '.$page->date(SCHEDULED_DATE_FORMAT);
	} elseif ((ORDER_BY === 'position') || ($type !== 'published' && $type !== 'sticky')) {
		$dateLabel = $L->g('Position').': '.$page->position();
	} else {
		$dateLabel = $page->date(MANAGE_CONTENT_DATE_FORMAT);
	}

	// Sticky / Unstick toggle first, then the other moves
	$items = array();
	$stickyToggleTarget = false;
	if ($type === 'published') {
		$stickyToggleTarget = 'sticky';
	} elseif ($type === 'sticky') {
		$stickyToggleTarget = 'published';
	}
	if ($stickyToggleTarget) {
		$items[] = '<a href="#" class="dropdown-item changeTypeButton" data-key="'.$page->key().'" data-type="'.$stickyToggleTarget.'"><i class="fa '.moveTypeIcon($stickyToggleTarget).' fa-fw me-2"></i>'.moveTypeLabel($type, $stickyToggleTarget, $L).'</a>';
	}
	if (isset($moves[$type])) {
		$remaining = '';
		foreach ($moves[$type] as $target) {
			if ($target !== $stickyToggleTarget) {
				$remaining .= '<a href="#" class="dropdown-item changeTypeButton" data-key="'.$page->key().'" data-type="'.$target.'"><i class="fa '.moveTypeIcon($target).' fa-fw me-2"></i>'.moveTypeLabel($type, $target, $L).'</a>';
			}
		}
		if ($remaining !== '') {
			$items[] = $remaining;
		}
	}
	if (count($page->children()) == 0) {
		$items[] = deleteItem($page);
	}

	echo '<tr>';
	echo rowTitleCell($page, $dateLabel, $isSticky);
	if ($showURL) {
		echo rowURLCell($page);
	}
	echo rowActionsCell($items);
	echo '</tr>';

	if ($renderChildren) {
		foreach ($page->children() as $child) {
			// Only nest children whose type matches this table; a child moved
			// to a different type renders in its own tab instead (see tableRows()).
			if ($child->type() !== $type) {
				continue;
			}
			echo '<tr>';
			echo rowTitleCell($child, $L->g('Position').': '.$child->position(), false, true);
			if ($showURL) {
				echo rowURLCell($child);
			}
			echo rowActionsCell(array(deleteItem($child)));
			echo '</tr>';
		}
	}
}

// Render rows for a list, applying the parent/child nesting rules used by the
// Static tab and by the Pages/Sticky lists when ORDER_BY is "position".
function tableRows($list, $type, $isSticky = false) {
	$nestChildren = ($type === 'static') || (ORDER_BY === 'position');
	$listSet = array_flip($list);
	foreach ($list as $pageKey) {
		try {
			$page = new Page($pageKey);
		} catch (Exception $e) {
			continue;
		}
		// A page is only skipped here if its parent is also in this same list
		// (same type) and will render it nested. Otherwise the parent lives in
		// a different type/tab and would never draw this page as a child, so
		// it must be rendered standalone here or it vanishes from every tab.
		if ($nestChildren && $page->isChild() && isset($listSet[$page->parentKey()])) {
			continue;
		}
		tableRow($pageKey, $type, $isSticky, $nestChildren);
	}
}

// Render a full table for a single tab (Static, Scheduled, Draft, Autosave).
function table($type) {
	global $L;
	global $drafts;
	global $scheduled;
	global $static;
	global $autosave;

	if ($type === 'draft') {
		$list = $drafts;
		$emptyMessage = $L->g('There are no draft pages at this moment.');
	} elseif ($type === 'scheduled') {
		$list = $scheduled;
		$emptyMessage = $L->g('There are no scheduled pages at this moment.');
	} elseif ($type === 'static') {
		$list = $static;
		$emptyMessage = $L->g('There are no static pages at this moment.');
	} elseif ($type === 'autosave') {
		$list = $autosave;
		$emptyMessage = '';
	} else {
		return;
	}

	if (empty($list) && $type !== 'autosave') {
		echo '<p class="mt-4 text-muted">'.$emptyMessage.'</p>';
		return;
	}

	echo '<div class="content-card mt-3"><table class="table content-table mb-0"><thead><tr>';
	echo '<th scope="col">'.$L->g('Title').'</th>';
	if ($type === 'static') {
		echo '<th class="d-none d-xl-table-cell" scope="col">'.$L->g('URL').'</th>';
	}
	echo '<th class="text-center d-sm-table-cell" scope="col">'.$L->g('Actions').'</th>';
	echo '</tr></thead><tbody>';
	tableRows($list, $type);
	echo '</tbody></table></div>';
}

// Render the Pages tab: sticky rows first, then the paginated published list,
// in a single table.
function tablePages() {
	global $L;
	global $published;
	global $sticky;
	global $url;

	$isFirstPage = ($url->pageNumber() <= 1);

	if (empty($published) && (empty($sticky) || !$isFirstPage)) {
		echo '<p class="mt-4 text-muted">'.$L->g('There are no pages at this moment.').'</p>';
		return;
	}

	echo '<div class="content-card mt-3"><table class="table content-table mb-0"><thead><tr>';
	echo '<th scope="col">'.$L->g('Title').'</th>';
	echo '<th class="d-none d-xl-table-cell" scope="col">'.$L->g('URL').'</th>';
	echo '<th class="text-center d-sm-table-cell" scope="col">'.$L->g('Actions').'</th>';
	echo '</tr></thead><tbody>';
	if (!empty($sticky) && $isFirstPage) {
		tableRows($sticky, 'sticky', true);
	}
	if (!empty($published)) {
		tableRows($published, 'published', false);
	}
	echo '</tbody></table></div>';
}

?>

<?php
// Same space as below the title
$quickSearchMargin = 'mb-3';
include(PATH_ADMIN_THEMES . $site->adminTheme() . DS . 'html' . DS . 'quick-search.php');
?>

<!-- TABS -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="pages-tab" data-bs-toggle="tab" href="#pages" role="tab"><?php $L->p('Pages') ?> <?php if ($countPublished>0) { echo '<span class="tab-count">'.$countPublished.'</span>'; } ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="static-tab" data-bs-toggle="tab" href="#static" role="tab"><?php $L->p('Static') ?> <?php if (count($static)>0) { echo '<span class="tab-count">'.count($static).'</span>'; } ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="scheduled-tab" data-bs-toggle="tab" href="#scheduled" role="tab"><?php $L->p('Scheduled') ?> <?php if (count($scheduled)>0) { echo '<span class="status-badge status-badge-inline status-scheduled">'.count($scheduled).'</span>'; } ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="draft-tab" data-bs-toggle="tab" href="#draft" role="tab"><?php $L->p('Draft') ?> <?php if (count($drafts)>0) { echo '<span class="tab-count">'.count($drafts).'</span>'; } ?></a>
	</li>
	<?php if (!empty($autosave)): ?>
	<li class="nav-item">
		<a class="nav-link" id="autosave-tab" data-bs-toggle="tab" href="#autosave" role="tab"><?php $L->p('Autosave') ?> <?php if (count($autosave)>0) { echo '<span class="tab-count">'.count($autosave).'</span>'; } ?></a>
	</li>
	<?php endif; ?>
</ul>
<div class="tab-content">
	<!-- TABS PAGES (includes sticky on top) -->
	<div class="tab-pane show active" id="pages" role="tabpanel">

		<?php tablePages(); ?>

		<?php if (Paginator::numberOfPages() > 1): ?>
		<!-- Paginator -->
		<nav class="paginator mt-3">
			<ul class="pagination flex-wrap justify-content-center">

			<!-- First button -->
			<li class="page-item <?php if (!Paginator::showPrev()) echo 'disabled' ?>">
				<a class="page-link" href="<?php echo Paginator::firstPageUrl() ?>"><span class="align-middle fa fa-media-skip-backward"></span> <?php echo $L->get('First'); ?></a>
			</li>

			<!-- Previous button -->
			<li class="page-item <?php if (!Paginator::showPrev()) echo 'disabled' ?>">
				<a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>"><?php echo $L->get('Previous'); ?></a>
			</li>

			<!-- Next button -->
			<li class="page-item <?php if (!Paginator::showNext()) echo 'disabled' ?>">
				<a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>"><?php echo $L->get('Next'); ?></a>
			</li>

			<!-- Last button -->
			<li class="page-item <?php if (!Paginator::showNext()) echo 'disabled' ?>">
				<a class="page-link" href="<?php echo Paginator::lastPageUrl() ?>"><?php echo $L->get('Last'); ?> <span class="align-middle fa fa-media-skip-forward"></span></a>
			</li>

			</ul>
		</nav>
		<?php endif; ?>
	</div>

	<!-- TABS STATIC -->
	<div class="tab-pane" id="static" role="tabpanel">
	<?php table('static'); ?>
	</div>

	<!-- TABS SCHEDULED -->
	<div class="tab-pane" id="scheduled" role="tabpanel">
	<?php table('scheduled'); ?>
	</div>

	<!-- TABS DRAFT -->
	<div class="tab-pane" id="draft" role="tabpanel">
	<?php table('draft'); ?>
	</div>

	<!-- TABS AUTOSAVE -->
	<?php if (!empty($autosave)): ?>
	<div class="tab-pane" id="autosave" role="tabpanel">
	<?php table('autosave'); ?>
	</div>
	<?php endif; ?>
</div>

<!-- Modal for delete page -->
<?php
	echo Bootstrap::modal(array(
		'buttonPrimary'=>$L->g('Delete'),
		'buttonPrimaryClass'=>'btn-danger deletePageModalAcceptButton',
		'buttonSecondary'=>$L->g('Cancel'),
		'buttonSecondaryClass'=>'btn-link',
		'modalTitle'=>$L->g('Delete content'),
		'modalText'=>$L->g('Are you sure you want to delete this page'),
		'modalId'=>'jsdeletePageModal'
	));
?>
<script>
// Cover thumbnails fall back to the cover image when the thumbnail is missing.
// The error event does not bubble, so listen in the capture phase.
document.addEventListener('error', function(e) {
	var img = e.target;
	if (img.classList && img.classList.contains('content-thumb') && img.dataset.fallback) {
		img.src = img.dataset.fallback;
		delete img.dataset.fallback;
	}
}, true);

$(document).ready(function() {
	var key = false;

	// Button for delete a page in the table
	$(document).on("click", ".deletePageButton", function() {
		key = $(this).data('key');
	});

	// Event from button accept from the modal
	$(".deletePageModalAcceptButton").on("click", function() {
		var currentTab = window.location.hash ? window.location.hash.substring(1) : 'pages';

		var form = jQuery('<form>', {
			'action': HTML_PATH_ADMIN_ROOT+'edit-content/'+key,
			'method': 'post',
			'target': '_top'
		}).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'tokenCSRF',
			'value': tokenCSRF
		})).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'key',
			'value': key
		})).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'type',
			'value': 'delete'
		})).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'tab',
			'value': currentTab
		}));

		form.hide().appendTo("body").submit();
	});

	// Move-to: change page type via AJAX
	$(document).on("click", ".changeTypeButton", function(e) {
		e.preventDefault();
		var $btn = $(this);
		if ($btn.data('busy')) { return; }
		$btn.data('busy', true).css('opacity', 0.5);

		$.ajax({
			type: "POST",
			url: HTML_PATH_ADMIN_ROOT + "ajax/change-type",
			data: {
				tokenCSRF: tokenCSRF,
				key: $btn.data('key'),
				type: $btn.data('type')
			},
			dataType: "json"
		}).done(function(response) {
			if (response && response.status === 0) {
				window.location.reload();
			} else {
				$btn.data('busy', false).css('opacity', 1);
				alert((response && response.message) ? response.message : <?php echo json_encode($L->g('Failed to change type.')); ?>);
			}
		}).fail(function() {
			$btn.data('busy', false).css('opacity', 1);
			alert(<?php echo json_encode($L->g('Failed to change type.')); ?>);
		});
	});
});
</script>

<script>
	// Open the tab defined in the URL
	const anchor = window.location.hash;
	const anchorTabEl = document.querySelector(`a[href="${anchor}"]`);
	if (anchorTabEl) {
		bootstrap.Tab.getOrCreateInstance(anchorTabEl).show();
	}
</script>
