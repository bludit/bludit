<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Tags'), 'icon'=>'tags'));

echo Bootstrap::link(array(
	'title'=>$L->g('Add a new tag'),
	'href'=>HTML_PATH_ADMIN_ROOT.'new-tag',
	'icon'=>'plus'
));

echo '
<table class="table table-striped mt-3">
	<thead>
		<tr>
			<th class="border-bottom-0" scope="col">'.$L->g('Name').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('URL').'</th>
		</tr>
	</thead>
	<tbody>
';

foreach ($tags->keys() as $key) {
	$tag = new Tag($key);
	echo '<tr>';
	echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-tag/'.$key.'">'.$tag->name().'</a></td>';
	echo '<td><a href="'.$tag->permalink().'">'.$url->filters('tag', false).$key.'</a></td>';
	echo '</tr>';
}

echo '
	</tbody>
</table>
';
