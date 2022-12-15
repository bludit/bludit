<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		// No events for the view yet
	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-bookmark"></i><?php $L->p('Categories') ?></h2>
	<div class="ms-auto">
		<a id="btnNew" class="btn btn-primary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'add-category' ?>" role="button"><i class="bi bi-plus-circle"></i><?php $L->p('Add a new category') ?></a>
	</div>
</div>

<?php

echo '
<table class="table table-striped mt-3">
	<thead>
		<tr>
			<th class="border-bottom-0" scope="col">'.$L->g('name').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('Description').'</th>
			<th class="border-bottom-0" scope="col">'.$L->g('URL').'</th>
		</tr>
	</thead>
	<tbody>
';

foreach ($categories->keys() as $key) {
	try {
		$category = new Category($key);
		echo '<tr>';
		echo '<td class="pt-4 pb-4"><i class="bi bi-bookmark"></i><a href="'.HTML_PATH_ADMIN_ROOT.'edit-category/'.$key.'">'.$category->name().'</a></td>';
		echo '<td class="pt-4 pb-4"><span>'.$category->description().'</span></td>';
		echo '<td class="pt-4 pb-4"><a href="'.$category->permalink().'">'.$category->permalink().'</a></td>';
		echo '</tr>';
	} catch (Exception $e) {
		// Continue
	}
}

echo '
	</tbody>
</table>
';
