<h2 class="title"><i class="fa fa-file-text-o"></i> <?php $Language->p('Manage posts') ?></h2>

<?php makeNavbar('manage'); ?>

<table class="table-bordered table-stripped">
	<thead>
		<tr>
			<th><?php $Language->p('Title') ?></th>
			<th><?php $Language->p('Published date') ?></th>
			<th><?php $Language->p('Modified date') ?></th>
		</tr>
	</thead>
	<tbody>
	<?php

		foreach($posts as $Post)
		{
			echo '<tr>';
			echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-post/'.$Post->key().'">'.($Post->published()?'':'['.$Language->g('Draft').'] ').($Post->title()?$Post->title():'['.$Language->g('Empty title').'] ').'</a></td>';
			echo '<td>'.$Post->dateCreated().'</td>';
			echo '<td>'.$Post->timeago().'</td>';
			echo '</tr>';
		}

	?>
	</tbody>
</table>