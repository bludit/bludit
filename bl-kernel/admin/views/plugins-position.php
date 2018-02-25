<?php

HTML::title(array('title'=>$L->g('Plugins Position'), 'icon'=>'puzzle-piece'));

echo '<div class="hint">'.$L->g('drag-and-drop-to-set-the-position-of-the-plugin').'</div>';

echo '<form class="uk-form" method="post" action="" autocomplete="off">';

HTML::formInputHidden(array(
	'name'=>'tokenCSRF',
	'value'=>$Security->getTokenCSRF()
));

echo '<div class="uk-sortable" data-uk-sortable>';

foreach ($plugins['siteSidebar'] as $Plugin) {
	echo '<div class="plugin-position" data-plugin="'.$Plugin->className().'"><i class="uk-icon-bars"></i> '.$Plugin->name().'</div>';
}

echo '</div>';
echo '<input id="plugin-list" name="plugin-list" type="text" value="" hidden/>';
echo '<button class="uk-button uk-button-primary" type="button" id="jsSave">'.$L->g('Save').'</button>';
echo '</form>';
?>

<script>
$( document ).ready(function() {
	$("#jsSave").on("click", function() {
		var tmp = [];
		$( "div.plugin-position" ).each(function() {
			tmp.push( $(this).attr("data-plugin") );
		});
		console.log( tmp.join(",") );
		$("#plugin-list").attr("value", tmp.join(",") );
		$(".uk-form").submit();
	});
});
</script>