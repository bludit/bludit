<?php

HTML::title(array('title'=>$L->g('Categories'), 'icon'=>'tag'));

echo '<a href="'.HTML_PATH_ADMIN_ROOT.'new-category"><i class="uk-icon-plus"></i> '.$L->g('Add a new category').'</a>';

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th>'.$L->g('Name').'</th>
	<th>'.$L->g('URL').'</th>
	</tr>
</thead>
<tbody>
';

$categories = $dbCategories->getKeyNameArray();
foreach($categories as $categoryKey=>$category)
{
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-category/'.$categoryKey.'">'.$category.'</a></td>';
	echo '<td><a href="'.DOMAIN_CATEGORIES.$categoryKey.'">'.$Url->filters('category', false).$categoryKey.'</a></td>';
	echo '</tr>';
}

echo '
</tbody>
</table>
';
