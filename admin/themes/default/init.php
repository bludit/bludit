<?php

class HTML {

	public static function title($args)
	{
		$html = '<h2 class="title"><i class="uk-icon-'.$args['icon'].'"></i> '.$args['title'].'</h2>';
		echo $html;
	}

	public static function formOpen($args)
	{
		$class = empty($args['class']) ? '' : ' '.$args['class'];
		$id = empty($args['id']) ? '' : 'id="'.$args['id'].'"';

		$html = '<form class="uk-form'.$class.'" '.$id.' method="post" action="" autocomplete="off">';
		echo $html;
	}

	public static function formClose()
	{
		$html = '</form>';
		echo $html;
	}

	// label, name, value, tip
	public static function formInputText($args)
	{
		$id = 'js'.$args['name'];
		$type = isset($args['type']) ? $args['type'] : 'text';
		$class = empty($args['class']) ? '' : 'class="'.$args['class'].'"';
		$placeholder = empty($args['placeholder']) ? '' : 'placeholder="'.$args['placeholder'].'"';

		$html  = '<div class="uk-form-row">';

		if(!empty($args['label'])) {
			$html .= '<label for="'.$id.'" class="uk-form-label">'.$args['label'].'</label>';
		}

		$html .= '<div class="uk-form-controls">';

		$html .= '<input id="'.$id.'" name="'.$args['name'].'" type="'.$type.'" '.$class.' '.$placeholder.' value="'.$args['value'].'">';

		if(!empty($args['tip'])) {
			$html .= '<p class="uk-form-help-block">'.$args['tip'].'</p>';
		}

		$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}

	public static function formInputPassword($args)
	{
		$args['type'] = 'password';
		self::formInputText($args);
	}

	public static function formTextarea($args)
	{
		$id = 'js'.$args['name'];
		$type = isset($args['type']) ? $args['type'] : 'text';
		$class = empty($args['class']) ? '' : 'class="'.$args['class'].'"';
		$placeholder = empty($args['placeholder']) ? '' : 'placeholder="'.$args['placeholder'].'"';
		$rows = empty($args['rows']) ? '' : 'rows="'.$args['rows'].'"';

		$html  = '<div class="uk-form-row">';

		if(!empty($args['label'])) {
			$html .= '<label for="'.$id.'" class="uk-form-label">'.$args['label'].'</label>';
		}

		$html .= '<div class="uk-form-controls">';

		$html .= '<textarea id="'.$id.'" name="'.$args['name'].'" '.$class.' '.$placeholder.' '.$rows.'>'.$args['value'].'</textarea>';

		if(!empty($args['tip'])) {
			$html .= '<p class="uk-form-help-block">'.$args['tip'].'</p>';
		}

		$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}

	public static function formSelect($args)
	{
		$id = 'js'.$args['name'];
		$type = isset($args['type']) ? $args['type'] : 'text';
		$class = empty($args['class']) ? '' : 'class="'.$args['class'].'"';

		$html  = '<div class="uk-form-row">';
		$html .= '<label for="'.$id.'" class="uk-form-label">'.$args['label'].'</label>';
		$html .= '<div class="uk-form-controls">';
		$html .= '<select id="'.$id.'" name="'.$args['name'].'" '.$class.'>';
		foreach($args['options'] as $key=>$value) {
			$html .= '<option value="'.$key.'"'.( ($args['selected']==$key)?' selected="selected"':'').'>'.$value.'</option>';
		}
		$html .= '</select>';
		$html .= '<p class="uk-form-help-block">'.$args['tip'].'</p>';
		$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}

	public static function formInputHidden($args)
	{
		$id = 'js'.$args['name'];

		$html  = '<input type="hidden" id="'.$id.'" name="'.$args['name'].'" value="'.$args['value'].'">';
		echo $html;
	}

	public static function legend($args)
	{
		$html = '<legend>'.$args['value'].'</legend>';
		echo $html;
	}

	public static function formButtonSubmit($args)
	{
		$html = '';
	}

}
