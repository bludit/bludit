<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'tab-content')); ?>

<div class="align-middle">
	<div class="float-right mt-1">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php echo Bootstrap::pageTitle(array('title'=>$L->g('Blocks'), 'icon'=>'box')); ?>
</div>

<?php
	// Token CSRF
	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	$list = $blocks->getDB();
	foreach ($list as $blockKey => $blockData) {
		echo Bootstrap::formTitle(array('title'=>$blockData['title']));

		echo Bootstrap::formInputText(array(
			'name'=>'key[]',
			'label'=>$L->g('Key'),
			'value'=>$blockKey,
			'class'=>'',
			'placeholder'=>'',
			'tip'=>'',
			'readonly'=>true
		));

		echo Bootstrap::formInputText(array(
			'name'=>'title[]',
			'label'=>$L->g('title'),
			'value'=>$blockData['title'],
			'class'=>'',
			'placeholder'=>'',
			'tip'=>''
		));

		echo Bootstrap::formTextarea(array(
			'name'=>'value[]',
			'label'=>$L->g('Value'),
			'value'=>$blockData['value'],
			'class'=>'',
			'placeholder'=>'',
			'tip'=>'',
			'rows'=>5
		));
	}

echo Bootstrap::formClose();

?>
