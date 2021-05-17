<?php defined('BLUDIT') or die('Bludit CMS.');

// Init scripts for the theme

// This theme use the API to work
activatePlugin('pluginAPI');
$plugins['all']['pluginAPI']->newToken();
