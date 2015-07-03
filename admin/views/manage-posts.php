<h2 class="title"><i class="fa fa-file-text-o"></i> Manage Posts</h2>

<?php makeNavbar('manage'); ?>

<table class="table-bordered table-stripped">
	<thead>
		<tr>
			<th>Title</th>
			<th>Published date</th>
			<th>Modified date</th>
		</tr>
	</thead>
	<tbody>
	<?php

		foreach($posts as $Post)
		{
			echo '<tr>';
			echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-post/'.$Post->key().'">'.($Post->published()?'':'[DRAFT] ').($Post->title()?$Post->title():'[Empty title] ').'</a></td>';
			echo '<td>'.$Post->dateCreated().'</td>';
			echo '<td>'.$Post->timeago().'</td>';
			echo '</tr>';
		}

	?>
	</tbody>
</table>
