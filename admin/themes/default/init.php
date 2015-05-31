<?php

function makeNavbar($type)
{
	global $layout;

	$navbar['users'] = array(
		'users'=>array('text'=>'Users'),
		'add-user'=>array('text'=>'Add new user')
	);

	$navbar['manage'] = array(
		'manage-posts'=>array('text'=>'Manage posts'),
		'manage-pages'=>array('text'=>'Manage pages')
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
