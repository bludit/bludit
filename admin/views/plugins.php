<h2 class="title"><i class="fa fa-rocket"></i> Plugins</h2>

<?php
	foreach($plugins['all'] as $Plugin)
	{
		echo '<div class="pluginBox">';

		echo '<p>'.$Plugin->name().'</p>';
		echo '<p>'.$Plugin->description().'</p>';

		if($Plugin->installed()) {
			echo '<a href="'.HTML_PATH_ADMIN_ROOT.'uninstall-plugin/'.$Plugin->className().'" class="btn btn-red btn-small">Uninstall plugin</a>';
		}
		else {
			echo '<a href="'.HTML_PATH_ADMIN_ROOT.'install-plugin/'.$Plugin->className().'" class="btn btn-blue btn-small">Install plugin</a>';
		}

		echo '</div>';
	}
?>
