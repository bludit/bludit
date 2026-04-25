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

// Render a single row (or row + nested children) for a page key.
// $type controls which columns/buttons are shown; $isSticky adds the Sticky badge
// and flips the toggle button into "Unstick" mode.
function tableRow($pageKey, $type, $isSticky = false, $renderChildren = false) {
	global $url;
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

	echo '<tr>';
	echo '<td class="pt-3">';
	echo '<div>';
	echo '<a style="font-size: 1.1em" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">';
	echo ($page->title() ? $page->title() : '<span class="label-empty-title">'.$L->g('Empty title').'</span> ');
	echo '</a>';
	if ($isSticky) {
		echo ' <span class="badge badge-warning align-middle ml-1" title="'.$L->g('Sticky').'"><i class="fa fa-thumb-tack"></i> '.$L->g('Sticky').'</span>';
	}
	echo '</div>';
	echo '<div><p style="font-size: 0.8em" class="m-0 text-uppercase text-muted">'.$dateLabel.'</p></div>';
	echo '</td>';

	if ($showURL) {
		$friendlyURL = Text::isEmpty($url->filters('page')) ? '/'.$page->key() : '/'.$url->filters('page').'/'.$page->key();
		echo '<td class="pt-3 d-none d-xl-table-cell contentURL"><a target="_blank" href="'.$page->permalink().'" title="'.$friendlyURL.'">'.$friendlyURL.'</a></td>';
	}

	echo '<td class="contentTools pt-3 text-center align-middle">'.PHP_EOL;
	echo '<div class="dropdown actionsDropdown">';
	echo '<button class="btn btn-link text-secondary p-1 actionsDropdownToggle" type="button" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false" title="'.$L->g('Actions').'"><i class="fa fa-bars"></i></button>';
	echo '<div class="dropdown-menu dropdown-menu-right">';

	// View / Edit
	if ($showURL) {
		echo '<a class="dropdown-item" target="_blank" href="'.$page->permalink().'"><i class="fa fa-desktop fa-fw mr-2"></i>'.$L->g('View').'</a>';
	}
	echo '<a class="dropdown-item" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'"><i class="fa fa-edit fa-fw mr-2"></i>'.$L->g('Edit').'</a>';

	// Sticky / Unstick toggle, between View/Edit and Move-to.
	$stickyToggleTarget = false;
	if ($type === 'published') {
		$stickyToggleTarget = 'sticky';
	} elseif ($type === 'sticky') {
		$stickyToggleTarget = 'published';
	}
	if ($stickyToggleTarget) {
		echo '<div class="dropdown-divider"></div>';
		echo '<a href="#" class="dropdown-item changeTypeButton" data-key="'.$page->key().'" data-type="'.$stickyToggleTarget.'"><i class="fa '.moveTypeIcon($stickyToggleTarget).' fa-fw mr-2"></i>'.moveTypeLabel($type, $stickyToggleTarget, $L).'</a>';
	}

	// Move to ... (everything except the sticky toggle target rendered above).
	if (isset($moves[$type])) {
		$remaining = array();
		foreach ($moves[$type] as $target) {
			if ($target !== $stickyToggleTarget) {
				$remaining[] = $target;
			}
		}
		if (!empty($remaining)) {
			echo '<div class="dropdown-divider"></div>';
			foreach ($remaining as $target) {
				echo '<a href="#" class="dropdown-item changeTypeButton" data-key="'.$page->key().'" data-type="'.$target.'"><i class="fa '.moveTypeIcon($target).' fa-fw mr-2"></i>'.moveTypeLabel($type, $target, $L).'</a>';
			}
		}
	}

	if (count($page->children()) == 0) {
		echo '<div class="dropdown-divider"></div>';
		echo '<a href="#" class="dropdown-item text-danger deletePageButton" data-toggle="modal" data-target="#jsdeletePageModal" data-key="'.$page->key().'"><i class="fa fa-trash fa-fw mr-2"></i>'.$L->g('Delete').'</a>';
	}
	echo '</div></div>';
	echo '</td>';
	echo '</tr>';

	if ($renderChildren) {
		foreach ($page->children() as $child) {
			echo '<tr>';
			echo '<td class="child">';
			echo '<div>';
			echo '<a style="font-size: 1.1em" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$child->key().'">';
			echo ($child->title() ? $child->title() : '<span class="label-empty-title">'.$L->g('Empty title').'</span> ');
			echo '</a>';
			echo '</div>';
			echo '<div><p style="font-size: 0.8em" class="m-0 text-uppercase text-muted">'.$L->g('Position').': '.$child->position().'</p></div>';
			echo '</td>';

			if ($showURL) {
				$friendlyChildURL = Text::isEmpty($url->filters('page')) ? '/'.$child->key() : '/'.$url->filters('page').'/'.$child->key();
				echo '<td class="d-none d-xl-table-cell contentURL"><a target="_blank" href="'.$child->permalink().'" title="'.$friendlyChildURL.'">'.$friendlyChildURL.'</a></td>';
			}

			echo '<td class="contentTools pt-3 text-center align-middle">'.PHP_EOL;
			echo '<div class="dropdown actionsDropdown">';
			echo '<button class="btn btn-link text-secondary p-1 actionsDropdownToggle" type="button" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false" title="'.$L->g('Actions').'"><i class="fa fa-bars"></i></button>';
			echo '<div class="dropdown-menu dropdown-menu-right">';
			if ($showURL) {
				echo '<a class="dropdown-item" target="_blank" href="'.$child->permalink().'"><i class="fa fa-desktop fa-fw mr-2"></i>'.$L->g('View').'</a>';
			}
			echo '<a class="dropdown-item" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$child->key().'"><i class="fa fa-edit fa-fw mr-2"></i>'.$L->g('Edit').'</a>';
			echo '<div class="dropdown-divider"></div>';
			echo '<a href="#" class="dropdown-item text-danger deletePageButton" data-toggle="modal" data-target="#jsdeletePageModal" data-key="'.$child->key().'"><i class="fa fa-trash fa-fw mr-2"></i>'.$L->g('Delete').'</a>';
			echo '</div></div>';
			echo '</td>';
			echo '</tr>';
		}
	}
}

// Render rows for a list, applying the parent/child nesting rules used by the
// Static tab and by the Pages/Sticky lists when ORDER_BY is "position".
function tableRows($list, $type, $isSticky = false) {
	$nestChildren = ($type === 'static') || (ORDER_BY === 'position');
	foreach ($list as $pageKey) {
		try {
			$page = new Page($pageKey);
		} catch (Exception $e) {
			continue;
		}
		if ($nestChildren) {
			if ($page->isChild()) {
				continue;
			}
			tableRow($pageKey, $type, $isSticky, true);
		} else {
			tableRow($pageKey, $type, $isSticky, false);
		}
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

	echo '<table class="table mt-3"><thead><tr>';
	echo '<th class="border-0" scope="col">'.$L->g('Title').'</th>';
	if ($type === 'static') {
		echo '<th class="border-0 d-none d-xl-table-cell" scope="col">'.$L->g('URL').'</th>';
	}
	echo '<th class="border-0 text-center d-sm-table-cell" scope="col">'.$L->g('Actions').'</th>';
	echo '</tr></thead><tbody>';
	tableRows($list, $type);
	echo '</tbody></table>';
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

	echo '<table class="table mt-3"><thead><tr>';
	echo '<th class="border-0" scope="col">'.$L->g('Title').'</th>';
	echo '<th class="border-0 d-none d-xl-table-cell" scope="col">'.$L->g('URL').'</th>';
	echo '<th class="border-0 text-center d-sm-table-cell" scope="col">'.$L->g('Actions').'</th>';
	echo '</tr></thead><tbody>';
	if (!empty($sticky) && $isFirstPage) {
		tableRows($sticky, 'sticky', true);
	}
	if (!empty($published)) {
		tableRows($published, 'published', false);
	}
	echo '</tbody></table>';
}

?>

<!-- TABS -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="pages-tab" data-toggle="tab" href="#pages" role="tab"><?php $L->p('Pages') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="static-tab" data-toggle="tab" href="#static" role="tab"><?php $L->p('Static') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="scheduled-tab" data-toggle="tab" href="#scheduled" role="tab"><?php $L->p('Scheduled') ?> <?php if (count($scheduled)>0) { echo '<span class="badge badge-danger">'.count($scheduled).'</span>'; } ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="draft-tab" data-toggle="tab" href="#draft" role="tab"><?php $L->p('Draft') ?></a>
	</li>
	<?php if (!empty($autosave)): ?>
	<li class="nav-item">
		<a class="nav-link" id="autosave-tab" data-toggle="tab" href="#autosave" role="tab"><?php $L->p('Autosave') ?></a>
	</li>
	<?php endif; ?>
</ul>
<div class="tab-content">
	<!-- TABS PAGES (includes sticky on top) -->
	<div class="tab-pane show active" id="pages" role="tabpanel">

		<?php tablePages(); ?>

		<?php if (Paginator::numberOfPages() > 1): ?>
		<!-- Paginator -->
		<nav class="paginator">
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
	$(`a[href="${anchor}"]`).tab('show');
</script>
