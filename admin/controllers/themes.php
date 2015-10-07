<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if($Login->role()!=='admin') {
	Alert::set($Language->g('you-do-not-have-sufficient-permissions'));
	Redirect::page('admin', 'dashboard');
}

// ============================================================================
// Main after POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================

$themes = array();
$themesPaths = Filesystem::listDirectories(PATH_THEMES);

foreach($themesPaths as $themePath)
{
	$langLocaleFile  = $themePath.DS.'languages'.DS.$Site->locale().'.json';
	$langDefaultFile = $themePath.DS.'languages'.DS.'en_US.json';

	// Check if exists default language
	if( Sanitize::pathFile($langDefaultFile) )
	{
		$database = new dbJSON($langDefaultFile, false);
		$databaseArray = $database->db;

		// Check if exists locale language
		if( Sanitize::pathFile($langLocaleFile) ) {
			$database = new dbJSON($langLocaleFile, false);
			$databaseArray['theme-data'] = array_merge($databaseArray['theme-data'], $database->db['theme-data']);
		}

		$databaseArray['theme-data']['dirname'] = basename($themePath);

		// Theme data
		array_push($themes, $databaseArray['theme-data']);
	}
}
