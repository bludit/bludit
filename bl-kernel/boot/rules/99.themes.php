<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================
$themePlugin = getPlugin($site->theme()); // Returns plugin object or False

// ============================================================================
// Functions
// ============================================================================

function buildThemes()
{
	global $site;

	$themes = array();
	$themesPaths = Filesystem::listDirectories(PATH_THEMES);

	foreach ($themesPaths as $themePath) {
		// Check if the theme is translated.
		$languageFilename = $themePath . DS . 'languages' . DS . $site->language() . '.json';
		if (!Sanitize::pathFile($languageFilename)) {
			$languageFilename = $themePath . DS . 'languages' . DS . DEFAULT_LANGUAGE_FILE;
		}

		if (Sanitize::pathFile($languageFilename)) {
			$database = file_get_contents($languageFilename);
			$database = json_decode($database, true);
			if (empty($database)) {
				Log::set('99.themes.php' . LOG_SEP . 'Language file error on theme ' . $themePath);
				break;
			}

			$database = $database['theme-data'];

			$database['dirname'] = basename($themePath);

			// --- Metadata ---
			$filenameMetadata = $themePath . DS . 'metadata.json';

			if (Sanitize::pathFile($filenameMetadata)) {
				$metadataString = file_get_contents($filenameMetadata);
				$metadata = json_decode($metadataString, true);

				$database['compatible'] = false;
				if (!empty($metadata['compatible'])) {
					$bluditRoot = explode('.', BLUDIT_VERSION);
					$compatible = explode(',', $metadata['compatible']);
					foreach ($compatible as $version) {
						$root = explode('.', $version);
						if ($root[0] == $bluditRoot[0] && $root[1] == $bluditRoot[1]) {
							$database['compatible'] = true;
						}
					}
				}

				$database = $database + $metadata;
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
$languageFilename = THEME_DIR . 'languages' . DS . $site->language() . '.json';
if (!Sanitize::pathFile($languageFilename)) {
	$languageFilename = THEME_DIR . 'languages' . DS . DEFAULT_LANGUAGE_FILE;
}

if (Sanitize::pathFile($languageFilename)) {
	$database = file_get_contents($languageFilename);
	$database = json_decode($database, true);

	// Remote the name and description.
	unset($database['theme-data']);

	// Load words from the theme language
	if (!empty($database)) {
		$L->add($database);
	}
}
