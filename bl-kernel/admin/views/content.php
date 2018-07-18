<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Content'), 'icon'=>'layers'));

function table($type) {
	global $url;
	global $Language;
	global $published;
	global $drafts;
	global $scheduled;
	global $static;
	global $sticky;

	if ($type=='published') {
		$list = $published;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $Language->g('There are not pages in this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type=='draft') {
		$list = $drafts;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $Language->g('There are not draft pages in this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type=='scheduled') {
		$list = $scheduled;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $Language->g('There are not scheduled pages in this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type=='static') {
		$list = $static;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $Language->g('There are not static pages in this moment.');
			echo '</p>';
			return false;
		}
	} elseif ($type=='sticky') {
		$list = $sticky;
		if (empty($list)) {
			echo '<p class="mt-4 text-muted">';
			echo $Language->g('There are not sticky pages in this moment.');
			echo '</p>';
			return false;
		}
	}

	echo '
	<table class="table mt-3">
		<thead>
			<tr>
				<th class="border-0" scope="col">'.$Language->g('Title').'</th>
				<th class="border-0 d-none d-lg-table-cell" scope="col">'.$Language->g('URL').'</th>
				<th class="border-0 text-center d-none d-sm-table-cell" scope="col">'.( ((ORDER_BY=='position') || ($type!='published'))?$Language->g('Position'):$Language->g('Creation date')).'</th>
			</tr>
		</thead>
		<tbody>
	';

	if (ORDER_BY=='position') {
		foreach ($list as $pageKey) {
			try {
				$page = new PageX($pageKey);
				if (!$page->isChild() || $type!='published') {
					echo '<tr>
					<td>
						<a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">'
						.($page->title()?$page->title():'<span>'.$Language->g('Empty title').'</span> ')
						.'</a>
					</td>';

					$friendlyURL = Text::isEmpty($url->filters('page')) ? '/'.$page->key() : '/'.$url->filters('page').'/'.$page->key();
					echo '<td class="d-none d-lg-table-cell"><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';

					echo '<td class="text-center d-none d-sm-table-cell">'.$page->position().'</td>';

					echo '</tr>';

					foreach ($page->children() as $child) {
						if ($child->published()) {
						echo '<tr>
						<td>
							<a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$child->key().'">'
							.($child->title()?$child->title():'<span>'.$Language->g('Empty title').'</span> ')
							.'</a>
						</td>';

						$friendlyURL = Text::isEmpty($url->filters('page')) ? '/'.$child->key() : '/'.$url->filters('page').'/'.$child->key();
						echo '<td><a target="_blank" href="'.$child->permalink().'">'.$friendlyURL.'</a></td>';

						echo '<td>'.$child->position().'</td>';

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
				$page = new PageX($pageKey);
				echo '<tr>';
				echo '<td>
					<a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">'
					.($page->title()?$page->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ')
					.'</a>
				</td>';

				$friendlyURL = Text::isEmpty($url->filters('page')) ? '/'.$page->key() : '/'.$url->filters('page').'/'.$page->key();
				echo '<td class="d-none d-lg-table-cell"><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';

				echo '<td class="text-center d-none d-sm-table-cell">'.( ((ORDER_BY=='position') || ($type!='published'))?$page->position():$page->dateRaw(ADMIN_PANEL_DATE_FORMAT) ).'</td>';

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
		<a class="nav-link active" id="pages-tab" data-toggle="tab" href="#pages" role="tab">Pages</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="static-tab" data-toggle="tab" href="#static" role="tab">Static</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="sticky-tab" data-toggle="tab" href="#sticky" role="tab">Sticky</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="scheduled-tab" data-toggle="tab" href="#scheduled" role="tab">Schedule <?php if (count($scheduled)>0) { echo '<span class="badge badge-danger">'.count($scheduled).'</span>'; } ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="draft-tab" data-toggle="tab" href="#draft" role="tab">Draft <?php if (count($drafts)>0) { echo '<span class="badge badge-danger">'.count($drafts).'</span>'; } ?></a>
	</li>
</ul>
<div class="tab-content">
	<!-- TABS PAGES -->
	<div class="tab-pane show active" id="pages" role="tabpanel">
	<?php table('published'); ?>
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
