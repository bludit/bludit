<?php defined('BLUDIT') or die('Bludit CMS.');

class Filesystem {

	// Returns an array with the absolutes directories.
	public static function listDirectories($path, $regex='*', $sortByDate=false)
	{
		$directories = glob($path.$regex, GLOB_ONLYDIR);

		if(empty($directories)) {
			return array();
		}

		if($sortByDate) {
			usort($directories, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
		}

		return $directories;
	}

	public static function listFiles($path, $regex='*', $extension='*', $sortByDate=false)
	{
		$files = glob($path.$regex.'.'.$extension);

		if(empty($files)) {
			return array();
		}

		if($sortByDate) {
			usort($files, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
		}

		return $files;
	}

	public static function mkdir($pathname, $recursive=false)
	{
		// DEBUG: Ver permisos si son correctos
		return mkdir($pathname, 0755, $recursive);
	}

	public static function rmdir($pathname)
	{
		return rmdir($pathname);
	}

	public static function mv($oldname, $newname)
	{
		return rename($oldname, $newname);
	}

	public static function rmfile($filename)
	{
		return unlink($filename);
	}

	public static function fileExists($filename)
	{
		return file_exists($filename);
	}

	public static function directoryExists($path)
	{
		return file_exists($path);
	}

	public static function copyRecursive($source, $destination)
	{
		if (!self::directoryExists($source)) {
			return false;
		}

		$destination = rtrim($destination, '/');

		foreach($iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
				RecursiveIteratorIterator::SELF_FIRST) as $item) {
					if($item->isDir()) {
						@mkdir($destination.DS.$iterator->getSubPathName());
					} else {
						copy($item, $destination.DS.$iterator->getSubPathName());
					}
		}
		return true;
	}

	public static function deleteRecursive($source)
	{
		if (!self::directoryExists($source)) {
			return false;
		}

		foreach(new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST) as $item) {
				if($item->isFile()) {
					unlink($item);
				} else {
					rmdir($item);
				}
		}

		return rmdir($source);
	}
}