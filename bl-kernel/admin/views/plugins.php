<?php

HTML::title(array('title'=>$L->g('Plugins'), 'icon'=>'puzzle-piece'));

echo '
<div class="uk-button-group">
	<a class="uk-button" href="#all">ALL</a>
	<a class="uk-button" href="#plugin-installed">'.$L->g('Activate').'</a>
	<a class="uk-button" href="#plugin-notInstalled">'.$L->g('Deactivate').'</a>
</div>

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
	</td>';

	echo '<td>';
	echo $Plugin->description();
	if( !$Plugin->isCompatible() ) {
		echo '<div class="plugin-incompatible">This plugin is incompatible with Bludit v'.BLUDIT_VERSION.'</div>';
	}
	echo '</td>';

	echo '
	<td class="uk-text-center">'.$Plugin->version().'</td>
	<td class="uk-text-center"><a targe="_blank" href="'.$Plugin->website().'">'.$Plugin->author().'</a></td>
	';

	echo '</tr>';
}

echo '
</tbody>
</table> 

<script>
$(".uk-button-group a").click(function (e) {
    e.preventDefault();
    var a = $(this).attr("href");
    a = a.substr(1);
    $("tbody tr").each(function () {
        if (!$(this).hasClass(a) && a != "all")
            $(this).addClass("hide");
        else
            $(this).removeClass("hide");
    });
});
</script>';