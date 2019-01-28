<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Content'), 'icon'=>'layers'));

function table($type) {
	global $url;
	global $L;
	global $published;
	global $drafts;
	global $scheduled;
	global $static;
	global $sticky;

	if ($type=='published') {
		$list = $published;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $L->g('There are no pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type=='draft') {
		$list = $drafts;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $L->g('There are no draft pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type=='scheduled') {
		$list = $scheduled;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $L->g('There are no scheduled pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type=='static') {
		$list = $static;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $L->g('There are no static pages at this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type=='sticky') {
		$list = $sticky;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $L->g('There are no sticky pages at this moment.');
			echo '</p>';
			return false;
		}
	}

	echo '
	<table class="table mt-3">
		<thead>
			<tr>
				<th style="font-size: 0.8em;" class="border-0 text-uppercase text-muted" scope="col">'.$L->g('Title').'</th>
				<th style="font-size: 0.8em;" class="border-0 d-none d-lg-table-cell text-uppercase text-muted" scope="col">'.$L->g('URL').'</th>
				<th style="font-size: 0.8em;" class="border-0 text-center d-none d-sm-table-cell text-uppercase text-muted" scope="col">'.$L->g('Actions').'</th>
			</tr>
		</thead>
		<tbody>
	';

	if (ORDER_BY=='position') {
		foreach ($list as $pageKey) {
			try {
				$page = new Page($pageKey);
				if (!$page->isChild() || $type!='published') {
					echo '<tr>
					<td>
						<div>
							<a style="font-size: 1.1em" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">'
							.($page->title()?$page->title():'<span class="label-empty-title">'.$L->g('Empty title').'</span> ')
							.'</a>
						</div>
						<div>
							<p style="font-size: 0.8em" class="m-0 text-uppercase text-muted">'.( ((ORDER_BY=='position') || ($type!='published'))?$L->g('Position').': '.$page->position():$page->date(MANAGE_CONTENT_DATE_FORMAT) ).'</p>
						</div>
					</td>';

					$friendlyURL = Text::isEmpty($url->filters('page')) ? '/'.$page->key() : '/'.$url->filters('page').'/'.$page->key();
					echo '<td class="d-none d-lg-table-cell"><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';

					echo '<td class="contentTools pt-3 text-center d-sm-table-cell w-25">'.PHP_EOL;
					echo '<a class="btn btn-outline-secondary btn-sm mb-1" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'"><span class="oi oi-pencil"></span> '.$L->g('Edit').'</a>'.PHP_EOL;
					if (count($page->children())==0) {
						echo '<button type="button" class="btn btn-outline-danger btn-sm deletePageButton mb-1" data-toggle="modal" data-target="#jsdeletePageModal" data-key="'.$page->key().'"><span class="oi oi-trash"></span> '.$L->g('Delete').'</button>'.PHP_EOL;
					}
					echo '</td>';

					echo '</tr>';

					foreach ($page->children() as $child) {
						if ($child->published()) {
						echo '<tr>
						<td class="child">
							<div>
								<a style="font-size: 1.1em" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$child->key().'">'
								.($child->title()?$child->title():'<span class="label-empty-title">'.$L->g('Empty title').'</span> ')
								.'</a>
							</div>
							<div>
								<p style="font-size: 0.8em" class="m-0 text-uppercase text-muted">'.( ((ORDER_BY=='position') || ($type!='published'))?$L->g('Position').': '.$child->position():$child->date(MANAGE_CONTENT_DATE_FORMAT) ).'</p>
							</div>
						</td>';

						$friendlyURL = Text::isEmpty($url->filters('page')) ? '/'.$child->key() : '/'.$url->filters('page').'/'.$child->key();
						echo '<td><a target="_blank" href="'.$child->permalink().'">'.$friendlyURL.'</a></td>';

						echo '<td class="contentTools pt-3 text-center d-sm-table-cell w-25">'.PHP_EOL;
						echo '<a class="btn btn-outline-secondary btn-sm mb-1" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$child->key().'"><span class="oi oi-pencil"></span> '.$L->g('Edit').'</a>'.PHP_EOL;
						echo '<button type="button" class="btn btn-outline-danger btn-sm deletePageButton mb-1" data-toggle="modal" data-target="#jsdeletePageModal" data-key="'.$child->key().'"><span class="oi oi-trash"></span> '.$L->g('Delete').'</button>'.PHP_EOL;
						echo '</td>';

						echo '</tr>';
						}
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
				echo '<tr>';
				echo '<td class="pt-3">
					<div>
						<a style="font-size: 1.1em" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">'
						.($page->title()?$page->title():'<span class="label-empty-title">'.$L->g('Empty title').'</span> ')
						.'</a>
					</div>
					<div>
						<p style="font-size: 0.8em" class="m-0 text-uppercase text-muted">'.( ($type=='scheduled')?$L->g('Scheduled').': '.$page->date(SCHEDULED_DATE_FORMAT):$page->date(MANAGE_CONTENT_DATE_FORMAT) ).'</p>
					</div>
				</td>';

				$friendlyURL = Text::isEmpty($url->filters('page')) ? '/'.$page->key() : '/'.$url->filters('page').'/'.$page->key();
				echo '<td class="pt-3 d-none d-lg-table-cell"><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';

				echo '<td class="contentTools pt-3 text-center d-sm-table-cell w-25">'.PHP_EOL;
				echo '<a class="btn btn-outline-secondary btn-sm mb-1" href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'"><span class="oi oi-pencil"></span> '.$L->g('Edit').'</a>'.PHP_EOL;
				if (count($page->children())==0) {
					echo '<button type="button" class="btn btn-outline-danger btn-sm deletePageButton mb-1" data-toggle="modal" data-target="#jsdeletePageModal" data-key="'.$page->key().'"><span class="oi oi-trash"></span> '.$L->g('Delete').'</button>'.PHP_EOL;
				}
				echo '</td>';

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

<!-- TABS -->
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="pages-tab" data-toggle="tab" href="#pages" role="tab"><?php $L->p('Pages') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="static-tab" data-toggle="tab" href="#static" role="tab"><?php $L->p('Static') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="sticky-tab" data-toggle="tab" href="#sticky" role="tab"><?php $L->p('Sticky') ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="scheduled-tab" data-toggle="tab" href="#scheduled" role="tab"><?php $L->p('Scheduled') ?> <?php if (count($scheduled)>0) { echo '<span class="badge badge-danger">'.count($scheduled).'</span>'; } ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="draft-tab" data-toggle="tab" href="#draft" role="tab"><?php $L->p('Draft') ?> <?php if (count($drafts)>0) { echo '<span class="badge badge-danger">'.count($drafts).'</span>'; } ?></a>
	</li>
</ul>
<div class="tab-content">
	<!-- TABS PAGES -->
	<div class="tab-pane show active" id="pages" role="tabpanel">
		<?php table('published'); ?>

		<?php if (Paginator::numberOfPages() > 1): ?>
		<!-- Paginator -->
		<nav class="paginator">
			<ul class="pagination flex-wrap justify-content-center">

			<!-- First button -->
			<li class="page-item <?php if (!Paginator::showPrev()) echo 'disabled' ?>">
				<a class="page-link" href="<?php echo Paginator::firstPageUrl() ?>"><span class="align-middle oi oi-media-skip-backward"></span> <?php echo $L->get('First'); ?></a>
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
				<a class="page-link" href="<?php echo Paginator::lastPageUrl() ?>"><?php echo $L->get('Last'); ?> <span class="align-middle oi oi-media-skip-forward"></span></a>
			</li>

			</ul>
		</nav>
		<?php endif; ?>
	</div>

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

	<!-- TABS DRAFT -->
	<div class="tab-pane" id="draft" role="tabpanel">
	<?php table('draft'); ?>
	</div>
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
	$(".deletePageButton").on("click", function() {
		key = $(this).data('key');
	});

	// Event from button accept from the modal
	$(".deletePageModalAcceptButton").on("click", function() {

		var form = jQuery('<form>', {
			'action': HTML_PATH_ADMIN_ROOT+'edit-content/'+key,
			'method': 'post',
			'target': '_top'
		}).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'tokenCSRF',
			'value': tokenCSRF
		}).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'key',
			'value': key
		}).append(jQuery('<input>', {
			'type': 'hidden',
			'name': 'type',
			'value': 'delete'
		}))));

		form.hide().appendTo("body").submit();
	});
});
</script>

<script>
	// Open the tab defined in the URL
	const anchor = window.location.hash;
	$(`a[href="${anchor}"]`).tab('show');
</script>