<?php defined('BLUDIT') or die('Bludit CMS.');

/*
| Client for the plugins directory, the database with all the plugins available
| to install, https://github.com/bludit/plugins
|
| The index is downloaded and cached on disk, Bludit never downloads the index
| on a regular request, only when the admin opens the page to add a plugin and
| only when the cache is older than PLUGINS_DIRECTORY_CACHE_TTL
*/

class PluginsDirectory {

	// Absolute path to the cached index
	public static function cacheFilename()
	{
		return PATH_TMP . 'plugins-directory.json';
	}

	// Returns the amount of seconds since the cache was created, FALSE if there is no cache
	public static function cacheAge()
	{
		$filename = self::cacheFilename();
		if (!file_exists($filename)) {
			return false;
		}
		return time() - filemtime($filename);
	}

	/*
	| Returns the list of plugins from the directory, FALSE when the directory
	| is not available and there is no cache to fall back to
	|
	| @force		boolean	TRUE to ignore the cache and download the index again
	|
	| @return		array|false
	*/
	public static function getIndex($force = false)
	{
		$filename = self::cacheFilename();

		$age = self::cacheAge();
		$expired = ($age === false) || ($age > PLUGINS_DIRECTORY_CACHE_TTL);

		if ($force || $expired) {
			$content = TCP::http(PLUGINS_DIRECTORY_URL, 'GET', true, 15);
			$index = self::parse($content);
			if ($index !== false) {
				file_put_contents($filename, $content, LOCK_EX);
				return $index;
			}

			Log::set('PluginsDirectory' . LOG_SEP . 'Unable to download the plugins directory from ' . PLUGINS_DIRECTORY_URL, LOG_TYPE_ERROR);

			// The download failed, keep working with the cache when there is one
			if ($age === false) {
				return false;
			}
		}

		return self::parse(file_get_contents($filename));
	}

	/*
	| Parse and validate the index, returns the list of plugins or FALSE when
	| the content is not a valid index
	|
	| @content		string	Content of index.json
	|
	| @return		array|false
	*/
	private static function parse($content)
	{
		if (empty($content)) {
			return false;
		}

		$index = json_decode($content, true);
		if (!is_array($index) || !isset($index['plugins']) || !is_array($index['plugins'])) {
			return false;
		}

		// Refuse an index generated for a newer version of Bludit instead of
		// reading it wrong, this is what allows the format to change later
		if (!isset($index['schema']) || ($index['schema'] > PLUGINS_DIRECTORY_SCHEMA)) {
			Log::set('PluginsDirectory' . LOG_SEP . 'The plugins directory requires a newer version of Bludit.', LOG_TYPE_ERROR);
			return false;
		}

		$plugins = array();
		foreach ($index['plugins'] as $plugin) {
			if (!isset($plugin['id'], $plugin['name'], $plugin['version'], $plugin['download'], $plugin['sha256'])) {
				continue;
			}
			if (!PluginInstaller::validId($plugin['id'])) {
				continue;
			}
			$plugins[$plugin['id']] = $plugin;
		}

		return $plugins;
	}

	// Returns only the plugins compatible with this version of Bludit
	public static function getCompatible($force = false)
	{
		$plugins = self::getIndex($force);
		if ($plugins === false) {
			return false;
		}

		$compatible = array();
		foreach ($plugins as $id => $plugin) {
			if (self::isCompatible($plugin)) {
				$compatible[$id] = $plugin;
			}
		}
		return $compatible;
	}

	// Returns a plugin from the directory by its id, FALSE when it doesn't exist
	public static function getEntry($id)
	{
		$plugins = self::getIndex();
		if ($plugins === false) {
			return false;
		}
		return isset($plugins[$id]) ? $plugins[$id] : false;
	}

	/*
	| Check the field compatible of a plugin from the directory against this
	| version of Bludit, same grammar as Plugin::isCompatible()
	|
	| @plugin		array	Plugin from the directory
	|
	| @return		boolean
	*/
	public static function isCompatible($plugin)
	{
		if (empty($plugin['compatible'])) {
			return false;
		}

		$bluditRoot = explode('.', BLUDIT_VERSION);
		foreach (explode(',', $plugin['compatible']) as $version) {
			$root = explode('.', trim($version));
			if (count($root) < 2) {
				continue;
			}
			if ($root[0] == $bluditRoot[0] && $root[1] == $bluditRoot[1]) {
				return true;
			}
		}
		return false;
	}
}
