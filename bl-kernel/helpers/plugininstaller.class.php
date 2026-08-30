<?php defined('BLUDIT') or die('Bludit CMS.');

/*
| Install, update and delete plugins
|
| Every plugin is downloaded and extracted inside a staging directory in
| PATH_TMP and it's fully validated there, nothing is copied to PATH_PLUGINS
| until the content is known to be a valid plugin, this is the only way to
| avoid a broken plugin breaking the site
*/

class PluginInstaller {

	// Last error message, to show it to the user
	public static $error = '';

	private static function fail($message, $logMessage = false)
	{
		self::$error = $message;
		Log::set('PluginInstaller' . LOG_SEP . ($logMessage === false ? $message : $logMessage), LOG_TYPE_ERROR);
		return false;
	}

	// A plugin id is the name of the directory inside bl-plugins
	public static function validId($id)
	{
		return (bool) preg_match('/^[a-z0-9][a-z0-9-]{1,48}$/', $id);
	}

	// Returns TRUE when Bludit is able to create directories inside bl-plugins
	public static function writable()
	{
		return is_writable(PATH_PLUGINS);
	}

	// Returns TRUE when the plugin directory exists inside bl-plugins
	public static function exists($id)
	{
		return self::validId($id) && Filesystem::directoryExists(PATH_PLUGINS . $id);
	}

	// Returns TRUE when the URL is https and the host is allowed
	public static function allowedURL($url)
	{
		$parts = parse_url($url);
		if (($parts === false) || empty($parts['scheme']) || empty($parts['host'])) {
			return false;
		}
		if ($parts['scheme'] !== 'https') {
			return false;
		}
		return in_array(Text::lowercase($parts['host']), $GLOBALS['PLUGINS_DOWNLOAD_ALLOWED_HOSTS'], true);
	}

	private static function stagingDirectory()
	{
		$staging = PATH_TMP . 'plugin-install-' . uniqid() . DS;
		if (!Filesystem::mkdir($staging, true)) {
			return false;
		}
		return $staging;
	}

	private static function cleanUp($staging, $zipFile = false)
	{
		if ($zipFile !== false && file_exists($zipFile)) {
			unlink($zipFile);
		}
		if ($staging !== false && Filesystem::directoryExists($staging)) {
			Filesystem::deleteRecursive($staging);
		}
	}

	/*
	| Install a plugin from the plugins directory
	|
	| @id			string	Plugin id
	|
	| @return		boolean
	*/
	public static function installFromDirectory($id)
	{
		global $L;

		$entry = PluginsDirectory::getEntry($id);
		if ($entry === false) {
			return self::fail($L->g('The plugin is not available in the plugins directory'), 'The plugin ' . $id . ' is not in the directory.');
		}

		$zipFile = self::download($entry);
		if ($zipFile === false) {
			return false;
		}

		$installed = self::installFromZip($zipFile, $id);
		if (file_exists($zipFile)) {
			unlink($zipFile);
		}
		return $installed;
	}

	/*
	| Download the zip of a plugin from the directory and check its integrity
	| Returns the absolute path to the downloaded file, FALSE on failure
	|
	| @entry		array	Plugin from the directory
	|
	| @return		string|false
	*/
	private static function download($entry)
	{
		global $L;

		if (!self::allowedURL($entry['download'])) {
			return self::fail($L->g('The download URL of the plugin is not allowed'), 'The URL is not allowed, ' . $entry['download']);
		}

		$zipFile = PATH_TMP . 'plugin-download-' . uniqid() . '.zip';
		$bytes = TCP::downloadFile($entry['download'], $zipFile, PLUGINS_MAX_ZIP_SIZE);
		if ($bytes === false) {
			return self::fail($L->g('Unable to download the plugin'), 'Unable to download ' . $entry['download']);
		}

		// The checksum is the integrity guarantee, an asset replaced after the
		// plugin was reviewed must not be installed
		$sha256 = hash_file('sha256', $zipFile);
		if (!hash_equals(Text::lowercase($entry['sha256']), $sha256)) {
			unlink($zipFile);
			return self::fail($L->g('The plugin downloaded does not match the checksum, it may have been modified'), 'Checksum mismatch for ' . $entry['download'] . ', expected ' . $entry['sha256'] . ' got ' . $sha256);
		}

		return $zipFile;
	}

	/*
	| Install a plugin from a zip file
	|
	| @zipFile		string	Absolute path to the zip file
	| @expectedId		string	When set, the plugin id must match this value
	|
	| @return		boolean
	*/
	public static function installFromZip($zipFile, $expectedId = false, $fallbackId = false)
	{
		global $L;
		global $syslog;

		if (!self::writable()) {
			return self::fail($L->g('The directory bl-plugins is not writable'));
		}

		$plugin = self::extractAndValidate($zipFile, $expectedId, $fallbackId);
		if ($plugin === false) {
			return false;
		}

		$id = $plugin['id'];
		if (self::exists($id)) {
			self::cleanUp($plugin['staging']);
			return self::fail($L->g('The plugin is already installed'), 'The plugin ' . $id . ' is already installed.');
		}

		$installed = Filesystem::copyRecursive($plugin['root'], PATH_PLUGINS . $id);
		self::cleanUp($plugin['staging']);

		if (!$installed) {
			Filesystem::deleteRecursive(PATH_PLUGINS . $id);
			return self::fail($L->g('Unable to copy the plugin to the directory bl-plugins'), 'Unable to copy the plugin ' . $id . ' to bl-plugins.');
		}

		$syslog->add(array(
			'dictionaryKey' => 'plugin-installed',
			'notes' => $id
		));
		Log::set('PluginInstaller' . LOG_SEP . 'The plugin ' . $id . ' version ' . $plugin['version'] . ' was installed.', LOG_TYPE_INFO);

		return $id;
	}

	/*
	| Update a plugin already installed, the settings of the plugin are not
	| touched, they live in bl-content/databases/plugins outside bl-plugins
	|
	| @id			string	Plugin id
	|
	| @return		boolean
	*/
	public static function update($id)
	{
		global $L;
		global $syslog;

		if (!self::exists($id)) {
			return self::fail($L->g('The plugin is not installed'), 'The plugin ' . $id . ' is not installed.');
		}

		if (!self::writable()) {
			return self::fail($L->g('The directory bl-plugins is not writable'));
		}

		$entry = PluginsDirectory::getEntry($id);
		if ($entry === false) {
			return self::fail($L->g('The plugin is not available in the plugins directory'), 'The plugin ' . $id . ' is not in the directory.');
		}

		$zipFile = self::download($entry);
		if ($zipFile === false) {
			return false;
		}

		// The new version is fully validated before deleting the current one
		$plugin = self::extractAndValidate($zipFile, $id);
		unlink($zipFile);
		if ($plugin === false) {
			return false;
		}

		// Keep a copy of the current version to restore it if the copy fails
		$backup = PATH_TMP . 'plugin-backup-' . uniqid() . DS;
		if (!Filesystem::copyRecursive(PATH_PLUGINS . $id, $backup)) {
			self::cleanUp($plugin['staging']);
			return self::fail($L->g('Unable to update the plugin'), 'Unable to backup the plugin ' . $id . ' before the update.');
		}

		Filesystem::deleteRecursive(PATH_PLUGINS . $id);
		$updated = Filesystem::copyRecursive($plugin['root'], PATH_PLUGINS . $id);

		if (!$updated) {
			// Restore the previous version, the site must not be left without the plugin
			Filesystem::deleteRecursive(PATH_PLUGINS . $id);
			Filesystem::copyRecursive($backup, PATH_PLUGINS . $id);
			self::cleanUp($plugin['staging']);
			self::cleanUp($backup);
			return self::fail($L->g('Unable to update the plugin'), 'Unable to copy the new version of the plugin ' . $id . ', the previous version was restored.');
		}

		self::cleanUp($plugin['staging']);
		self::cleanUp($backup);

		$syslog->add(array(
			'dictionaryKey' => 'plugin-updated',
			'notes' => $id
		));
		Log::set('PluginInstaller' . LOG_SEP . 'The plugin ' . $id . ' was updated to the version ' . $plugin['version'] . '.', LOG_TYPE_INFO);

		return true;
	}

	/*
	| Delete the files of a plugin, the plugin is deactivated first to remove
	| its database and workspace
	|
	| @id			string	Plugin id
	|
	| @return		boolean
	*/
	public static function delete($id)
	{
		global $L;
		global $plugins;
		global $syslog;

		if (!self::exists($id)) {
			return self::fail($L->g('The plugin is not installed'), 'The plugin ' . $id . ' is not installed.');
		}

		// Deactivate the plugin to delete its database and workspace
		foreach ($plugins['all'] as $className => $plugin) {
			if ($plugin->directoryName() === $id) {
				if ($plugin->installed()) {
					deactivatePlugin($className);
				}
				break;
			}
		}

		if (!Filesystem::deleteRecursive(PATH_PLUGINS . $id)) {
			return self::fail($L->g('Unable to delete the plugin'), 'Unable to delete the directory of the plugin ' . $id . '.');
		}

		$syslog->add(array(
			'dictionaryKey' => 'plugin-deleted',
			'notes' => $id
		));
		Log::set('PluginInstaller' . LOG_SEP . 'The plugin ' . $id . ' was deleted.', LOG_TYPE_INFO);

		return true;
	}

	/*
	| Extract the zip inside a staging directory and validate the content is a
	| valid plugin for this version of Bludit
	|
	| Returns an array with the keys staging, root, id and version, the caller
	| is responsible of deleting the staging directory
	|
	| @zipFile		string	Absolute path to the zip file
	| @expectedId		string	When set, the plugin id must match this value
	|
	| @return		array|false
	*/
	private static function extractAndValidate($zipFile, $expectedId = false, $fallbackId = false)
	{
		global $L;

		if (!extension_loaded('zip')) {
			return self::fail($L->g('The PHP extension zip is required to install plugins'));
		}

		$zip = new ZipArchive();
		if ($zip->open($zipFile) !== true) {
			return self::fail($L->g('The file is not a valid zip file'));
		}

		// Check every entry BEFORE extracting, ZipArchive::extractTo() does not
		// protect against path traversal
		$uncompressed = 0;
		for ($i = 0; $i < $zip->numFiles; $i++) {
			$stat = $zip->statIndex($i);
			if ($stat === false) {
				$zip->close();
				return self::fail($L->g('The file is not a valid zip file'));
			}

			$name = $stat['name'];
			if ((strpos($name, '..') !== false) ||
				(strpos($name, '\\') !== false) ||
				(strpos($name, ':') !== false) ||
				(substr($name, 0, 1) === '/')) {
				$zip->close();
				return self::fail($L->g('The zip file contains invalid file names'), 'Path traversal detected in the zip file, entry ' . $name);
			}

			// Symbolic links are not allowed, they can point outside bl-plugins
			// The entry is checked here because ZipArchive does not always
			// extract a symbolic link as a symbolic link
			if (self::isSymlinkEntry($zip, $i)) {
				$zip->close();
				return self::fail($L->g('The zip file contains symbolic links'), 'Symbolic link detected in the zip file, entry ' . $name);
			}

			$uncompressed += $stat['size'];
			if ($uncompressed > PLUGINS_MAX_UNCOMPRESSED_SIZE) {
				$zip->close();
				return self::fail($L->g('The content of the zip file is too big'), 'The uncompressed content of the zip file is bigger than the maximum allowed.');
			}
		}

		$staging = self::stagingDirectory();
		if ($staging === false) {
			$zip->close();
			return self::fail($L->g('Unable to create a temporary directory'), 'Unable to create the staging directory inside ' . PATH_TMP);
		}

		$extracted = $zip->extractTo($staging);
		$zip->close();
		if (!$extracted) {
			self::cleanUp($staging);
			return self::fail($L->g('Unable to extract the zip file'));
		}

		// Symbolic links are not allowed, they can point outside bl-plugins
		if (self::containsSymlink($staging)) {
			self::cleanUp($staging);
			return self::fail($L->g('The zip file contains symbolic links'), 'Symbolic link detected in the zip file.');
		}

		$root = self::findRoot($staging);
		if ($root === false) {
			self::cleanUp($staging);
			return self::fail($L->g('The zip file does not contain a Bludit plugin, the file plugin.php is missing'));
		}

		// --- metadata.json ---
		$metadataFilename = $root . DS . 'metadata.json';
		if (!file_exists($metadataFilename)) {
			self::cleanUp($staging);
			return self::fail($L->g('The plugin does not contain the file metadata.json'));
		}

		$metadata = json_decode(file_get_contents($metadataFilename), true);
		if (!is_array($metadata) || empty($metadata['version']) || empty($metadata['compatible'])) {
			self::cleanUp($staging);
			return self::fail($L->g('The file metadata.json is invalid or incomplete'));
		}

		if (!PluginsDirectory::isCompatible($metadata)) {
			self::cleanUp($staging);
			return self::fail($L->g('The plugin is not compatible with this version of Bludit'), 'The plugin is compatible with ' . $metadata['compatible'] . ' and this is Bludit ' . BLUDIT_VERSION);
		}

		// --- languages/en.json ---
		$languageFilename = $root . DS . 'languages' . DS . DEFAULT_LANGUAGE_FILE;
		if (!file_exists($languageFilename)) {
			self::cleanUp($staging);
			return self::fail($L->g('The plugin does not contain the file languages/en.json'));
		}

		$language = json_decode(file_get_contents($languageFilename), true);
		if (!isset($language['plugin-data']['name']) || !isset($language['plugin-data']['description'])) {
			self::cleanUp($staging);
			return self::fail($L->g('The file languages/en.json is invalid, the name or the description of the plugin is missing'));
		}

		// --- id ---
		// The id is the name of the directory created inside bl-plugins
		// When the plugin comes from the directory the id is already known,
		// otherwise it's the name of the directory inside the zip file and
		// when the zip file has no directory it's the name of the zip file
		if ($expectedId !== false) {
			$id = $expectedId;
		} elseif ($root !== rtrim($staging, DS)) {
			$id = basename($root);
		} else {
			$id = $fallbackId;
		}

		if (($id === false) || !self::validId($id)) {
			self::cleanUp($staging);
			return self::fail($L->g('The name of the plugin is not valid, rename the zip file with the name of the plugin'), 'The plugin id is not valid, ' . var_export($id, true));
		}

		// A plugin declaring a class already in use is a fatal error that can
		// not be caught, it has to be rejected before installing it
		$className = self::declaredClassName($root . DS . 'plugin.php');
		if ($className === false) {
			self::cleanUp($staging);
			return self::fail($L->g('The file plugin.php does not declare a plugin class'));
		}

		// The class is allowed to exist already when it belongs to the very
		// plugin being replaced, that is the normal case when updating
		if (class_exists($className, false) && !self::classBelongsTo($className, $id)) {
			self::cleanUp($staging);
			return self::fail($L->g('The plugin can not be installed because another plugin is already using the same class name') . ' ' . $className, 'Class name collision, ' . $className);
		}

		return array(
			'staging' => $staging,
			'root' => $root,
			'id' => $id,
			'version' => $metadata['version']
		);
	}

	/*
	| Returns the directory containing the plugin.php, it can be the staging
	| directory itself or the single directory created by the unzip, this is
	| how the archives generated by GitHub are supported
	|
	| @staging		string	Absolute path to the staging directory
	|
	| @return		string|false
	*/
	private static function findRoot($staging)
	{
		$staging = rtrim($staging, DS);

		if (file_exists($staging . DS . 'plugin.php')) {
			return $staging;
		}

		$directories = Filesystem::listDirectories($staging . DS);
		foreach ($directories as $directory) {
			if (file_exists($directory . DS . 'plugin.php')) {
				return rtrim($directory, DS);
			}
		}

		return false;
	}

	/*
	| Returns TRUE when the class already declared was declared by the plugin
	| installed in the directory $id, this is how an update is told apart from
	| two different plugins using the same class name
	|
	| @className		string	Name of the class
	| @id			string	Plugin id
	|
	| @return		boolean
	*/
	private static function classBelongsTo($className, $id)
	{
		try {
			$reflector = new ReflectionClass($className);
			$filename = $reflector->getFileName();
		} catch (Throwable $e) {
			return false;
		}

		if ($filename === false) {
			return false;
		}

		return (dirname($filename) === rtrim(PATH_PLUGINS . $id, DS));
	}

	/*
	| Returns TRUE when the entry of the zip file is a symbolic link
	|
	| The type of the file is stored in the high bits of the external
	| attributes when the zip file was created on a Unix system
	|
	| @zip			ZipArchive
	| @index		int	Index of the entry
	|
	| @return		boolean
	*/
	private static function isSymlinkEntry($zip, $index)
	{
		$opsys = null;
		$attributes = null;
		if (!$zip->getExternalAttributesIndex($index, $opsys, $attributes)) {
			return false;
		}

		if ($opsys !== ZipArchive::OPSYS_UNIX) {
			return false;
		}

		// S_IFMT 0xF000, S_IFLNK 0xA000
		return ((($attributes >> 16) & 0xF000) === 0xA000);
	}

	// Returns TRUE when there is at least one symbolic link inside the directory
	private static function containsSymlink($path)
	{
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST
		);

		foreach ($iterator as $item) {
			if ($item->isLink()) {
				return true;
			}
		}

		return false;
	}

	/*
	| Returns the name of the first class declared in the file, FALSE when the
	| file does not declare a class
	|
	| The file is tokenized, it's never included, a plugin is code from a third
	| party and it must not be executed to be validated
	|
	| @filename		string	Absolute path to the PHP file
	|
	| @return		string|false
	*/
	private static function declaredClassName($filename)
	{
		$tokens = @token_get_all(file_get_contents($filename));
		if (!is_array($tokens)) {
			return false;
		}

		$total = count($tokens);
		for ($i = 0; $i < $total; $i++) {
			if (!is_array($tokens[$i]) || ($tokens[$i][0] !== T_CLASS)) {
				continue;
			}

			// Skip the keyword class used for ::class
			if (isset($tokens[$i - 1]) && is_array($tokens[$i - 1]) && ($tokens[$i - 1][0] === T_DOUBLE_COLON)) {
				continue;
			}

			for ($j = $i + 1; $j < $total; $j++) {
				if (is_array($tokens[$j]) && ($tokens[$j][0] === T_STRING)) {
					return $tokens[$j][1];
				}
			}
		}

		return false;
	}
}
