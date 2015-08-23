<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

$theme = array(
	'name'=>'',
	'description'=>'',
	'author'=>'',
	'email'=>'',
	'website'=>'',
	'version'=>'',
	'releaseDate'=>''
);

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main
// ============================================================================

$langLocaleFile  = PATH_THEME.'languages'.DS.$Site->locale().'.json';
$langDefaultFile = PATH_THEME.'languages'.DS.'en_US.json';
$database = false;

// Check if exists locale language
if( Sanitize::pathFile($langLocaleFile) ) {
	$database = new dbJSON($langLocaleFile, false);
}
// Check if exists default language
elseif( Sanitize::pathFile($langDefaultFile) ) {
	$database = new dbJSON($langDefaultFile, false);
}

if($database!==false)
{
	$databaseArray = $database->db;

	// Theme data
	$theme = $databaseArray['theme-data'];

	// Remove theme data
	unset($databaseArray['theme-data']);

	// Add new words from language theme
	$Language->add($databaseArray);
}