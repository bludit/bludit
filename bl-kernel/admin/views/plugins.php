<?php

HTML::title(array('title'=>$L->g('Plugins'), 'icon'=>'puzzle-piece'));

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

foreach($plugins['all'] as $Plugin)
{
	echo '
	<tr '.($Plugin->installed()?'class="plugin-installed"':'class="plugin-notInstalled"').'>
	<td>
	<div class="plugin-name">
	';

	if($Plugin->installed()) {
		echo '<a class="uninstall" href="'.HTML_PATH_ADMIN_ROOT.'uninstall-plugin/'.$Plugin->className().'" title="'.$L->g('Deactivate').'"><i class="uk-icon-check-square-o"></i></a> ';
		if(method_exists($Plugin, 'form')) {
			echo '<a class="configure" href="'.HTML_PATH_ADMIN_ROOT.'configure-plugin/'.$Plugin->className().'" title="'.$L->g('Settings').'"><i class="uk-icon-cog settings-icon"></i></a> ';
		}
	}
	else {
		echo '<a class="install" href="'.HTML_PATH_ADMIN_ROOT.'install-plugin/'.$Plugin->className().'" title="'.$L->g('Activate').'"><i class="uk-icon-square-o"></i></a> ';
	}

	echo '
	'.$Plugin->name().'</div>
	</td>';

	echo '<td>';
	echo $Plugin->description();
	echo '</td>';
	echo '
	<td class="uk-text-center">';
	if( !$Plugin->isCompatible() ) {
		echo '<i class="uk-icon-exclamation-triangle incompatible-warning" title="This plugin is incompatible with Bludit v'.BLUDIT_VERSION.'"></i>';
	}
	echo $Plugin->version().'</td>';

	echo '
	<td class="uk-text-center"><a target="_blank" href="'.$Plugin->website().'">'.$Plugin->author().'</a></td>
	';

	echo '</tr>';
}

echo '
</tbody>
</table>
';