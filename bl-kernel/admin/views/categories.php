<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Categories'), 'icon'=>'grid-three-up'));

echo Bootstrap::link(array(
	'title'=>'Add a new category',
	'href'=>HTML_PATH_ADMIN_ROOT.'new-category',
	'icon'=>'plus'
));

echo '
<table class="table table-striped mt-3">
	<thead>
		<tr>
			<th class="border-bottom-0" scope="col">Name</th>
			<th class="border-bottom-0" scope="col">URL</th>
		</tr>
	</thead>
	<tbody>
';

$categories = $dbCategories->getKeyNameArray();
foreach ($categories as $categoryKey=>$category) {
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-category/'.$categoryKey.'">'.$category.'</a></td>';
	echo '<td><a href="'.DOMAIN_CATEGORIES.$categoryKey.'">'.$Url->filters('category', false).$categoryKey.'</a></td>';
	echo '</tr>';
}

echo '
	</tbody>
</table>
';
