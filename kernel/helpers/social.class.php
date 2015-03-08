<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Social {

	public static function twitter_share($args = array())
	{
		// HTML Code
		$code  = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$args['url'].'" data-text="'.$args['text'].'">Tweet</a>';
		$code .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

		return $code;
	}

	public static function facebook_share($args = array())
	{
		// HTML Code
		$code  = '<fb:share-button type="button" href="'.$args['url'].'" />';
		$code .= '<script type="text/javascript" src="http://static.ak.fbcdn.net/connect.php/js/FB.Share"></script>';

		return $code;
	}

	public static function facebook_like($args = array())
	{
		// HTML Code
		$code = '<iframe src="https://www.facebook.com/plugins/like.php?href='.$args['url'].'&amp;layout=button_count" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;"></iframe>';

		return $code;
	}

}

?>
