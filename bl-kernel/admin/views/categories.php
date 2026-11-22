<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Categories'), 'icon'=>'tags'));

echo Bootstrap::link(array(
	'title'=>$L->g('Add a new category'),
	'href'=>HTML_PATH_ADMIN_ROOT.'new-category',
	'icon'=>'plus'
));

echo '
<div class="table-card mt-3">
<table class="table">
	<thead>
		<tr>
			<th scope="col">'.$L->g('Name').'</th>
			<th class="text-center" scope="col">'.$L->g('Pages').'</th>
			<th scope="col">'.$L->g('URL').'</th>
		</tr>
	</thead>
	<tbody>
';

foreach ($categories->keys() as $key) {
	$category = new Category($key);
	// The list of pages is in the categories database, already in memory
	$count = isset($categories->db[$key]['list']) ? count($categories->db[$key]['list']) : 0;
	echo '<tr>';
	echo '<td class="align-middle"><a class="row-title" href="'.HTML_PATH_ADMIN_ROOT.'edit-category/'.$key.'">'.$category->name().'</a></td>';
	echo '<td class="align-middle text-center"><span class="pill-muted">'.$count.'</span></td>';
	echo '<td class="align-middle rowURL"><a target="_blank" href="'.$category->permalink().'" title="'.$L->g('View').'"><span class="content-url d-none d-md-inline">'.$url->filters('category', false).$key.'</span> <i class="fa fa-external-link"></i></a></td>';
	echo '</tr>';
}

echo '
	</tbody>
</table>
</div>
';
