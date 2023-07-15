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
			usort($directories,
			      function($a, $b) {
				      return filemtime($b) - filemtime($a);
			      }
			);
		}

		return $directories;
	}

	// Returns an array with the list of files with the absolute path
	// $sortByDate = TRUE, the first file is the newer file
	// $chunk = amount of chunks, FALSE if you don't want to chunk
	public static function listFiles($path, $regex='*', $extension='*', $sortByDate=false, $chunk=false)
	{
		error_log($path.$regex.'.'.$extension);
		$files = glob($path.$regex.'.'.$extension);

		if (empty($files)) {
			return array();
		}

		if ($sortByDate) {
			usort($files,
				function($a, $b) {
					return filemtime($b) - filemtime($a);
				}
			);
		}

		// Split the list of files into chunks
		// http://php.net/manual/en/function.array-chunk.php
		if ($chunk) {
			return array_chunk($files, $chunk);
		}

		return $files;
	}

	public static function mkdir($pathname, $recursive=false)
	{
		return mkdir($pathname, DIR_PERMISSIONS, $recursive);
	}

	public static function rmdir($pathname)
	{
		Log::set('rmdir = '.$pathname, LOG_TYPE_INFO);
		return rmdir($pathname);
	}

	public static function mv($oldname, $newname)
	{
		Log::set('mv '.$oldname.' '.$newname, LOG_TYPE_INFO);
		return rename($oldname, $newname);
	}

	public static function rmfile($filename)
	{
		Log::set('rmfile = '.$filename, LOG_TYPE_INFO);
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

	// Copy recursive a directory to another
	// If the destination directory not exists is created
	// $source = /home/diego/example or /home/diego/example/
	// $destination = /home/diego/newplace or /home/diego/newplace/
	public static function copyRecursive($source, $destination, $skipDirectory=false)
	{
		$source 	= rtrim($source, DS);
		$destination 	= rtrim($destination, DS);

		// Check $source directory if exists
		if (!self::directoryExists($source)) {
			return false;
		}

		// Check $destionation directory if exists
		if (!self::directoryExists($destination)) {
			// Create the $destination directory
			if (!mkdir($destination, DIR_PERMISSIONS, true)) {
				return false;
			}
		}

		foreach ($iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
				RecursiveIteratorIterator::SELF_FIRST) as $item) {

			$currentDirectory = dirname($item->getPathName());
			if ($skipDirectory !== $currentDirectory) {
				if ($item->isDir()) {
					@mkdir($destination.DS.$iterator->getSubPathName());
				} else {
					copy($item, $destination.DS.$iterator->getSubPathName());
				}
			}
		}

		return true;
	}

	// Delete a file or directory recursive
	// The directory is delete
	public static function deleteRecursive($source, $deleteDirectory=true)
	{
		Log::set('deleteRecursive = '.$source, LOG_TYPE_INFO);

		if (!self::directoryExists($source)) {
			return false;
		}

		foreach (new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST) as $item) {
				if ($item->isFile() || $item->isLink()) {
					unlink($item);
				} else {
					rmdir($item);
				}
		}

		if ($deleteDirectory) {
			return rmdir($source);
		}
		return true;
	}

	// Compress a file or directory
	// $source = /home/diego/example
	// $destionation = /tmp/example.zip
	public static function zip($source, $destination)
	{
		if (!extension_loaded('zip')) {
			return false;
		}

		if (!file_exists($source)) {
			return false;
		}

		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}

		if (is_dir($source) === true) {
			$iterator = new RecursiveDirectoryIterator($source);
			$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

			foreach ($files as $file) {
				$file = realpath($file);
				if (is_dir($file)) {
					$zip->addEmptyDir(ltrim(str_replace($source, '', $file), "/\\"));
				} elseif (is_file($file)) {
					$zip->addFromString(ltrim(str_replace($source, '', $file), "/\\"), file_get_contents($file));
				}
			}
		} elseif (is_file($source)) {
			$zip->addFromString(basename($source), file_get_contents($source));
		}

		return $zip->close();
	}

	// Uncompress a zip file
	// $source = /home/diego/example.zip
	// $destionation = /home/diego/content
	public static function unzip($source, $destination)
	{
		if (!extension_loaded('zip')) {
			return false;
		}

		if (!file_exists($source)) {
			return false;
		}

		$zip = new ZipArchive();
		if (!$zip->open($source)) {
			return false;
		}

		$zip->extractTo($destination);
		return $zip->close();
	}

  /*
  | Returns the next filename if the filename already exist otherwise returns the original filename
  |
  | @path	string	Path
  | @filename	string	Filename
  |
  | @return	string
  */
	public static function nextFilename($filename, $path=PATH_UPLOADS) {
		// Clean filename and get extension
		$fileExtension 	= pathinfo($filename, PATHINFO_EXTENSION);
		$fileExtension 	= Text::lowercase($fileExtension);
		$filename 	= pathinfo($filename, PATHINFO_FILENAME);
		$filename 	= Text::removeSpaces($filename);
		$filename 	= Text::removeQuotes($filename);

		// Search for the next filename
		$tmpName = $filename.'.'.$fileExtension;
		if (Sanitize::pathFile($path.$tmpName)) {
			$number = 0;
			$tmpName = $filename.'_'.$number.'.'.$fileExtension;
			while (Sanitize::pathFile($path.$tmpName)) {
				$number = $number + 1;
				$tmpName = $filename.'_'.$number.'.'.$fileExtension;
			}
		}
		return $tmpName;
	}

  /*
  | Returns the filename
  | Example:
  |	@file	/home/diego/dog.jpg
  |	@return dog.jpg
  |
  | @file	string	Full path of the file
  |
  | @return	string
  */
	public static function filename($file) {
		return basename($file);
	}

	/*
  | Returns the file extension
  | Example:
  |	@file	/home/diego/dog.jpg
  |	@return jpg
  |
  | @file	string	Full path of the file
  |
  | @return	string
  */
	public static function extension($file) {
		return pathinfo($file, PATHINFO_EXTENSION);
	}

	/**
	 * Get Size of file or directory in bytes
	 * @param  [string] $fileOrDirectory
	 * @return [int|bool]                  [bytes or false on error]
	 */
	public static function getSize($fileOrDirectory) {
		// Files
		if (is_file($fileOrDirectory)) {
			return filesize($fileOrDirectory);
		}
		// Directories
		if (file_exists($fileOrDirectory)) {
		    $size = 0;
		    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fileOrDirectory, FilesystemIterator::SKIP_DOTS)) as $file){
				try {
					$size += $file->getSize();
				} catch (Exception $e) {
					// SplFileInfo::getSize RuntimeException will be thrown on broken symlinks/errors
				}
		    }
		    return $size;
		}
		return false;
	}

	public static function bytesToHumanFileSize($bytes, $decimals = 2) {
	    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
	    $factor = floor((strlen($bytes) - 1) / 3);
	    return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$size[$factor];
	}

	/*
  | Returns the mime type of the file
  | Example:
  |	@file	/home/diego/dog.jpg
  |	@return image/jpeg
  |
  | @file	[string]	Full path of the file
  |
  | @return	[string|bool]	Mime type as string or FALSE if not possible to get the mime type
  */
	public static function mimeType($file) {
		if (function_exists('mime_content_type')) {
			return mime_content_type($file);
		}

		if (function_exists('finfo_file')) {
			$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimeType = finfo_file($fileinfo, $file);
			finfo_close($fileinfo);
			return $mimeType;
		}

		return false;
	}

	public static function symlink($from, $to) {
		if (function_exists('symlink')) {
			return symlink($from, $to);
		} else {
			return copy($from, $to);
		}
	}

}
