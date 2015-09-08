<h2 class="title"><i class="fa fa-file-text-o"></i><?php $Language->p('Manage posts') ?></h2>

<?php makeNavbar('manage'); ?>

<table class="table-bordered table-stripped">
	<thead>
		<tr>
			<th><?php $Language->p('Title') ?></th>
			<th><?php $Language->p('Published date') ?></th>
		</tr>
	</thead>
	<tbody>
	<?php

		foreach($posts as $Post)
		{
			$status = false;
			if($Post->scheduled()) {
				$status = $Language->g('Scheduled');
			}
			elseif(!$Post->published()) {
				$status = $Language->g('Draft');
			}

			echo '<tr>';
			echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'edit-post/'.$Post->key().'">'.($status?'<span class="label label-outline label-red smaller">'.$status.'</span>':'').($Post->title()?$Post->title():'<span class="label label-outline label-blue smaller">'.$Language->g('Empty title').'</span> ').'</a></td>';
			echo '<td>'.$Post->date().'</td>';
			echo '</tr>';
		}

	?>
	</tbody>
</table>

<div id="paginator">
<ul>
	<?php
		if(Paginator::get('showNewer')) {
			echo '<li class="left"><a href="'.HTML_PATH_ADMIN_ROOT.'manage-posts?page='.Paginator::get('prevPage').'">« '.$Language->g('Prev page').'</a></li>';
		}

		echo '<li class="list">'.(Paginator::get('currentPage')+1).' / '.(Paginator::get('numberOfPages')+1).'</li>';

		if(Paginator::get('showOlder')) {
			echo '<li class="right"><a href="'.HTML_PATH_ADMIN_ROOT.'manage-posts?page='.Paginator::get('nextPage').'">'.$Language->g('Next page').' »</a></li>';
		}
	?>
</ul>
</div>