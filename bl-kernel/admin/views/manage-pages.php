<?php

HTML::title(array('title'=>$L->g('Manage pages'), 'icon'=>'folder'));

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th>'.$L->g('Title').'</th>
	<th class="uk-text-center">'.$L->g('Position').'</th>
	<th>'.$L->g('Friendly URL').'</th>
	</tr>
</thead>
<tbody>
';

	unset($pagesParents[NO_PARENT_CHAR]);

	foreach($pagesParents as $parentKey=>$pageList)
	{
		// Parent page
		$Page = $pages[$parentKey];

		$friendlyURL = Text::isEmpty($Url->filters('page')) ? '/'.$Page->key() : '/'.$Url->filters('page').'/'.$Page->key();

		echo '<tr>';
		echo '<td>';
		echo '<a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$Page->key().'">'.($Page->published()?'':'<span class="label-draft">'.$Language->g('Draft').'</span> ').($Page->title()?$Page->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ').'</a>';
		echo '</td>';
		echo '<td class="uk-text-center">'.$Page->position().'</td>';
		echo '<td><a target="_blank" href="'.$Page->permalink().'">'.$friendlyURL.'</a></td>';
		echo '</tr>';

		// Children
		foreach($pageList as $Page)
		{
			$friendlyURL = Text::isEmpty($Url->filters('page')) ? '/'.$Page->key() : '/'.$Url->filters('page').'/'.$Page->key();

			echo '<tr class="children">';
			echo '<td class="children">';
			echo '<a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$Page->key().'">'.($Page->published()?'':'<span class="label-draft">'.$Language->g('Draft').'</span> ').($Page->title()?$Page->title():'<span class="label-empty-title">'.$Language->g('Empty title').'</span> ').'</a>';
			echo '</td>';
			echo '<td class="uk-text-center">'.$Page->position().'</td>';
			echo '<td><a target="_blank" href="'.$Page->permalink().'">'.$friendlyURL.'</a></td>';
			echo '</tr>';
		}
	}

echo '
</tbody>
</table>
';
