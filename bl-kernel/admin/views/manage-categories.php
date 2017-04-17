<?php

HTML::title(array('title'=>$L->g('Categories'), 'icon'=>'users'));

echo '<a href="'.HTML_PATH_ADMIN_ROOT.'new-category"><i class="uk-icon-plus"></i> '.$L->g('Add a new category').'</a>';

echo '
<table class="uk-table uk-table-striped">
<thead>
	<tr>
	<th>'.$L->g('Name').'</th>
	<th>'.$L->g('Slug').'</th>
	</tr>
</thead>
<tbody>
';

$categories = $dbCategories->getAll();
foreach($categories as $categoryKey=>$category)
{
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-category/'.$categoryKey.'">'.$category.'</a></td>';
	echo '<td>'.$categoryKey.'</td>';
	echo '</tr>';
}

echo '
</tbody>
</table>
';
