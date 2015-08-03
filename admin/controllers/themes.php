<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if($Login->role()!=='admin') {
	Alert::set($Language->g('you-do-not-have-sufficient-permissions'));
	Redirect::page('admin', 'dashboard');
}

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main
// ============================================================================

$themes = array();
$themesPaths = Filesystem::listDirectories(PATH_THEMES);

// Load each plugin clasess
foreach($themesPaths as $themePath)
{
	$langLocaleFile  = $themePath.DS.'language'.DS.$Site->locale().'.json';
	$langDefaultFile = $themePath.DS.'language'.DS.'en_US.json';
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
		$databaseArray['theme-data']['dirname'] = basename($themePath);

		// Theme data
		array_push($themes, $databaseArray['theme-data']);
	}
}
