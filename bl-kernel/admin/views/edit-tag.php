<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform')); ?>

<div class="align-middle">
	<div class="float-right mt-1">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#jsdeleteModal"><?php $L->p('Delete') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php echo Bootstrap::pageTitle(array('title'=>$L->g('Edit Tag'), 'icon'=>'cog')); ?>
</div>

<?php
	// Token CSRF
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
		'value'=>$tagMap['key']
	));

	echo Bootstrap::formInputText(array(
		'name'=>'name',
		'label'=>$L->g('Name'),
		'value'=>$tagMap['name'],
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputText(array(
		'name'=>'newKey',
		'label'=>$L->g('Friendly URL'),
		'value'=>$tagMap['key'],
		'class'=>'',
		'placeholder'=>'',
		'tip'=>DOMAIN_TAGS.$tagMap['key']
	));

	echo '<h5>' . $L->g('linked-pages') . '</h5>';
	if(sizeof($tagMap['list']) > 0){
		echo Bootstrap::linkedPagesTable($tagMap['list']);
	}
	else {
		echo $L->g('no-linked-pages');
	}

echo Bootstrap::formClose();

?>

<!-- Modal for delete tag -->
<?php
	echo Bootstrap::modal(array(
		'buttonPrimary'=>$L->g('Delete'),
		'buttonPrimaryClass'=>'btn-danger jsbuttonDeleteAccept',
		'buttonSecondary'=>$L->g('Cancel'),
		'buttonSecondaryClass'=>'btn-link',
		'modalTitle'=>$L->g('Delete tag'),
		'modalText'=>$L->g('Are you sure you want to delete this tag?'),
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
