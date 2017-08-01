<?php

HTML::title(array('title'=>$L->g('Manage content'), 'icon'=>'folder'));

echo '<a href="'.HTML_PATH_ADMIN_ROOT.'new-page"><i class="uk-icon-plus"></i> '.$L->g('Add new content').'</a>';

// Fixed page list
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
	global $pages;
	global $Url;
	$showLegend = true;
	foreach ($pages as $key=>$page) {
		if ($page->status()==$status) {
			if ($showLegend) {
				$showLegend = false;
				echo '<tr>
				<td style="color: #aaa; font-size: 0.9em; text-transform: uppercase;"><i class="fa fa-'.$icon.'" aria-hidden="true"></i> '.$status.'</td>
				<td></td>
				<td></td>
				</tr>';
			}
			unset($pages[$key]);
			echo '<tr>';
			echo '<td>
				<a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$page->key().'">'
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

table('draft', 'spinner');
table('scheduled', 'clock-o');
table('fixed', 'thumb-tack');
table('sticky', 'sticky-note-o');
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
			echo '<li class="first"><a href="'.Paginator::prevPageUrl().'" class="previous"><i class="fa fa-arrow-circle-o-left"></i> Previous</a></li>';
		}

		for($i=1; $i<=Paginator::amountOfPages(); $i++) {
			echo '<li><a href="'.Paginator::numberUrl($i).'" class="page">'.$i.'</a></li>';
		}

		// Show next page link
		if(Paginator::showNext()) {
			echo '<li class="next"><a href="'.Paginator::nextPageUrl().'" class="next">Next <i class="fa fa-arrow-circle-o-right"></i></a></li>';
		}
	?>
</ul>
</div>
