<?php

HTML::title(array('title'=>$L->g('Plugins'), 'icon'=>'puzzle-piece'));

echo '
<style>
.list tr{ width: 100%; border-bottom: 1px dotted #CCC; margin-bottom: 10px; padding-bottom: 10px; }
.grid tr{ float: left; width: 20%; height: 100px; border-right: 1px dotted #CCC; border-bottom: 1px dotted #CCC; padding: 10px; }
.grid .desc, .grid thead { display:none }
.grid .plugin-name{ text-transform: uppercase; font-size: 12px }
.grid td{ border:none }
.grid div.plugin-links { display:block }
</style>
    <div class="uk-button-group">
        <button class="uk-button grid"><i class="uk-icon-th-large"></i></button>
        <button class="uk-button list"><i class="uk-icon-list"></i></button>
    </div>
    
<table class="uk-table uk-table-striped grid">
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
	<tr>
	<td>
	<div class="plugin-name">'.$Plugin->name().'</div>
	<div class="plugin-links">
	';

	if($Plugin->installed()) {
		if(method_exists($Plugin, 'form')) {
			echo '<a class="configure" href="'.HTML_PATH_ADMIN_ROOT.'configure-plugin/'.$Plugin->className().'">'.$L->g('Configure').'</a>';
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
	<td class="desc">'.$Plugin->description().'</td>
	<td class="uk-text-center">'.$Plugin->version().'</td>
	<td class="uk-text-center"><a targe="_blank" href="'.$Plugin->website().'">'.$Plugin->author().'</a></td>
	';

	echo '</tr>';
}

echo '
</tbody>
</table>
<script>
$("button").on("click",function(e) {
    if ($(this).hasClass("grid")) {
        $(".uk-table").removeClass("list").addClass("grid");
    }
    else if($(this).hasClass("list")) {
        $(".uk-table").removeClass("grid").addClass("list");
    }
});
</script>';