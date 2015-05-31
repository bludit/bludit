<?php defined('BLUDIT') or die('Bludit CMS.');

class Filesystem {

	// NEW
	public static function listDirectories($path, $regex='*')
	{
		return glob($path.$regex, GLOB_ONLYDIR);
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

	// OLD
	public static function get_images($regex)
	{
		return self::ls(PATH_UPLOAD, $regex, '*', false, false, false);
	}

	// Devuelve un arreglo con el listado de archivos
	// $path con una barra al final, ej: /home/
	// $file_expression : *.0.*.*.*.*.*.*.*.*
	// $ext : xml
	// $flag_dir : si quiero listar directorios
	// $sort_asc_numeric : ordeno ascedente numerico
	// $sort_desc_numeric : ordeno descendente numerico
	public static function ls($path, $file_expression = NULL, $ext, $flag_dir = false, $sort_asc_numeric = false, $sort_desc_numeric = true)
	{
		if($flag_dir)
		{
			$files = glob($path . $file_expression, GLOB_ONLYDIR);
		}
		else
		{
			$files = glob($path . $file_expression . '.' . $ext);
		}

		if( ($files==false) || (empty($files)) )
		{
			$files = array();
		}

		foreach($files as $key=>$file)
		{
			$files[$key] = basename($file);
		}

		// Sort
		if($sort_asc_numeric)
		{
			sort($files, SORT_NUMERIC);
		}
		elseif($sort_desc_numeric)
		{
			rsort($files, SORT_NUMERIC);
		}

		return $files;
	}
}
