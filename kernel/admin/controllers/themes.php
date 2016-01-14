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
	// Check if the theme is translated.
	$languageFilename = $themePath.DS.'languages'.DS.$Site->locale().'.json';
	if( !Sanitize::pathFile($languageFilename) ) {
		$languageFilename = $themePath.DS.'languages'.DS.'en_US.json';
	}

	if( Sanitize::pathFile($languageFilename) )
	{
		$database = file_get_contents($languageFilename);
		$database = json_decode($database, true);
		$database = $database['theme-data'];

		$database['dirname'] = basename($themePath);

		// --- Metadata ---
		$filenameMetadata = $themePath.DS.'metadata.json';

		if( Sanitize::pathFile($filenameMetadata) )
		{
			$metadataString = file_get_contents($filenameMetadata);
			$metadata = json_decode($metadataString, true);

			$database = $database + $metadata;

			// Theme data
			array_push($themes, $database);
		}
	}
}
