<?php defined('BLUDIT') or die('Bludit CMS.');

// Init scripts for the theme

// This theme use the API to work
if (!isPluginActive('pluginAPI')) {
    activatePlugin('pluginAPI');
}
