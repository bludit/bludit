<?php

HTML::title(array('title'=>$L->g('Manage content'), 'icon'=>'folder'));

echo '<a href="'.HTML_PATH_ADMIN_ROOT.'new-page"><i class="uk-icon-plus"></i> '.$L->g('Add new content').'</a>';

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

foreach($pages as $page) {
	$status = false;
	if($page->status()!='published') {
		$status = $Language->g( $page->status() );
	}
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$page->key().'">'.($status?'<span class="label-'.$page->status().'">'.$status.'</span>':'').($page->title()?$page->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ').'</a></td>';

	echo '<td class="uk-text-center">'.( (ORDER_BY=='date') ? $page->dateRaw() : $page->position() ).'</td>';

	$friendlyURL = Text::isEmpty($Url->filters('page')) ? '/'.$page->key() : '/'.$Url->filters('page').'/'.$page->key();
	echo '<td><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';
	echo '</tr>';
}

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
			echo '<li class="first"><a href="'.Paginator::prevPageUrl().'" class="previous"><- Previous</a></li>';
		}

		for($i=1; $i<=Paginator::amountOfPages(); $i++) {
			echo '<li><a href="'.Paginator::numberUrl($i).'" class="page">'.$i.'</a></li>';
		}

		// Show next page link
		if(Paginator::showNext()) {
			echo '<li class="next"><a href="'.Paginator::nextPageUrl().'" class="next">Next -></a></li>';
		}
	?>
</ul>
</div>
