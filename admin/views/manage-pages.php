<h2 class="title"><i class="fa fa-file-text-o"></i> Manage Pages</h2>

<?php makeNavbar('manage'); ?>

<table class="table-bordered table-stripped">
	<thead>
		<tr>
			<th>Title</th>
			<th>Parent</th>
		</tr>
	</thead>
	<tbody>
	<?php

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
				echo '<td>'.($Page->parentKey()?NO_PARENT_CHAR:'').'<a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$Page->key().'">'.($Page->published()?'':'[DRAFT] ').($Page->title()?$Page->title():'[Empty title] ').'</a></td>';
				echo '<td>'.$parentTitle.'</td>';
				echo '</tr>';
			}
		}

	?>
	</tbody>
</table>
