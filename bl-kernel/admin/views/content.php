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

	if ($status=='published') {
		$list = $published;
	} elseif ($status=='draft') {
		$list = $drafts;
	} elseif ($status=='scheduled') {
		$list = $scheduled;
	} elseif ($status=='static') {
		$list = $static;
	}

	if (!empty($list)) {
		echo '<tr>
		<td style="color: #aaa; font-size: 0.9em; text-transform: uppercase;"><i class="fa fa-'.$icon.'" aria-hidden="true"></i> '.$Language->g($status).'</td>
		<td></td>
		<td></td>
		</tr>';
	}

	foreach($list as $pageKey=>$fields) {
		$page = buildPage($pageKey);
		if ($page) {
			echo '<tr>';
			echo '<td>
				<a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/'.$page->key().'">'
				.($page->title()?$page->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ')
				.'</a>
			</td>';

			echo '<td class="uk-text-center">'.( (ORDER_BY=='date') ? $page->dateRaw() : $page->position() ).'</td>';

			$friendlyURL = Text::isEmpty($Url->filters('page')) ? '/'.$page->key() : '/'.$Url->filters('page').'/'.$page->key();
			echo '<td><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';
			echo '</tr>';
		}
	}
}

if ($Url->pageNumber()==1) {
	table('draft', 'spinner');
	table('scheduled', 'clock-o');
	table('static', 'thumb-tack');
}
table('published', 'check');

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