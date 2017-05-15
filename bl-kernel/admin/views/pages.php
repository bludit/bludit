<?php

HTML::title(array('title'=>$L->g('Manage pages'), 'icon'=>'folder'));

echo '<a href="'.HTML_PATH_ADMIN_ROOT.'new-page"><i class="uk-icon-plus"></i> '.$L->g('Add a new page').'</a>';

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th>'.$L->g('Title').'</th>
	<th class="uk-text-center">'.$L->g('Position').'</th>
	<th>'.$L->g('URL').'</th>
	</tr>
</thead>
<tbody>
';

foreach($pages as $page)
{
	$status = false;
	if($page->scheduled()) {
		$status = $Language->g('Scheduled');
	}
	elseif(!$page->published()) {
		$status = $Language->g('Draft');
	}
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$page->key().'">'.($status?'<span class="label-draft">'.$status.'</span>':'').($page->title()?$page->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ').'</a></td>';
	echo '<td class="uk-text-center">'.$page->dateRaw().'</td>';
	$friendlyURL = Text::isEmpty($Url->filters('page')) ? '/'.$page->key() : '/'.$Url->filters('page').'/'.$page->key();
	echo '<td><a target="_blank" href="'.$page->permalink().'">'.$friendlyURL.'</a></td>';
	echo '</tr>';
}

echo '
</tbody>
</table>
';