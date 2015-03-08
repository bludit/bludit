<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Post {

	public static function title()
	{
		global $post;

		return $post['title'];
	}

	public static function description()
	{
		global $post;

		return $post['description'];
	}

	public static function comments()
	{
		global $post;
		global $_DB_COMMENTS;

		$comments = $_DB_COMMENTS->get_list_by_post( array('id_post'=>$post['id']) );

		return $comments;
	}

	public static function num_comments()
	{
		global $post;

		return count($post['comments']);
	}

	public static function category($field=false)
	{
		global $post;
		global $_DB_CATEGORIES;

		$category = $_DB_CATEGORIES->get( array('id'=>$post['id_cat']) );

		if($field=='id')
			return $category['id'];

		if($field=='slug')
			return $category['slug'];

		if($field=='permalink')
			return helperUrl::category($category['slug']);

		return $category['name'];
	}

	public static function read_more()
	{
		global $post;

		return $post['read_more'];
	}

	public static function allow_comments()
	{
		global $post;

		return $post['allow_comments'];
	}

	public static function permalink($absolute=false)
	{
		global $post;

		return helperUrl::post($post,$absolute);
	}

	public static function tags($return2array=false)
	{
		global $post;
		global $_DB_TAGS;

		$tags = $_DB_TAGS->get_by_idpost( array('id_post'=>$post['id']) );

		if($return2array)
			return $tags;

		$html = '<ul>';
		foreach($tags as $tag)
			$html .= '<li><a class="tag" href="'.helperUrl::tag($tag['name']).'">'.$tag['name_human'].'</a></li>';
		$html .= '</ul>';

		return $html;
	}

	public static function comment_count_link()
	{
		global $post;
		global $theme;
		global $Language;
		global $Comment;

		if(!$post['allow_comments'])
			return false;

		if( $Comment->disqus_enabled() )
		{
			$url = helperUrl::post($post, true);
			return '<a href="'.$url.'#disqus_thread">'.$Language->get('COMMENTS').'</a>';
		}
		elseif( $Comment->facebook_enabled() )
		{
			$url = helperUrl::post($post, true);
			return '<a href="'.$post['permalink'].'#comment_form">'.$Language->get('COMMENTS').' (<fb:comments-count href="'.$url.'"></fb:comments-count>)</a>';
		}
		else
		{
			return '<a href="'.$post['permalink'].'#comment_form">'.$Language->get('COMMENTS').' ('.count($post['comments']).')</a>';
		}
	}

	// DEPRECATED
	// Last version available 4.0.3
	public static function tweet_link()
	{
		global $post;

		$url = helperUrl::post($post, true);
		return 'https://twitter.com/share?url='.urlencode($url);
	}

	public static function twitter($text=false)
	{
		global $post;

		$text = $text===false?'':$text;

		$url = helperUrl::post($post, true);
		return 'http://twitter.com/home?status='.urlencode($text.' '.$url);
	}

	public static function facebook($text=false)
	{
		global $post;

		$text = $text===false?'':$text;

		$url = helperUrl::post($post, true);
		return 'https://www.facebook.com/sharer/sharer.php?u='.urlencode($text.' '.$url);
	}

	public static function linkedin($title=false, $text=false)
	{
		global $post;

		$title = $title===false?'':$title;
		$text = $text===false?'':$text;

		$url = helperUrl::post($post, true);
		return 'http://www.linkedin.com/shareArticle?mini=true&url='.urlencode($url).'&title='.urlencode($title).'&summary='.urlencode($text);
	}

	public static function googleplus($text=false)
	{
		global $post;

		$text = $text===false?'':$text;

		$url = helperUrl::post($post, true);
		return 'https://plus.google.com/share?url='.urlencode($text.' '.$url);
	}

	public static function mailto($text=false)
	{
		global $post;

		$text = $text===false?'':$text;

		$url = helperUrl::post($post, true);
		return 'mailto:?subject='.rawurlencode(Blog::name().' - '.$text).'&amp;body='.urlencode($url);
	}

	public static function published($format=false)
	{
		global $post;
		global $settings;

		$format = $format===false?$settings['timestamp_format']:$format;

		return Date::format($post['pub_date_unix'], $format);
	}

	public static function modified($format=false)
	{
		global $post;
		global $settings;

		$format = $format===false?$settings['timestamp_format']:$format;

		return Date::format($post['mod_date_unix'], $format);
	}

	public static function content($full=false)
	{
		global $post;
		global $theme;

		if($post['type']=='quote')
		{
			$html = '<blockquote>'.$post['quote'].'</blockquote>';
		}
		elseif($post['type']=='video')
		{
			$video_width = !isset($theme['video_width'])?640:$theme['video_width'];
			$video_height = !isset($theme['video_height'])?320:$theme['video_height'];

			$video_info = Video::video_get_info($post['video'], $video_width, $video_height);

			$html  = '<div class="video-embed">';
			$html .= $video_info['embed'];
			$html .= '</div>';
		}
		else
		{
			if($full)
				$html = $post['content'][0];
			else
				$html = $post['content'][1];
		}

		return $html;
	}

}

?>
