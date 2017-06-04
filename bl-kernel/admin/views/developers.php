<?php

HTML::title(array('title'=>$L->g('Developers'), 'icon'=>'support'));

// Constanst defined by Bludit
$constants = get_defined_constants(true);
printTable('CONSTANTS', $constants['user']);

// Site object
printTable('$Site object database',$Site->db);
