<?php

HTML::title(array('title'=>$L->g('Developers'), 'icon'=>'support'));

echo '<h2>PHP version: '.phpversion().'</h2>';

// PHP Ini
$uploadOptions = array(
	'upload_max_filesize'=>ini_get('upload_max_filesize'),
	'post_max_size'=>ini_get('post_max_size'),
	'upload_tmp_dir'=>ini_get('upload_tmp_dir')
);
printTable('File Uploads', $uploadOptions);

// Loaded extensions
printTable('Server information ( $_SERVER )', $_SERVER);

// PHP Ini
printTable('PHP Configuration options ( ini_get_all() )', ini_get_all());

// Loaded extensions
printTable('Loaded extensions',get_loaded_extensions());

// Locales installed
exec('locale -a', $locales);
printTable('Locales installed', $locales);

echo '<hr>';
echo '<h2>BLUDIT</h2>';
echo '<hr>';

// Constanst defined by Bludit
$constants = get_defined_constants(true);
printTable('Bludit Constants', $constants['user']);

// Site object
printTable('$Site object database',$Site->db);

