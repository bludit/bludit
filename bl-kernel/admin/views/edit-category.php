<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Edit Category'), 'icon'=>'grid-three-up'));

echo Bootstrap::formOpen(array());

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$Security->getTokenCSRF()
	));

	echo Bootstrap::formInputHidden(array(
		'name'=>'oldCategoryName',
		'value'=>$categoryName
	));

	echo Bootstrap::formInputHidden(array(
		'name'=>'oldCategoryKey',
		'value'=>$categoryKey
	));

	echo Bootstrap::formInputTextBlock(array(
		'name'=>'categoryName',
		'label'=>$L->g('Category name'),
		'value'=>$categoryName,
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputGroupText(array(
		'name'=>'categoryKey',
		'label'=>$L->g('Category key'),
		'labelInside'=>DOMAIN_CATEGORIES,
		'value'=>$categoryKey,
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo '
	<div class="form-group mt-4">
		<button type="submit" class="btn btn-primary mr-2" name="edit">'.$L->g('Save').'</button>
		<button type="submit" class="btn btn-secondary mr-2" name="delete">'.$L->g('Delete').'</button>
		<a class="btn btn-secondary" href="'.HTML_PATH_ADMIN_ROOT.'categories" role="button">'.$L->g('Cancel').'</a>
	</div>
	';

echo Bootstrap::formClose();
