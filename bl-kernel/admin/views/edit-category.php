<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-bookmark"></i><?php $L->p('Edit category') ?></h2>
	<div class="ms-auto">
		<button id="btnSave" type="button" class="btn btn-primary btn-sm"><?php $L->p('Save') ?></button>
		<button id="btnDelete" type="button" class="btn btn-danger btn-sm"><?php $L->p('Delete') ?></button>
		<a id="btnCancel" class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'categories' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
</div>

<?php
echo Bootstrap::formInputHidden(array(
	'id' => 'key',
	'name' => 'key',
	'value' => $categoryMap['key']
));

echo Bootstrap::formInputText(array(
	'id' => 'name',
	'name' => 'name',
	'label' => $L->g('Name'),
	'value' => $categoryMap['name']
));

echo Bootstrap::formTextarea(array(
	'name' => 'description',
	'label' => $L->g('Description'),
	'value' => isset($categoryMap['description']) ? $categoryMap['description'] : '',
	'rows' => 3
));

echo Bootstrap::formInputText(array(
	'name' => 'template',
	'label' => $L->g('Template'),
	'value' => isset($categoryMap['template']) ? $categoryMap['template'] : ''
));

echo Bootstrap::formInputText(array(
	'name' => 'friendlyURL',
	'label' => $L->g('Friendly URL'),
	'value' => $categoryMap['key'],
	'tip' => DOMAIN_CATEGORIES . $categoryMap['key']
));
?>

<script>
	$(document).ready(function() {
		$('#btnSave').on('click', function() {
			var name = $('#name').val();
			var friendlyURL = $('#friendlyURL').val();

			if ((name.length < 1) || (friendlyURL.length < 1)) {
				showAlertError("<?php $L->p('Complete all fields') ?>");
				return false;
			}

			var args = {
				key: $('#key').val(),
				name: name,
				description: $('#description').val(),
				friendlyURL: $('#friendlyURL').val(),
				template: $('#template').val()
			};
			api.editCategory(args).then(function(key) {
				logs('Category edited. Key: ' + key);
				showAlertInfo("<?php $L->p('The changes have been saved') ?>");
			});
			return true;
		});

		$('#btnDelete').on('click', function() {
			var key = $('#key').val();
			logs('Deleting category. Key: ' + key);
			var args = {
				key: key
			};
			api.deleteCategory(args).then(function(key) {
				logs('Category deleted. Key: ' + key);
				window.location.replace(HTML_PATH_ADMIN_ROOT + 'categories');
			});
			return true;
		});
	});
</script>