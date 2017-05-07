<?php

HTML::title(array('title'=>$L->g('Themes'), 'icon'=>'paint-brush'));

echo '
<table class="uk-table">
<thead>
<tr>
	<th class="uk-width-1-5">'.$L->g('Name').'</th>
	<th class="uk-width-3-5">'.$L->g('Description').'</th>
	<th class="uk-text-center">'.$L->g('Version').'</th>
	<th class="uk-text-center">'.$L->g('Author').'</th>
	</tr>
</thead>
<tbody>
';

foreach($themes as $theme)
{
	echo '
	<tr '.($theme['dirname']==$Site->theme()?'class="theme-installed"':'class="theme-notInstalled"').'>
	<td>
	<div class="plugin-name">
	';

	if($theme['dirname']!=$Site->theme()) {
		echo '<a class="install" href="'.HTML_PATH_ADMIN_ROOT.'install-theme/'.$theme['dirname'].'" title="'.$L->g('Activate').'"><i class="uk-icon-square-o"></i></a> ';
	}
	else {
		echo '<i class="uk-icon-check-square-o"></i> ';
	}

	echo '
	'.$theme['name'].'</div>
	</td>';

	echo '<td>';
	echo $theme['description'];
	echo '</td>';
	echo '
	<td class="uk-text-center">';

	if( !$theme['compatible'] ) {
		echo '<i class="uk-icon-exclamation-triangle incompatible-warning" title="This theme is incompatible with Bludit v'.BLUDIT_VERSION.'"></i>';
	}
	echo $theme['version'].'</td>';

	echo '
	<td class="uk-text-center"><a target="_blank" href="'.$theme['website'].'">'.$theme['author'].'</a></td>
	';

	echo '</tr>';
}

echo '
</tbody>
</table>
';