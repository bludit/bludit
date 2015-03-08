<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Video {

	// Get video info on array
	// If the video does not exist or is invalid, returns false
	public static function video_get_info($url, $width = 640, $height = 360)
	{
		if( helperText::is_substring($url, 'youtube.com') )
		{
			return( self::video_get_youtube($url, $width, $height) );
		}
		elseif( helperText::is_substring($url, 'vimeo.com') )
		{
			return( self::video_get_vimeo($url, $width, $height) );
		}

		return false;
	}

	private static function video_get_youtube($url, $width = 640, $height = 360)
	{
		// Youtube ID
		preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
		$video_id = $matches[1];

		// Check if a valid url
		if(!Net::check_http_code('http://gdata.youtube.com/feeds/api/videos/'.$video_id,200))
		{
			return(false);
		}

		// GET INFO
		$xml = simplexml_load_file('http://gdata.youtube.com/feeds/api/videos/'.$video_id);
		$media = $xml->children('http://search.yahoo.com/mrss/');

		$info = array();
		$info['id'] = $video_id;
		$info['title'] = (string)$media->group->title;
		$info['description'] = (string)$media->group->description;

		$info['thumb'][0] = (string)$media->group->thumbnail[0]->attributes()->url;
		$info['thumb'][1] = (string)$media->group->thumbnail[1]->attributes()->url;
		$info['thumb'][2] = (string)$media->group->thumbnail[2]->attributes()->url;
		$info['thumb'][3] = (string)$media->group->thumbnail[3]->attributes()->url;

		$info['embed'] = '<iframe class="youtube_embed" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video_id.'?rel=0" frameborder="0" allowfullscreen></iframe>';

		return($info);
	}

	private static function video_get_vimeo($url, $width = 640, $height = 360)
	{
		preg_match('/vimeo\.com\/([0-9]{1,10})/', $url, $matches);
		$video_id = $matches[1];

		// Check if a valid url
		if(!Net::check_http_code('http://vimeo.com/api/v2/video/'.$video_id.'.php',200))
		{
			return(false);
		}

		$hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/'.$video_id.'.php'));

		$info = array();
		$info['id'] = $video_id;
		$info['title'] = $hash[0]['title'];
		$info['description'] = $hash[0]['description'];

		$info['thumb'][0] =  $hash[0]['thumbnail_medium'];
		$info['thumb'][1] =  $hash[0]['thumbnail_small'];

		$info['embed'] = '<iframe class="vimeo_embed" width="'.$width.'" height="'.$height.'" src="http://player.vimeo.com/video/'.$video_id.'"  frameborder="0" allowFullScreen></iframe>';

		return($info);
	}

}

?>
