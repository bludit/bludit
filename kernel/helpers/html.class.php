<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Html {

	private static function get_attributes($array = array())
	{
		unset($array['content']);

		$attributes = '';

		if(isset($array['hidden']) && $array['hidden'])
		{
			$attributes .= 'style="display:none" ';
		}

		unset($array['hidden']);

		foreach( $array as $key=>$value )
		{
			$attributes .= $key.'="'.$value.'" ';
		}

		return($attributes);
	}

	public static function h1($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<h1 '.$attributes.'>'.$array['content'].'</h1>' );
	}

	public static function h2($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<h2 '.$attributes.'>'.$array['content'].'</h2>' );
	}

	public static function h3($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<h3 '.$attributes.'>'.$array['content'].'</h3>' );
	}

	public static function h4($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<h4 '.$attributes.'>'.$array['content'].'</h4>' );
	}

	public static function blockquote($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<blockquote '.$attributes.'>'.$array['content'].'</blockquote>' );
	}

	public static function p($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<p '.$attributes.'>'.$array['content'].'</p>' );
	}

	public static function separator($array = array(), $top=false, $hidden=false)
	{
		if(isset($array['class']))
		{
			$array['class'] = 'separator '.$array['class'];
		}
		else
		{
			$array['class'] = 'separator';
		}

		if($hidden)
			$hidden = 'style="display:none"';
		else
			$hidden = '';

		$attributes = self::get_attributes($array);

		return( '<header '.$hidden.' class="'.($top?'separator_top':'separator').'"><div '.$attributes.'>'.$array['content'].'</div></header>' );
	}

	public static function form_open($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<form '.$attributes.' >' );
	}

	public static function form_close()
	{
		return( '</form>' );
	}

	public static function input($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<input '.$attributes.'/>' );
	}

	public static function checkbox($array = array(), $checked = false)
	{
		$attributes = self::get_attributes($array);

		if( $checked )
			return( '<input type="checkbox" '.$attributes.' checked="checked" value="1" />' );
		else
			return( '<input type="checkbox" '.$attributes.' value="1"/>' );
	}

	public static function radio($array = array(), $checked = false)
	{
		$attributes = self::get_attributes($array);

		if( $checked )
			return( '<input type="radio" '.$attributes.' checked="checked" />' );
		else
			return( '<input type="radio" '.$attributes.'/>' );
	}

	public static function textarea($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<textarea '.$attributes.'>'.$array['content'].'</textarea>' );
	}

	public static function label($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<label '.$attributes.'>'.$array['content'].'</label>' );
	}

	public static function select($array = array(), $options = array(), $selected)
	{
		$attributes = self::get_attributes($array);

		$tmp = '<select '.$attributes.'>';
		foreach( $options as $key=>$value )
		{
			if( $key == $selected)
				$attr = 'selected="selected"';
			else
				$attr = '';

			$tmp .= '<option value="'.$key.'" '.$attr.'>'.$value.'</option>';
		}
		$tmp .= '</select>';

		return( $tmp );
	}

	public static function div($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<div '.$attributes.'>'.$array['content'].'</div>' );
	}

	public static function div_open($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<div '.$attributes.'>' );
	}

	public static function div_close()
	{
		return( '</div>' );
	}

	public static function article_open($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<article '.$attributes.'>' );
	}

	public static function article_close()
	{
		return( '</article>' );
	}

	public static function header_open($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<header '.$attributes.'>' );
	}

	public static function header_close()
	{
		return( '</header>' );
	}

	public static function link($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<a '.$attributes.'>'.$array['content'].'</a>' );
	}

	public static function span($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<span '.$attributes.'>'.$array['content'].'</span>' );
	}

	public static function img($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<img '.$attributes.'/>' );
	}

	public static function ul($array = array())
	{
		$attributes = self::get_attributes($array);

		return( '<ul '.$attributes.'>'.$array['content'].'</ul>' );
	}

	public static function banner($msg, $success, $error)
	{
		if( $success )
			return('<div class="notification_success">'.$msg.'</div>');
		elseif( $error )
			return('<div class="notification_error">'.$msg.'</div>');
	}

}

?>
