<?php

HTML::title(array('title'=>$L->g('Developers'), 'icon'=>'support'));

echo '<h2>PHP version: '.phpversion().'</h2>';

// Constanst defined by Bludit
$constants = get_defined_constants(true);
printTable('Constants', $constants['user']);

// Loaded extensions
printTable('Loaded extensions',get_loaded_extensions());

// Site object
printTable('$Site object database',$Site->db);
