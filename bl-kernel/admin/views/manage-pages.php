<?php

HTML::title(array('title'=>$L->g('Manage pages'), 'icon'=>'folder'));

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th>'.$L->g('Title').'</th>
	<th>'.$L->g('Parent').'</th>
	<th class="uk-text-center">'.$L->g('Position').'</th>
	<th>'.$L->g('Friendly URL').'</th>
	</tr>
</thead>
<tbody>
';

	foreach($pagesParents as $parentKey=>$pageList)
	{
		foreach($pageList as $Page)
		{
			if($parentKey!==NO_PARENT_CHAR) {
				$parentTitle = $pages[$Page->parentKey()]->title();
			}
			else {
				$parentTitle = '';
			}

			echo '<tr>';
			echo '<td>'.($Page->parentKey()?'- ':'').'<a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$Page->key().'">'.($Page->published()?'':'<span class="label-draft">'.$Language->g('Draft').'</span> ').($Page->title()?$Page->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ').'</a></td>';
			echo '<td>'.$parentTitle.'</td>';
			echo '<td class="uk-text-center">'.$Page->position().'</td>';
			echo '<td><a target="_blank" href="'.$Page->permalink().'">'.$Url->filters('page').'/'.$Page->key().'</a></td>';
			echo '</tr>';
		}
	}

echo '
</tbody>
</table>
';
