<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title'=>$L->g('Plugins position'), 'icon'=>'tags'));

echo Bootstrap::alert(array('class'=>'alert-primary', 'text'=>$L->g('Drag and Drop to sort the plugins')));

echo Bootstrap::formOpen(array('id'=>'jsform'));

	// Token CSRF
	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo Bootstrap::formInputHidden(array(
		'name'=>'plugin-list',
		'value'=>''
	));

	echo '<ul class="list-group list-group-sortable">';
	foreach ($plugins['siteSidebar'] as $Plugin) {
		echo '<li class="list-group-item" data-plugin="'.$Plugin->className().'"><span class="oi oi-move"></span> '.$Plugin->name().'</li>';
	}
	echo '</ul>';

	echo '
	<div class="form-group mt-3">
		<button type="button" class="jsbuttonSave btn btn-primary">'.$L->g('Save').'</button>
		<a href="'.HTML_PATH_ADMIN_ROOT.'plugins" class="btn btn-secondary">'.$L->g('Cancel').'</a>
	</div>
	';

echo Bootstrap::formClose();

?>

<script>
$(document).ready(function() {

	$('.list-group-sortable').sortable({
		placeholderClass: 'list-group-item'
	});

	$(".jsbuttonSave").on("click", function() {
		var tmp = [];
		$("li.list-group-item").each(function() {
			tmp.push( $(this).attr("data-plugin") );
		});
		$("#jsplugin-list").attr("value", tmp.join(",") );
		$("#jsform").submit();
	});
});
</script>