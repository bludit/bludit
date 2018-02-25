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
	<div class="plugin-name">'.$theme['name'].'</div>
	<div class="plugin-links">
	';

	if($theme['dirname']!=$Site->theme()) {
		echo '<a class="install" href="'.HTML_PATH_ADMIN_ROOT.'install-theme/'.$theme['dirname'].'">'.$L->g('Activate').'</a>';
	}

	echo '
	</div>
	</td>';

	echo '<td>';
	echo $theme['description'];
	echo '</td>';

	echo '<td class="uk-text-center">';
	if( !$theme['compatible'] ) {
		echo '<i class="uk-icon-exclamation-triangle incompatible-warning" title="'.$L->g('This theme may not be supported by this version of Bludit').'"></i>';
	}
	echo $theme['version'];
	echo '</td>';

	echo '<td class="uk-text-center"><a targe="_blank" href="'.$theme['website'].'">'.$theme['author'].'</a></td>';

	echo '</tr>';
}

echo '
</tbody>
</table>
';