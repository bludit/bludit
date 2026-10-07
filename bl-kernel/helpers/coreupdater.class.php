<?php defined('BLUDIT') or die('Bludit CMS.');

/*
| Update Bludit from the admin panel
|
| core.json describes the last release, it's signed with the key of the Bludit
| team and the signature is verified before trusting anything inside it, the
| zip file is verified with the sha256 from core.json
|
| The update never touches bl-content, the content, the settings and the
| license are kept, it replaces bl-kernel, bl-languages, index.php and the
| plugins and themes included in the release, the other plugins and themes
| are not touched
|
| Every item is copied next to the current one first, the slow part, and then
| the items are swapped with rename(), so the site is never left with half of
| the files of each version, the previous version is restored when something
| fails or when the site doesn't answer after the update
*/

class CoreUpdater {

	// Last error message, to show it to the user
	public static $error = '';

	// Items always replaced by an update, relative to PATH_ROOT
	private static $items = array('bl-kernel', 'bl-languages', 'index.php');

	private static function fail($message, $logMessage = false)
	{
		self::$error = $message;
		Log::set('CoreUpdater' . LOG_SEP . ($logMessage === false ? $message : $logMessage), LOG_TYPE_ERROR);
		return false;
	}

	// Absolute path to the cached core.json
	public static function cacheFilename()
	{
		return PATH_TMP . 'core-update.json';
	}

	/*
	| Returns the last release of Bludit, FALSE when core.json is not available
	| or the signature is not valid
	|
	| @force		boolean	TRUE to ignore the cache and download core.json again
	|
	| @return		array|false
	*/
	public static function getRelease($force = false)
	{
		$filename = self::cacheFilename();
		$cached = file_exists($filename);
		$expired = !$cached || ((time() - filemtime($filename)) > CORE_UPDATE_CACHE_TTL);

		if ($force || $expired) {
			$content = TCP::http(CORE_UPDATE_URL, 'GET', true, 15);
			$release = self::parse($content);

			if ($release !== false) {
				file_put_contents($filename, $content, LOCK_EX);
				return $release;
			}

			Log::set('CoreUpdater' . LOG_SEP . 'Unable to get a valid core.json from ' . CORE_UPDATE_URL, LOG_TYPE_ERROR);
			if (!$cached || $force) {
				return false;
			}
		}

		// The cache is verified too, it's the same content downloaded before
		return self::parse(file_get_contents($filename));
	}

	/*
	| Verify the signature of core.json and returns the release, FALSE when the
	| content is not valid
	|
	| core.json is {"payload": base64, "signature": base64}, the signature is of
	| the bytes of the payload, so the JSON inside is never parsed before the
	| signature is verified
	|
	| @content		string	Content of core.json
	|
	| @return		array|false
	*/
	public static function parse($content)
	{
		if (empty($content) || !is_string($content)) {
			return false;
		}

		if (!function_exists('sodium_crypto_sign_verify_detached')) {
			Log::set('CoreUpdater' . LOG_SEP . 'The PHP extension sodium is required to verify the updates.', LOG_TYPE_ERROR);
			return false;
		}

		$json = json_decode($content, true);
		if (!isset($json['payload'], $json['signature']) || !is_string($json['payload']) || !is_string($json['signature'])) {
			return false;
		}

		$payload = base64_decode($json['payload'], true);
		$signature = base64_decode($json['signature'], true);
		$publicKey = base64_decode(CORE_UPDATE_PUBLIC_KEY, true);
		if (($payload === false) || ($signature === false) || ($publicKey === false)) {
			return false;
		}
		if ((strlen($signature) !== SODIUM_CRYPTO_SIGN_BYTES) || (strlen($publicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES)) {
			return false;
		}

		try {
			$valid = sodium_crypto_sign_verify_detached($signature, $payload, $publicKey);
		} catch (Throwable $e) {
			$valid = false;
		}
		if (!$valid) {
			Log::set('CoreUpdater' . LOG_SEP . 'The signature of core.json is not valid.', LOG_TYPE_ERROR);
			return false;
		}

		$release = json_decode($payload, true);
		if (!is_array($release)) {
			return false;
		}

		// Refuse a release described for a newer version of Bludit instead of
		// reading it wrong, this is what allows the format to change later
		if (!isset($release['schema']) || ($release['schema'] > CORE_UPDATE_SCHEMA)) {
			return false;
		}

		foreach (array('version', 'download', 'sha256', 'php') as $field) {
			if (!isset($release[$field]) || !is_string($release[$field]) || ($release[$field] === '')) {
				return false;
			}
		}

		return $release;
	}

	// Returns TRUE when the release is newer than this version of Bludit
	public static function available($release)
	{
		return version_compare($release['version'], BLUDIT_VERSION, '>');
	}

	// Returns TRUE when this server runs the version of PHP required by the release
	public static function phpCompatible($release)
	{
		return version_compare(PHP_VERSION, $release['php'], '>=');
	}

	// Returns TRUE when the release changes the major or minor version, the
	// license of Bludit PRO is for a minor version, 4.0, 4.1, ...
	public static function changesMinorVersion($release)
	{
		$current = explode('.', BLUDIT_VERSION);
		$new = explode('.', $release['version']);
		if ((count($current) < 2) || (count($new) < 2)) {
			return true;
		}
		return ($current[0] !== $new[0]) || ($current[1] !== $new[1]);
	}

	/*
	| Returns the directories where Bludit can't write, the update is only
	| possible when the list is empty
	|
	| rename() needs to write in the directory containing the item, not in the
	| item itself, that's why the directories checked are the parents
	|
	| @return		array
	*/
	public static function notWritable()
	{
		$directories = array(
			'/' => PATH_ROOT,
			'bl-plugins' => PATH_PLUGINS,
			'bl-themes' => PATH_THEMES,
			'bl-content/tmp' => PATH_TMP
		);

		$list = array();
		foreach ($directories as $name => $path) {
			if (!is_writable($path)) {
				$list[] = $name;
			}
		}
		return $list;
	}

	/*
	| Download the last release and update Bludit
	|
	| @return		boolean
	*/
	public static function update()
	{
		global $L;

		// Never trust the cache to update, core.json is downloaded again
		$release = self::getRelease(true);
		if ($release === false) {
			return self::fail($L->g('Unable to check the updates of Bludit'));
		}

		if (!self::available($release)) {
			return self::fail($L->g('You have the last version of Bludit'));
		}

		if (!self::phpCompatible($release)) {
			return self::fail(sprintf($L->g('The new version of Bludit requires PHP %s'), Sanitize::html($release['php'])));
		}

		$notWritable = self::notWritable();
		if (!empty($notWritable)) {
			return self::fail($L->g('Bludit can not write in these directories') . ' ' . implode(', ', $notWritable));
		}

		$zipFile = self::download($release);
		if ($zipFile === false) {
			return false;
		}

		$updated = self::install($zipFile, $release['version']);
		if (file_exists($zipFile)) {
			unlink($zipFile);
		}
		return $updated;
	}

	// Returns TRUE when the URL is https and the host is allowed
	public static function allowedURL($url)
	{
		$parts = parse_url($url);
		if (($parts === false) || empty($parts['scheme']) || empty($parts['host'])) {
			return false;
		}
		$host = Text::lowercase($parts['host']);

		// Testing, remove before merging: a local server to download the zip from
		if (($parts['scheme'] === 'http') && in_array($host, array('127.0.0.1', 'localhost'), true)) {
			return true;
		}

		if ($parts['scheme'] !== 'https') {
			return false;
		}
		return in_array($host, $GLOBALS['CORE_UPDATE_ALLOWED_HOSTS'], true);
	}

	/*
	| Download the zip file of the release and check its integrity
	| Returns the absolute path to the downloaded file, FALSE on failure
	|
	| @release		array	Release from core.json
	|
	| @return		string|false
	*/
	private static function download($release)
	{
		global $L;

		if (!self::allowedURL($release['download'])) {
			return self::fail($L->g('Unable to download the new version of Bludit'), 'The URL is not allowed, ' . $release['download']);
		}

		$zipFile = PATH_TMP . 'core-download-' . uniqid() . '.zip';

		// Testing, remove before merging: TCP::downloadFile() only allows https,
		// a local server for testing is plain http
		$host = Text::lowercase((string) parse_url($release['download'], PHP_URL_HOST));
		if (in_array($host, array('127.0.0.1', 'localhost'), true)) {
			$bytes = @copy($release['download'], $zipFile) ? filesize($zipFile) : false;
		} else {
			$bytes = TCP::downloadFile($release['download'], $zipFile, CORE_UPDATE_MAX_ZIP_SIZE, 120);
		}
		if ($bytes === false) {
			return self::fail($L->g('Unable to download the new version of Bludit'), 'Unable to download ' . $release['download']);
		}

		$sha256 = hash_file('sha256', $zipFile);
		if (!hash_equals(Text::lowercase($release['sha256']), $sha256)) {
			unlink($zipFile);
			return self::fail($L->g('The new version of Bludit does not match the checksum, it may have been modified'), 'Checksum mismatch for ' . $release['download'] . ', expected ' . $release['sha256'] . ' got ' . $sha256);
		}

		return $zipFile;
	}

	/*
	| Update Bludit from the zip file of a release
	|
	| @zipFile		string	Absolute path to the zip file
	| @version		string	Version the zip file must contain
	|
	| @return		boolean
	*/
	public static function install($zipFile, $version)
	{
		global $L;
		global $syslog;

		$staging = PluginInstaller::extract($zipFile, CORE_UPDATE_MAX_UNCOMPRESSED_SIZE);
		if ($staging === false) {
			return self::fail(PluginInstaller::$error);
		}

		$root = self::findRoot($staging);
		if ($root === false) {
			Filesystem::deleteRecursive($staging);
			return self::fail($L->g('The zip file does not contain Bludit'));
		}

		// The zip file must contain the version described by core.json
		$zipVersion = self::readVersion($root);
		if ($zipVersion !== $version) {
			Filesystem::deleteRecursive($staging);
			return self::fail($L->g('The zip file does not contain Bludit'), 'The zip file contains the version ' . var_export($zipVersion, true) . ' and core.json describes ' . $version);
		}

		$items = self::items($root);

		// A backup left by an update that didn't finish can be the only copy of
		// a working version, it's never deleted automatically
		foreach ($items as $item) {
			if (file_exists(self::backupPath($item))) {
				Filesystem::deleteRecursive($staging);
				return self::fail($L->g('A previous update did not finish, check the log'), 'The backup ' . self::backupPath($item) . ' exists, restore it or delete it before updating.');
			}
		}

		// Copy every item next to the current one, nothing is replaced yet
		foreach ($items as $item) {
			if (!self::prepare($root . DS . $item, self::updatePath($item))) {
				self::discard($items);
				Filesystem::deleteRecursive($staging);
				return self::fail($L->g('Unable to update Bludit'), 'Unable to copy ' . $item . ' next to the current version.');
			}
		}
		Filesystem::deleteRecursive($staging);

		// Swap the items, rename() is immediate
		$swapped = array();
		foreach ($items as $item) {
			if (!self::swap($item)) {
				self::rollback($swapped);
				self::discard($items);
				return self::fail($L->g('Unable to update Bludit'), 'Unable to swap ' . $item . ', the previous version was restored.');
			}
			$swapped[] = $item;
		}

		// Without this PHP can keep running the previous version from the cache
		if (function_exists('opcache_reset')) {
			@opcache_reset();
		}

		if (!self::healthy()) {
			self::rollback($swapped);
			self::discard($items);
			if (function_exists('opcache_reset')) {
				@opcache_reset();
			}
			return self::fail($L->g('The new version of Bludit did not work, the previous version was restored'), 'The site returned an error after the update to ' . $version . ', the previous version was restored.');
		}

		foreach ($items as $item) {
			self::remove(self::backupPath($item));
		}

		$syslog->add(array(
			'dictionaryKey' => 'bludit-updated',
			'notes' => BLUDIT_VERSION . ' > ' . $version
		));
		Log::set('CoreUpdater' . LOG_SEP . 'Bludit was updated from ' . BLUDIT_VERSION . ' to ' . $version . '.', LOG_TYPE_INFO);

		return true;
	}

	/*
	| Returns the directory containing Bludit inside the staging directory, it
	| can be the staging directory itself or a single directory inside
	|
	| @return		string|false
	*/
	private static function findRoot($staging)
	{
		$candidates = array(rtrim($staging, DS));
		foreach (Filesystem::listDirectories($staging) as $directory) {
			$candidates[] = rtrim($directory, DS);
		}

		foreach ($candidates as $candidate) {
			if (file_exists($candidate . DS . 'index.php') && file_exists($candidate . DS . 'bl-kernel' . DS . 'boot' . DS . 'init.php')) {
				return $candidate;
			}
		}
		return false;
	}

	// Returns the version of Bludit inside the directory, the file is read, never included
	private static function readVersion($root)
	{
		$content = file_get_contents($root . DS . 'bl-kernel' . DS . 'boot' . DS . 'init.php');
		if (preg_match("/define\('BLUDIT_VERSION',\s*'([^']+)'\)/", $content, $matches)) {
			return $matches[1];
		}
		return false;
	}

	// Items replaced by the update, the fixed ones plus the plugins and themes
	// included in the release, relative to PATH_ROOT
	private static function items($root)
	{
		$items = self::$items;
		foreach (array('bl-plugins', 'bl-themes') as $parent) {
			if (!Filesystem::directoryExists($root . DS . $parent)) {
				continue;
			}
			foreach (Filesystem::listDirectories($root . DS . $parent . DS) as $directory) {
				$items[] = $parent . DS . basename($directory);
			}
		}
		return $items;
	}

	// The new version of the item is copied here before the swap
	private static function updatePath($item)
	{
		return PATH_ROOT . dirname($item) . DS . '.' . basename($item) . '.update';
	}

	// The current version of the item is moved here during the swap
	private static function backupPath($item)
	{
		return PATH_ROOT . dirname($item) . DS . '.' . basename($item) . '.backup';
	}

	// Delete a file or a directory
	private static function remove($path)
	{
		if (is_dir($path)) {
			return Filesystem::deleteRecursive($path);
		}
		if (file_exists($path)) {
			return unlink($path);
		}
		return true;
	}

	/*
	| Copy a file or a directory checking every file, Filesystem::copyRecursive()
	| ignores the files it can't copy and an update can't miss a file
	|
	| @return		boolean
	*/
	private static function prepare($source, $destination)
	{
		self::remove($destination);

		if (is_file($source)) {
			return copy($source, $destination);
		}

		if (!mkdir($destination, DIR_PERMISSIONS)) {
			return false;
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST
		);
		foreach ($iterator as $file) {
			$target = $destination . DS . $iterator->getSubPathName();
			if ($file->isDir()) {
				if (!is_dir($target) && !mkdir($target, DIR_PERMISSIONS)) {
					return false;
				}
			} elseif (!copy($file->getPathname(), $target)) {
				return false;
			}
		}
		return true;
	}

	// Replace the current item with the new one, the current one is kept as backup
	private static function swap($item)
	{
		$current = PATH_ROOT . $item;
		if (file_exists($current) && !rename($current, self::backupPath($item))) {
			return false;
		}
		if (!rename(self::updatePath($item), $current)) {
			// Put back the current one, the item can't be left missing
			if (file_exists(self::backupPath($item))) {
				rename(self::backupPath($item), $current);
			}
			return false;
		}
		return true;
	}

	// Restore the previous version of the items already swapped
	private static function rollback($swapped)
	{
		foreach (array_reverse($swapped) as $item) {
			$current = PATH_ROOT . $item;
			$backup = self::backupPath($item);
			self::remove($current);
			if (file_exists($backup)) {
				rename($backup, $current);
			}
		}
	}

	// Delete the copies of the new version not swapped
	private static function discard($items)
	{
		foreach ($items as $item) {
			self::remove(self::updatePath($item));
		}
	}

	/*
	| Returns FALSE when the site or the login page answers with an error after
	| the update, a site that can't be reached from the server itself, a local
	| or intranet installation, is not considered broken
	|
	| @return		boolean
	*/
	private static function healthy()
	{
		if (!function_exists('curl_version')) {
			return true;
		}

		foreach (array(DOMAIN_BASE, DOMAIN_ADMIN . 'login') as $url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Bludit/' . BLUDIT_VERSION);
			curl_exec($ch);
			$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
			curl_close($ch);

			if ($responseCode >= 500) {
				Log::set('CoreUpdater' . LOG_SEP . 'The URL ' . $url . ' returned the HTTP code ' . $responseCode . ' after the update.', LOG_TYPE_ERROR);
				return false;
			}
		}
		return true;
	}
}
