<?php

class Bootstrap {

	public static function formTitle($args)
	{
		return '<h4 class="mt-4 mb-3">'.$args['title'].'</h4>';
	}

	public static function formInputTextBlock($args)
	{
		$id = 'js'.$args['name'];
		if (isset($args['id'])) {
			$id = $args['id'];
		}

		$class = 'form-control';
		if (isset($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$html = '<div class="form-group">';

		if (isset($args['label'])) {
			$html .= '<label for="'.$id.'">'.$args['label'].'</label>';
		}

		$html .= '<input type="text" class="'.$class.'" id="'.$id.'" name="'.$args['name'].'" placeholder="'.$args['placeholder'].'">';

		if (isset($args['tip'])) {
			$html .= '<small class="form-text text-muted">'.$args['tip'].'</small>';
		}

		$html .= '</div>';

		return $html;
	}

	public static function formInputFile($args)
	{
		$id = 'js'.$args['name'];
		if (isset($args['id'])) {
			$id = $args['id'];
		}

		$class = 'custom-file';
		if (isset($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$html  = '<div class="'.$class.'">';
		$html .= '<input type="file" class="custom-file-input" id="'.$id.'">';
		$html .= '<label class="custom-file-label" for="'.$id.'">'.$args['label'].'</label>';
		$html .= '</div>';

		return $html;
	}

	public static function formTextarea($args)
	{
		$id = 'js'.$args['name'];
		if (isset($args['id'])) {
			$id = $args['id'];
		}

		$class = 'form-control';
		if (isset($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$html = '<div class="form-group row">';

		if (isset($args['label'])) {
			$html .= '<label for="'.$id.'" class="col-sm-2 col-form-label">'.$args['label'].'</label>';
		}

		$html .= '<div class="col-sm-10">';
		$html .= '<textarea class="'.$class.'" id="'.$id.'" name="'.$args['name'].'" rows="'.$args['rows'].'" placeholder="'.$args['placeholder'].'"></textarea>';
		if (isset($args['tip'])) {
			$html .= '<small class="form-text text-muted">'.$args['tip'].'</small>';
		}
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public static function formInputText($args)
	{
		$id = 'js'.$args['name'];
		if (isset($args['id'])) {
			$id = $args['id'];
		}

		$class = 'form-control';
		if (isset($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$html = '<div class="form-group row">';

		if (isset($args['label'])) {
			$html .= '<label for="'.$id.'" class="col-sm-2 col-form-label">'.$args['label'].'</label>';
		}

		$html .= '<div class="col-sm-10">';
		$html .= '<input type="text" class="'.$class.'" id="'.$id.'" name="'.$args['name'].'" placeholder="'.$args['placeholder'].'">';
		if (isset($args['tip'])) {
			$html .= '<small class="form-text text-muted">'.$args['tip'].'</small>';
		}
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public static function formSelect($args)
	{
		$id = 'js'.$args['name'];
		if (isset($args['id'])) {
			$id = $args['id'];
		}

		$class = 'custom-select';
		if (isset($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$html = '<div class="form-group row">';

		if (isset($args['label'])) {
			$html .= '<label for="'.$id.'" class="col-sm-2 col-form-label">'.$args['label'].'</label>';
		}

		$html .= '<div class="col-sm-10">';
		$html .= '<select id="'.$id.'" name="'.$args['name'].'" class="'.$class.'">';
		foreach ($args['options'] as $key=>$value) {
			$html .= '<option '.(($key==$args['selected'])?'selected':'').' value="'.$key.'">'.$value.'</option>';
		}
		$html .= '</select>';
		if (isset($args['tip'])) {
			$html .= '<small class="form-text text-muted">'.$args['tip'].'</small>';
		}
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public static function formInputHidden($args)
	{
		return '<input type="hidden" id="js'.$args['name'].'" name="'.$args['name'].'" value="'.$args['value'].'">';
	}
}
