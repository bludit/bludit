<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================
	function deletePage(key) {
		var args = {
			key: key
		};
		api.deletePage(args).then(function(response) {
			if (response.status == 0) {
				logs('Page deleted. Key: ' + response.data.key);
				showAlertInfo("<?php $L->p('Page deleted') ?>");
				$('#pagekey-'+response.data.key).addClass('disabled');
			} else {
				logs('An error occurred while trying to delete the page.');
				showAlertError(response.message);
			}
		});
	}

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {

		$(".btnDeletePage").on("click", function() {
			var key = $(this).data('key');
			logs('Deleting page. Key: ' + key);
			bootbox.confirm({
				message: '<?php $L->p('Are you sure you want to delete this page') ?>',
				buttons: {
					cancel: {
						label: '<i class="bi bi-x"></i><?php $L->p('Cancel') ?>',
						className: 'btn-sm btn-secondary'
					},
					confirm: {
						label: '<i class="bi bi-check"></i><?php $L->p('Confirm') ?>',
						className: 'btn-sm btn-primary'
					}
				},
				closeButton: false,
				callback: function(result) {
					if (result) {
						deletePage(key);
					}
				}
			});
		});

	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-folder"></i><?php $L->p('Content') ?></h2>
	<div class="ms-auto">
		<a id="btnNew" class="btn btn-primary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'editor' ?>" role="button"><i class="bi bi-plus-circle"></i><?php $L->p('New content') ?></a>
	</div>
</div>

<?php

function table($type)
{
	global $L;
	global $published;
	global $drafts;
	global $scheduled;
	global $static;
	global $sticky;
    global $unlisted;

	if ($type == 'published') {
		$list = $published;
		if (empty($list)) {
			echo '<p class="text-muted p-4">';
			echo $L->g('There are no pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type == 'draft') {
		$list = $drafts;
		if (empty($list)) {
			echo '<p class="text-muted p-4">';
			echo $L->g('There are no draft pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type == 'scheduled') {
		$list = $scheduled;
		if (empty($list)) {
			echo '<p class="text-muted p-4">';
			echo $L->g('There are no scheduled pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type == 'static') {
		$list = $static;
		if (empty($list)) {
			echo '<p class="text-muted p-4">';
			echo $L->g('There are no static pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type == 'unlisted') {
		$list = $unlisted;
		if (empty($list)) {
			echo '<p class="text-muted p-4">';
			echo $L->g('There are no unlisted pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type == 'sticky') {
		$list = $sticky;
		if (empty($list)) {
			echo '<p class="text-muted p-4">';
			echo $L->g('There are no sticky pages at this moment.');
			echo '</p>';
			return false;
		}
	}

	echo '<table class="table table-striped"><tbody><tr></tr>';

	if ((ORDER_BY == 'position') || $type == 'static') {
		foreach ($list as $pageKey) {
			try {
				$page = new Page($pageKey);
				if (!$page->isChild()) {
					echo '<tr id="pagekey-'.$pageKey.'">';

					echo '<td class="pt-4 pb-4">
					<div>
						<i class="bi bi-file-text"></i><span>' . ($page->title() ? $page->title() : '<span class="text-muted">' . $L->g('Empty title') . '</span> ') . '</span>
					</div>
					<div class="mt-1">
						<a class="me-2" target="_blank" href="' . $page->permalink() . '">' . $L->g('View') . '</a>
						<a class="me-2" href="' . HTML_PATH_ADMIN_ROOT . 'editor/' . $page->key() . '">' . $L->g('Edit') . '</a>
					';

					if (count($page->children()) == 0) {
						echo '<span class="link btnDeletePage" data-key="' . $page->key() . '">' . $L->g('Delete') . '</span>';
					}

					echo '
					</div>
					</td>';

					echo '<td class="pt-4 pb-4 d-none d-lg-table-cell">' . $L->get('Category') . ': ' . ($page->category() ? $page->category() : $L->get('uncategorized')) . '</td>';

					echo '<td class="pt-4 text-center d-sm-table-cell">' . (((ORDER_BY == 'position') || ($type != 'published')) ? $L->g('Position') . ': ' . $page->position() : $page->date(MANAGE_CONTENT_DATE_FORMAT)) . '</td>';

					echo '</tr>';

					foreach ($page->children() as $child) {
						echo '<tr id="pagekey-'.$child->key().'">';

						echo '<td class="ps-4 pt-4 pb-4">
						<div>
                        <i class="bi bi-file-text"></i><span>' . ($child->title() ? $child->title() : '<span class="text-muted">' . $L->g('Empty title') . '</span> ') . '</span>
                        <div class="text-muted"><small><i class="bi bi-diagram-2-fill"></i>' . ($page->title() ? $page->title() : $L->g('Empty title')) . '</small></div>
						</div>
						<div class="mt-1">
							<a class="me-2" target="_blank" href="' . $child->permalink() . '">' . $L->g('View') . '</a>
							<a class="me-2" href="' . HTML_PATH_ADMIN_ROOT . 'editor/' . $child->key() . '">' . $L->g('Edit') . '</a>
							<span class="link btnDeletePage" data-key="' . $child->key() . '">' . $L->g('Delete') . '</span>
						</div>
						</td>';

						echo '<td class="pt-4 d-none d-lg-table-cell">' . $L->get('Category') . ': ' . ($child->category() ? $child->category() : $L->get('uncategorized')) . '</td>';

						echo '<td class="pt-4 text-center d-sm-table-cell">' . (((ORDER_BY == 'position') || ($type != 'published')) ? $L->g('Position') . ': ' . $child->position() : $child->date(MANAGE_CONTENT_DATE_FORMAT)) . '</td>';

						echo '</tr>';
					}
				}
			} catch (Exception $e) {
				// Continue
			}
		}
	} else {
		foreach ($list as $pageKey) {
			try {
				$page = new Page($pageKey);
				echo '<tr id="pagekey-'.$pageKey.'">';

				echo '<td class="pt-4 pb-4">
					<div>
						<i class="bi bi-file-text"></i>' . ($page->title() ? $page->title() : '<span class="text-muted">' . $L->g('Empty title') . '</span> ') . '
					</div>
					<div class="mt-1">
						<a class="me-2" target="_blank" href="' . $page->permalink() . '">' . $L->g('View') . '</a>
						<a class="me-2" href="' . HTML_PATH_ADMIN_ROOT . 'editor/' . $page->key() . '">' . $L->g('Edit') . '</a>
						<span class="link btnDeletePage" data-key="' . $page->key() . '">' . $L->g('Delete') . '</span>
					</div>
				</td>';

				echo '<td class="pt-4 d-none d-lg-table-cell">' . $L->get('Category') . ': ' . ($page->category() ? $page->category() : $L->get('uncategorized')) . '</td>';

				echo '<td class="pt-4 text-center d-sm-table-cell"> ' . (((ORDER_BY == 'position') || ($type != 'published')) ? $L->g('Position') . ': ' . $page->position() : $page->date(MANAGE_CONTENT_DATE_FORMAT)) . '</td>';

				echo '</tr>';
			} catch (Exception $e) {
				// Continue
			}
		}
	}

	echo '
		</tbody>
	</table>
	';
}

?>

<!-- Tabs -->
<ul class="nav nav-tabs ps-3" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="standard-tab" data-bs-toggle="tab" href="#standard" role="tab" aria-controls="standard" aria-selected="true"><?php $L->p('Standard') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="static-tab" data-bs-toggle="tab" href="#static" role="tab" aria-controls="static" aria-selected="true"><?php $L->p('Static') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="sticky-tab" data-bs-toggle="tab" href="#sticky" role="tab" aria-controls="sticky" aria-selected="true"><?php $L->p('Sticky') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="scheduled-tab" data-bs-toggle="tab" href="#scheduled" role="tab" aria-controls="scheduled" aria-selected="true"><?php $L->p('Scheduled') ?>
			<?php if (count($scheduled) > 0) {
				echo '<span class="badge badge-danger">' . count($scheduled) . '</span>';
			} ?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="unlisted-tab" data-bs-toggle="tab" href="#unlisted" role="tab" aria-controls="unlisted" aria-selected="true"><?php $L->p('Unlisted') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="draft-tab" data-bs-toggle="tab" href="#draft" role="tab" aria-controls="draft" aria-selected="true"><?php $L->p('Draft') ?></a>
	</li>
</ul>
<!-- End Tabs -->

<!-- Content -->
<div class="tab-content">

	<!-- Tab pages -->
	<div class="tab-pane show active" id="standard" role="tabpanel">
		<?php table('published'); ?>

		<!-- Paginator -->
		<!-- The paginator is defined in the rule 99.paginator.php for the admin area -->
		<?php if (Paginator::numberOfPages() > 1) : ?>
			<nav class="mt-4 mb-4">
				<ul class="pagination flex-wrap justify-content-center">
					<!-- First button -->
					<li class="page-item <?php if (!Paginator::showPrev()) echo 'disabled' ?>">
						<a class="page-link" href="<?php echo Paginator::firstPageUrl() ?>"><i class="bi bi-arrow-left-circle"></i><?php echo $L->get('First'); ?></a>
					</li>

					<!-- Previous button -->
					<li class="page-item <?php if (!Paginator::showPrev()) echo 'disabled' ?>">
						<a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>"><?php echo $L->get('Previous'); ?></a>
					</li>

					<li class="page-item"><span class="page-link text-muted"><?php echo Paginator::currentPage() ?> / <?php echo Paginator::numberOfPages() ?></span></li>

					<!-- Next button -->
					<li class="page-item <?php if (!Paginator::showNext()) echo 'disabled' ?>">
						<a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>"><?php echo $L->get('Next'); ?></a>
					</li>

					<!-- Last button -->
					<li class="page-item <?php if (!Paginator::showNext()) echo 'disabled' ?>">
						<a class="page-link" href="<?php echo Paginator::lastPageUrl() ?>"><?php echo $L->get('Last'); ?><i class="ms-2 bi bi-arrow-right-circle"></i></a>
					</li>
				</ul>
			</nav>
		<?php endif; ?>
		<!-- End Paginator -->
	</div>
	<!-- End Tab pages -->

	<!-- TABS STATIC -->
	<div class="tab-pane" id="static" role="tabpanel">
		<?php table('static'); ?>
	</div>

	<!-- TABS STICKY -->
	<div class="tab-pane" id="sticky" role="tabpanel">
		<?php table('sticky'); ?>
	</div>

	<!-- TABS SCHEDULED -->
	<div class="tab-pane" id="scheduled" role="tabpanel">
		<?php table('scheduled'); ?>
	</div>

	<!-- TABS UNLISTED -->
	<div class="tab-pane" id="unlisted" role="tabpanel">
		<?php table('unlisted'); ?>
	</div>

	<!-- TABS DRAFT -->
	<div class="tab-pane" id="draft" role="tabpanel">
		<?php table('draft'); ?>
	</div>
</div>
<!-- End Content -->
