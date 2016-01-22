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
	<div class="plugin-name">'.$Plugin->name().'</div>
	<div class="plugin-links">
	';

	if($Plugin->installed()) {
		if(method_exists($Plugin, 'form')) {
			echo '<a class="configure" href="'.HTML_PATH_ADMIN_ROOT.'configure-plugin/'.$Plugin->className().'">'.$L->g('Settings').'</a>';
			echo '<span class="separator"> | </span>';
		}
		echo '<a class="uninstall" href="'.HTML_PATH_ADMIN_ROOT.'uninstall-plugin/'.$Plugin->className().'">'.$L->g('Deactivate').'</a>';
	}
	else {
		echo '<a class="install" href="'.HTML_PATH_ADMIN_ROOT.'install-plugin/'.$Plugin->className().'">'.$L->g('Activate').'</a>';
	}

	echo '
	</div>
	</td>
	<td>'.$Plugin->description().'</td>
	<td class="uk-text-center">'.$Plugin->version().'</td>
	<td class="uk-text-center"><a targe="_blank" href="'.$Plugin->website().'">'.$Plugin->author().'</a></td>
	';

	echo '</tr>';
}

echo '
</tbody>
</table>
';