<?php

function makeNavbar($type)
{
	global $layout;
	global $Language;

	$navbar['users'] = array(
		'users'=>array('text'=>$Language->g('Users')),
		'add-user'=>array('text'=>$Language->g('Add a new user'))
	);

	$navbar['manage'] = array(
		'manage-posts'=>array('text'=>$Language->g('Manage posts')),
		'manage-pages'=>array('text'=>$Language->g('Manage pages'))
	);

	echo '<nav class="navbar sublinks"><ul>';
	foreach($navbar[$type] as $link=>$nav)
	{
		if($link==$layout['view'])
			echo '<li class="active">';
		else
			echo '<li>';

		echo '<a href="'.HTML_PATH_ADMIN_ROOT.$link.'">'.$nav['text'].'</a></li>';
	}
	echo '</ul></nav>';
}
