<h2 class="title"><i class="fa fa-adjust"></i> <?php $Language->p('Themes') ?></h2>

<?php
	foreach($themes as $theme)
	{
		$installed = '';
		if($theme['dirname']==$Site->theme()) {
			$installed = 'themeBoxInstalled';
		}

		echo '<div class="themeBox '.$installed.'">';

		echo '<p class="name">'.$theme['name'].'</p>';
		echo '<p>'.$theme['description'].'</p>';
		echo '<span class="version">'.$Language->g('Version').': '.$theme['version'].'</span><span class="author">'.$Language->g('author').': '.$theme['author'].'</span>';

		if($theme['dirname']!=$Site->theme()) {
			echo '<a href="'.HTML_PATH_ADMIN_ROOT.'install-theme/'.$theme['dirname'].'" class="btn btn-red btn-smaller">Install theme</a>';
		}

		echo '</div>';
	}

?>