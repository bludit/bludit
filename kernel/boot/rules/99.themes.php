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

// Theme meta data from English
if( Sanitize::pathFile($langDefaultFile) ) {
	$database = new dbJSON($langDefaultFile, false);
	$themeMetaData = $database->db['theme-data'];
}

// Check if exists locale language
if( Sanitize::pathFile($langLocaleFile) ) {
	$database = new dbJSON($langLocaleFile, false);
}

if($database!==false)
{
	$databaseArray = $database->db;

	// Theme data
	$theme = $themeMetaData;

	// Remove theme meta data
	unset($databaseArray['theme-data']);

	// Add new words/phrase from language theme
	$Language->add($databaseArray);
}
