<?php

// DEBUG:
$loadTime = microtime(true);

// SECURITY CONSTANT
define('BLUDIT', true);

// PHP PATHS
define('PATH_ROOT',					__DIR__.'/');
define('PATH_LANGUAGES',			PATH_ROOT.'languages/');
define('PATH_THEMES',				PATH_ROOT.'themes/');
define('PATH_PLUGINS',				PATH_ROOT.'plugins/');

define('PATH_KERNEL',				PATH_ROOT.'kernel/');
define('PATH_ABSTRACT',				PATH_ROOT.'kernel/abstract/');
define('PATH_BOOT',					PATH_ROOT.'kernel/boot/');
define('PATH_RULES',				PATH_ROOT.'kernel/boot/rules/');
define('PATH_HELPERS',				PATH_ROOT.'kernel/helpers/');

define('PATH_CONTENT',				PATH_ROOT.'content/');
define('PATH_POSTS',				PATH_CONTENT.'posts/');
define('PATH_PAGES',				PATH_CONTENT.'pages/');
define('PATH_DATABASES',			PATH_CONTENT.'databases/');
define('PATH_PLUGINS_DATABASES',	PATH_CONTENT.'databases/plugins/');

// BOOT
require(PATH_BOOT.'site.php');

// Theme init.php
if(file_exists(PATH_THEMES.$Site->theme().'/init.php'))
	include(PATH_THEMES.$Site->theme().'/init.php');

// Theme HTML
include(PATH_THEMES.$Site->theme().'/index.php');

// DEBUG: Estas funciones llamarlas despues que el usuario se logueo, en la parte de administracion.
$dbPosts->regenerate();
$dbPages->regenerate();

// DEBUG:
echo "Load time: ".(microtime(true) - $loadTime);

?>
