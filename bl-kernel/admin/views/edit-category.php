<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Edit Category'), 'icon'=>'tags'));

echo Bootstrap::formOpen(array('id'=>'jsform'));

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo Bootstrap::formInputHidden(array(
		'name'=>'action',
		'value'=>'edit'
	));

	echo Bootstrap::formInputHidden(array(
		'name'=>'oldKey',
		'value'=>$categoryMap['key']
	));

	echo Bootstrap::formInputText(array(
		'name'=>'name',
		'label'=>$L->g('Name'),
		'value'=>$categoryMap['name'],
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formTextarea(array(
		'name'=>'description',
		'label'=>$L->g('Description'),
		'value'=>isset($categoryMap['description'])?$categoryMap['description']:'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>'',
		'rows'=>3
	));

	echo Bootstrap::formInputText(array(
		'name'=>'template',
		'label'=>$L->g('Template'),
		'value'=>isset($categoryMap['template'])?$categoryMap['template']:'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'newKey',
		'label'=>$L->g('Friendly URL'),
		'value'=>$categoryMap['key'],
		'class'=>'',
		'placeholder'=>'',
		'tip'=>DOMAIN_CATEGORIES.$categoryMap['key']
	));

	echo '
	<div class="form-group mt-4">
		<button type="submit" class="btn btn-primary">'.$L->g('Save').'</button>
		<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#jsdeleteModal">'.$L->g('Delete').'</button>
		<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'categories" role="button">'.$L->g('Cancel').'</a>
	</div>
	';

echo Bootstrap::formClose();

?>

<!-- Modal for delete category -->
<?php
	echo Bootstrap::modal(array(
		'buttonPrimary'=>$L->g('Delete'),
		'buttonPrimaryClass'=>'jsbuttonDeleteAccept',
		'buttonSecondary'=>$L->g('Cancel'),
		'buttonSecondaryClass'=>'',
		'modalTitle'=>$L->g('Delete category'),
		'modalText'=>$L->g('Are you sure you want to delete this category?'),
		'modalId'=>'jsdeleteModal'
	));
?>
<script>
$(document).ready(function() {
	// Delete content
	$(".jsbuttonDeleteAccept").on("click", function() {
		$("#jsaction").val("delete");
		$("#jsform").submit();
	});
});
</script>