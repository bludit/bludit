<?php

class Bootstrap {

	public static function css($filename) {
		if (is_array($filename)) {
			$tmp = '';
			foreach ($filename as $file) {
				$tmp .= '<link rel="stylesheet" type="text/css" href="'.HTML_PATH_ADMIN_THEME.'css/'.$file.'?version='.BLUDIT_VERSION.'">'.PHP_EOL;
			}
		} else {
			$tmp = '<link rel="stylesheet" type="text/css" href="'.HTML_PATH_ADMIN_THEME.'css/'.$file.'?version='.BLUDIT_VERSION.'">'.PHP_EOL;
		}
		return $tmp;
	}

	public static function js($filename) {
		if (is_array($filename)) {
			$tmp = '';
			foreach ($filename as $file) {
				$tmp .= '<script charset="utf-8" src="'.HTML_PATH_ADMIN_THEME.'js/'.$file.'?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
			}
		} else {
			$tmp = '<script charset="utf-8" src="'.HTML_PATH_ADMIN_THEME.'js/'.$file.'?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
		}
		return $tmp;
	}

	public static function link($args)
	{
		$options = 'href="'.$args['href'].'"';
		if (isset($args['class'])) {
			$options .= ' class="'.$args['class'].'"';
		}
		if (isset($args['target'])) {
			$options .= ' target="'.$args['target'].'"';
		}

		if (isset($args['icon'])) {
			return '<a '.$options.'><span class="oi oi-'.$args['icon'].'" style="font-size: 0.7em;"></span> '.$args['title'].'</a>';
		}

		return '<a '.$options.'>'.$args['title'].'</a>';
	}

	public static function pageTitle($args)
	{
		return '<h2 class="mt-0 mb-3"><span class="oi oi-'.$args['icon'].'" style="font-size: 0.7em;"></span> '.$args['title'].'</h2>';
	}

	public static function formOpen($args)
	{
		$class = empty($args['class']) ? '' : ' '.$args['class'];
		$id = empty($args['id']) ? '' : ' id="'.$args['id'].'" ';
		$enctype = empty($args['enctype']) ? '' : ' enctype="'.$args['enctype'].'" ';

		$html = '<form class="'.$class.'" '.$enctype.$id.' method="post" action="" autocomplete="off">';
		return $html;
	}

	public static function formClose()
	{
		$html = '</form>';

		$script = '<script>
		$(document).ready(function() {
			// Prevent the form submit when press enter key.
			$("form").keypress(function(e) {
				if ((e.which == 13) && (e.target.type !== "textarea")) {
					return false;
				}
			});
		});
		</script>';

		return $html.$script;
	}

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

		$html .= '<input type="text" value="'.$args['value'].'" class="'.$class.'" id="'.$id.'" name="'.$args['name'].'" placeholder="'.$args['placeholder'].'">';

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

	public static function formInputGroupText($args)
	{
		$label = $args['label'];
		$labelInside = $args['labelInside'];
		$tip = $args['tip'];
		$value = $args['value'];
		$name = $args['name'];
		$id = 'js'.$name;
		if (isset($args['id'])) {
			$id = $args['id'];
		}
		$disabled = isset($args['disabled'])?'disabled':'';

return <<<EOF
<div class="form-group">
	<label for="$id">$label</label>
	<div class="input-group">
		<div class="input-group-prepend">
			<span class="input-group-text" id="$id">$labelInside</span>
		</div>
		<input id="$id" name="$name" value="$value" type="text" class="form-control" $disabled>
	</div>
	<small class="form-text text-muted">$tip</small>
</div>
EOF;
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
