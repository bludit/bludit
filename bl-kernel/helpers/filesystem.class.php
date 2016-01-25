<?php defined('BLUDIT') or die('Bludit CMS.');

class Filesystem {

	// Returns an array with the absolutes directories.
	public static function listDirectories($path, $regex='*')
	{
		$directories = glob($path.$regex, GLOB_ONLYDIR);

		if(empty($directories)) {
			return array();
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

}