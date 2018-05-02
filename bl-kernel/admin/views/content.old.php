<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Settings'), 'icon'=>'cog'));

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
		<a class="nav-link" id="scheduled-tab" data-toggle="tab" href="#scheduled" role="tab">Scheduled</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="draft-tab" data-toggle="tab" href="#draft" role="tab">Draft</a>
	</li>
</ul>
<div class="tab-content" id="myTabContent">
	<!-- TABS PAGES -->
	<div class="tab-pane show active" id="pages" role="tabpanel">
	</div>

	<!-- TABS STATIC -->
	<div class="tab-pane" id="static" role="tabpanel">
	</div>

	<!-- TABS STICKY -->
	<div class="tab-pane" id="sticky" role="tabpanel">
	</div>

	<!-- TABS SCHEDULED -->
	<div class="tab-pane" id="scheduled" role="tabpanel">
	</div>

	<!-- TABS DRAFT -->
	<div class="tab-pane" id="draft" role="tabpanel">
	</div>
</div>

?>

<?php


HTML::title(array('title'=>$L->g('Manage content'), 'icon'=>'folder'));

echo '<a href="'.HTML_PATH_ADMIN_ROOT.'new-content"><i class="uk-icon-plus"></i> '.$L->g('Add new content').'</a>';

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th>'.$L->g('Title').'</th>
';

echo '<th class="uk-text-center">'.( (ORDER_BY=='date') ? $L->g('Date') : $L->g('Position') ).'</th>';

echo '
	<th>'.$L->g('URL').'</th>
	</tr>
</thead>
<tbody>
';


function table($status, $icon='arrow-circle-o-down') {
	global $Url;
	global $Language;
	global $published;
	global $drafts;
	global $scheduled;
	global $static;
	global $sticky;

	if ($status=='published') {
		$list = $published;
	} elseif ($status=='draft') {
		$list = $drafts;
	} elseif ($status=='scheduled') {
		$list = $scheduled;
	} elseif ($status=='static') {
		$list = $static;
	} elseif ($status=='sticky') {
		$list = $sticky;
	}

	if (!empty($list)) {
		echo '<tr>
		<td style="color: #aaa; font-size: 0.9em; text-transform: uppercase;">'.$Language->g($status).'</td>
		<td></td>
		<td></td>
		</tr>';
	}

	if (ORDER_BY=='position') {
		foreach ($list as $pageKey) {
			$page = buildPage($pageKey);
			if ($page) {
				if (!$page->isChild() || $status!='published') {
					echo '<tr>
					<td>
						<a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">'
						.($page->title()?$page->title():'<span>'.$Language->g('Empty title').'</span> ')
						.'</a>
					</td>
					<td class="uk-text-center">'.$page->position().'</td>';

					$friendlyURL = Text::isEmpty($Url->filters('page')) ? '/'.$page->key() : '/'.$Url->filters('page').'/'.$page->key();
					echo '<td><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';
					echo '</tr>';

					foreach ($page->children() as $child) {
						if ($child->published()) {
						echo '<tr>
						<td>
							<a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$child->key().'">'
							.($child->title()?$child->title():'<span>'.$Language->g('Empty title').'</span> ')
							.'</a>
						</td>';

						$friendlyURL = Text::isEmpty($Url->filters('page')) ? '/'.$child->key() : '/'.$Url->filters('page').'/'.$child->key();
						echo '<td><a target="_blank" href="'.$child->permalink().'">'.$friendlyURL.'</a></td>';

						echo '<td>'.$child->position().'</td>';
						echo '</tr>';
						}
					}
				}
			}
		}
	} else {
		foreach ($list as $pageKey) {
			$page = buildPage($pageKey);
			if ($page) {
				echo '<tr>';
				echo '<td>
					<a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'"><i class="fa fa-'.$icon.'"></i> '
					.($page->title()?$page->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ')
					.'</a>
				</td>';

				echo '<td class="uk-text-center">'.( (ORDER_BY=='date') ? $page->dateRaw(ADMIN_PANEL_DATE_FORMAT) : $page->position() ).'</td>';

				$friendlyURL = Text::isEmpty($Url->filters('page')) ? '/'.$page->key() : '/'.$Url->filters('page').'/'.$page->key();
				echo '<td><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';
				echo '</tr>';
			}
		}
	}
}

if ($Url->pageNumber()==1) {
	table('draft', 'spinner');
	table('scheduled', 'clock-o');
	table('static', 'thumb-tack');
	table('sticky', 'sticky-note-o');
}
table('published', '');

echo '
</tbody>
</table>
';
?>

<!-- Paginator -->
<div id="paginator">
<ul>
	<?php
		// Show previus page link
		if(Paginator::showPrev()) {
			echo '<li><a href="'.Paginator::prevPageUrl().'" class="previous"><i class="fa fa-arrow-circle-o-left"></i> '.$Language->g('Previous').'</a></li>';
		} else {
			echo '<li class="disabled"><i class="fa fa-arrow-circle-o-left"></i> '.$Language->g('Previous').'</li>';
		}

		for($i=1; $i<=Paginator::amountOfPages(); $i++) {
			echo '<li><a href="'.Paginator::numberUrl($i).'" class="page">'.$i.'</a></li>';
		}

		// Show next page link
		if(Paginator::showNext()) {
			echo '<li><a href="'.Paginator::nextPageUrl().'" class="next">'.$Language->g('Next').' <i class="fa fa-arrow-circle-o-right"></i></a></li>';
		} else {
			echo '<li class="disabled">'.$Language->g('Next').' <i class="fa fa-arrow-circle-o-right"></i></li>';
		}
	?>
</ul>
</div>