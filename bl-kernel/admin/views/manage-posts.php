<?php

HTML::title(array('title'=>$L->g('Manage posts'), 'icon'=>'folder'));

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th>'.$L->g('Title').'</th>
	<th class="uk-text-center">'.$L->g('Published date').'</th>
	<th>'.$L->g('Friendly URL').'</th>
	</tr>
</thead>
<tbody>
';

	foreach($posts as $Post)
	{
		$status = false;
		if($Post->scheduled()) {
			$status = $Language->g('Scheduled');
		}
		elseif(!$Post->published()) {
			$status = $Language->g('Draft');
		}

		echo '<tr>';
		echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-post/'.$Post->key().'">'.($status?'<span class="label-draft">'.$status.'</span>':'').($Post->title()?$Post->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ').'</a></td>';
		echo '<td class="uk-text-center">'.$Post->dateRaw().'</td>';
		echo '<td><a target="_blank" href="'.$Post->permalink().'">'.$Url->filters('post').'/'.$Post->key().'</a></td>';
		echo '</tr>';
	}

echo '
</tbody>
</table>
';

?>

<div id="paginator">
<ul>
<?php
	if(Paginator::get('showNewer')) {
		echo '<li class="previous"><a href="'.HTML_PATH_ADMIN_ROOT.'manage-posts?page='.Paginator::get('prevPage').'">« '.$Language->g('Prev page').'</a></li>';
	}

	echo '<li class="list">'.(Paginator::get('currentPage')+1).' / '.(Paginator::get('numberOfPages')+1).'</li>';

	if(Paginator::get('showOlder')) {
		echo '<li class="next"><a href="'.HTML_PATH_ADMIN_ROOT.'manage-posts?page='.Paginator::get('nextPage').'">'.$Language->g('Next page').' »</a></li>';
	}
?>
</ul>
</div>
