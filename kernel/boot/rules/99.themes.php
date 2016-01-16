<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

function buildThemes()
{
	global $Site;

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

	return $themes;
}

// ============================================================================
// Main
// ============================================================================

// Load the language file
$languageFilename = PATH_THEME.DS.'languages'.DS.$Site->locale().'.json';
if( !Sanitize::pathFile($languageFilename) ) {
	$languageFilename = PATH_THEME.DS.'languages'.DS.'en_US.json';
}

if( Sanitize::pathFile($languageFilename) )
{
	$database = file_get_contents($languageFilename);
	$database = json_decode($database, true);

	// Remote the name and description.
	unset($database['theme-data']);

	// Load words from the theme language
	if(!empty($database)) {
		$Language->add($database);
	}
}
