<h2 class="title"><i class="fa fa-file-text-o"></i><?php $Language->p('Manage pages') ?></h2>

<?php makeNavbar('manage'); ?>

<form id="jsformposition" method="post" action="" class="forms">
<table class="table-bordered table-stripped">
	<thead>
		<tr>
			<th style="width: 30%"><?php $Language->p('Title') ?></th>
			<th style="width: 40%"><?php $Language->p('Parent') ?></th>
			<th style="width: 30%"><?php $Language->p('Position') ?></th>
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
				echo '<td>'.($Page->parentKey()?NO_PARENT_CHAR:'').'<a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$Page->key().'">'.($Page->published()?'':'<span class="label label-outline label-red smaller">'.$Language->g('Draft').'</span> ').($Page->title()?$Page->title():'<span class="label label-outline label-blue smaller">'.$Language->g('Empty title').'</span> ').'</a></td>';
				echo '<td>'.$parentTitle.'</td>';
				echo '<td><input id="jsposition" name="position" type="text" class="width-20" value="' .$Page->position(). '"></td>';
				echo '</tr>';
			}
		}

	?>
	</tbody>
	<tfoot>
		<tr>
			<th style="width: 30%">&nbsp;</th>
			<th style="width: 40%">&nbsp;</th>
			<th style="width: 30%"><button class="btn btn-blue" name="publish"><?php //echo $Language->p('Update') ?>Update</button></th>
		</tr>
	</tfoot>	
</table>
</form>