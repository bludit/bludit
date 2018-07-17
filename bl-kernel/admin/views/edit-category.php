<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Edit Category'), 'icon'=>'tags'));

echo Bootstrap::formOpen(array());

	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo Bootstrap::formInputHidden(array(
		'name'=>'oldKey',
		'value'=>$categoryMap['key']
	));

	echo Bootstrap::formInputTextBlock(array(
		'name'=>'name',
		'label'=>$L->g('Category name'),
		'value'=>$categoryMap['name'],
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputGroupText(array(
		'name'=>'newKey',
		'label'=>$L->g('Category key'),
		'labelInside'=>DOMAIN_CATEGORIES,
		'value'=>$categoryMap['key'],
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formInputTextBlock(array(
		'name'=>'template',
		'label'=>$L->g('Category template'),
		'value'=>isset($categoryMap['template'])?$categoryMap['template']:'',
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
