<?php

HTML::title(array('title'=>$L->g('Plugin Sort'), 'icon'=>'puzzle-piece'));

foreach(array_keys($plugins) as $key) {
	if ($key != 'all' and count($plugins[$key]) > 0) {
		sortPlugins($plugins[$key], $key);
		HTML::formOpen(array('class'=>'uk-form-horizontal'));
		HTML::formInputHidden(
			array('name'=>'tokenCSRF','value'=>$Security->getTokenCSRF())
		);
		HTML::formInputHidden(array('name'=>'pluginList','value'=>$key));
		echo '<table class="uk-table">'."\n";
		echo '<thead>'."\n";
		echo "\t".'<tr><th>'.$key.'</th></tr>'."\n";
		echo '</thead>'."\n";
		echo '<tbody class="sortable">'."\n";
		foreach($plugins[$key] as $plugin) {
			print "\t".'<tr><td><span class="handle">&equiv;</span>';
			HTML::formInputHidden(array('name'=>$plugin->directoryName, 'value'=>''));
			echo $plugin->name().'</td></tr>'."\n";
		}
		echo '</tbody>'."\n";
		echo '</table>'."\n";
		echo '<button type="submit" class="uk-button">'.$L->g('Save').'</button>'."\n";
		HTML::formClose(false);
	}
}
?>
<script src="<?php echo HTML_PATH_ADMIN_THEME_JS.'jquery-ui.min.js'; ?>"></script>
<script>
$(function() {
	jQuery(".sortable").sortable({ handle: ".handle" });
});
</script>