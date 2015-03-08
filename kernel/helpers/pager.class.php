<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Pager {

	public static function href_newer()
	{
		global $pager;

		return $pager['href_newer'];
	}

	public static function href_older()
	{
		global $pager;

		return $pager['href_older'];
	}

	public static function num_posts()
	{
		global $pager;

		return $pager['num_posts'];
	}

	public static function num_pages()
	{
		global $pager;

		return $pager['num_pages'];
	}

	public static function current()
	{
		global $pager;

		return $pager['current'];
	}

	public static function next()
	{
		global $pager;

		return $pager['next'];
	}

	public static function prev()
	{
		global $pager;

		return $pager['prev'];
	}

	public static function next_link($text=NULL)
	{
		global $pager;
		global $Language;

		if(!$pager['show_newer'])
			return false;

		$text = isset($text)?$text:$Language->get('NEWER_POSTS').' →';

		$html = '<a class="next-page" href="'.$pager['href_newer'].'">'.$text.'</a>';

		return $html;
	}

	public static function prev_link($text=NULL)
	{
		global $pager;
		global $Language;

		if(!$pager['show_older'])
			return false;

		$text = isset($text)?$text:'← '.$Language->get('OLDER_POSTS');

		$html = '<a class="prev-page" href="'.$pager['href_older'].'">'.$text.'</a>';

		return $html;
	}

	public static function home_link($text=NULL)
	{
		global $pager;
		global $Language;

		$text = isset($text)?$text:$Language->get('HOME');

		$html = '<a class="home-page" href="'.HTML_PATH_ROOT.'">'.$text.'</a>';

		return $html;
	}

}

?>
