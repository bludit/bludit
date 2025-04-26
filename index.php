<?php

/*
 * Bludit
 * https://www.bludit.com
 * Author Diego Najar
 * Bludit is opensource software licensed under the MIT license.
*/

// Check if Bludit is installed
global $url;
if (!file_exists('bl-content/databases/site.php')) {
	$base = dirname($_SERVER['SCRIPT_NAME']);
	$base = rtrim($base, '/');
	$base = rtrim($base, '\\'); // Workaround for Windows Servers
	header('Location:' . $base . '/install.php');
	exit('<a href="./install.php">Install Bludit first.</a>');
}

// Load time init
$loadTime = microtime(true);

// Security constant
const BLUDIT = true;

// Directory separator
const DS = DIRECTORY_SEPARATOR;

// PHP paths for init
const PATH_ROOT = __DIR__ . DS;
const PATH_BOOT = PATH_ROOT . 'bl-kernel' . DS . 'boot' . DS;

// Init
require(PATH_BOOT . 'init.php');

// Admin area
if ($url->whereAmI() === 'admin') {
	require(PATH_BOOT . 'admin.php');
}
// Site
else {
	require(PATH_BOOT . 'site.php');
}
