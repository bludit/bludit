<h2 class="title"><i class="fa fa-rocket"></i><?php $Language->p('Plugins') ?></h2>

<?php
	foreach($plugins['all'] as $Plugin)
	{
		echo '<div class="pluginBox">';

		echo '<p class="name">'.$Plugin->name().'</p>';
		echo '<p>'.$Plugin->description().'</p>';
		echo '<span class="version">'.$Language->g('Version').': '.$Plugin->version().'</span><span class="author">'.$Language->g('author').': <a targe="_blank" href="'.$Plugin->website().'">'.$Plugin->author().'</a></span>';

		if($Plugin->installed()) {
			if(method_exists($Plugin, 'form')) {
				echo '<a href="'.HTML_PATH_ADMIN_ROOT.'configure-plugin/'.$Plugin->className().'" class="btn btn-smaller">'.$Language->g('Configure plugin').'</a>';
			}
			echo '<a href="'.HTML_PATH_ADMIN_ROOT.'uninstall-plugin/'.$Plugin->className().'" class="btn btn-red btn-smaller">'.$Language->g('Uninstall plugin').'</a>';
		}
		else {
			echo '<a href="'.HTML_PATH_ADMIN_ROOT.'install-plugin/'.$Plugin->className().'" class="btn btn-blue btn-smaller">'.$Language->g('Install plugin').'</a>';
		}

		echo '</div>';
	}
?>