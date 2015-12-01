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
		$disabled = empty($args['disabled']) ? '' : 'disabled';

		$html  = '<div class="uk-form-row">';

		if(!empty($args['label'])) {
			$html .= '<label for="'.$id.'" class="uk-form-label">'.$args['label'].'</label>';
		}

		$html .= '<div class="uk-form-controls">';

		$html .= '<input id="'.$id.'" name="'.$args['name'].'" type="'.$type.'" '.$class.' '.$placeholder.' autocomplete="off" '.$disabled.' value="'.$args['value'].'">';

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
		$class = empty($args['class']) ? '' : 'class="'.$args['class'].'"';

		$html = '<legend '.$class.'>'.$args['value'].'</legend>';
		echo $html;
	}

	public static function formButtonSubmit($args)
	{
		$html = '';
	}

	public static function uploader()
	{
		global $L;

		$html = '
		<div id="upload-drop" class="uk-placeholder uk-text-center">
		<i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right"></i>'.$L->g('Upload Image').'<br><a class="uk-form-file">'.$L->g('Drag and drop or click here').'<input id="upload-select" type="file"></a>
		</div>

		<div id="progressbar" class="uk-progress uk-hidden">
		<div class="uk-progress-bar" style="width: 0%;">0%</div>
		</div>
		';

		$html .= '<select id="jsimageList" class="uk-width-1-1" size="10">';
		$imagesList = Filesystem::listFiles(PATH_UPLOADS,'*','*',true);
		foreach($imagesList as $file) {
			$html .= '<option value="">'.basename($file).'</option>';
		}
		$html .= '</select>';

		$html .= '
		<div class="uk-form-row uk-margin-top">
		<button id="jsaddImage" class="uk-button uk-button-primary" type="button"><i class="uk-icon-angle-double-left"></i> '.$L->g('Insert Image').'</button>
		</div>
		';

		$html .= '
		<script>
		$(document).ready(function() {

			$("#jsaddImage").on("click", function() {
				var filename = $("#jsimageList option:selected").text();
				if(!filename.trim()) {
					return false;
				}
				var textareaValue = $("#jscontent").val();
				$("#jscontent").val(textareaValue + "<img src=\""+filename+"\" alt=\"\">" + "\n");
			});

			$(function()
			{
				var progressbar = $("#progressbar");
				var bar = progressbar.find(".uk-progress-bar");
				var settings =
				{
					type: "json",
					action: "'.HTML_PATH_ADMIN_ROOT.'ajax/uploader",
					allow : "*.(jpg|jpeg|gif|png)",

					loadstart: function() {
						bar.css("width", "0%").text("0%");
						progressbar.removeClass("uk-hidden");
					},

					progress: function(percent) {
						percent = Math.ceil(percent);
						bar.css("width", percent+"%").text(percent+"%");
					},

					allcomplete: function(response) {
						bar.css("width", "100%").text("100%");
						setTimeout(function() { progressbar.addClass("uk-hidden"); }, 250);
						$("#jsimageList").prepend("<option value=\'"+response.filename+"\' selected=\'selected\'>"+response.filename+"</option>");
					},

					notallowed: function(file, settings) {
						alert("'.$L->g('Supported image file types').' "+settings.allow);
					}
				};

				var select = UIkit.uploadSelect($("#upload-select"), settings);
				var drop = UIkit.uploadDrop($("#upload-drop"), settings);
			});

		});
		</script>';

		echo $html;
	}

	public static function profileUploader($username)
	{
		global $L;

		$html = '
		<div id="jsprogressBar" class="uk-progress uk-hidden">
		<div class="uk-progress-bar" style="width: 0%;">0%</div>
		</div>

		<div id="upload-drop" class="uk-placeholder uk-text-center">
		<i class="uk-icon-cloud-upload uk-margin-small-right"></i>'.$L->g('Upload Image').'<br><a class="uk-form-file">'.$L->g('Drag and drop or click here').'<input id="upload-select" type="file"></a>
		</div>
		';

		$html .= '
		<script>
		$(document).ready(function() {

			$(function()
			{
				var progressbar = $("#jsprogressBar");
				var bar = progressbar.find(".uk-progress-bar");
				var settings =
				{
					type: "json",
					action: "'.HTML_PATH_ADMIN_ROOT.'ajax/uploader",
					allow : "*.(jpg|jpeg|gif|png)",
					params: {"type":"profilePicture", "username":"'.$username.'"},

					loadstart: function() {
						bar.css("width", "0%").text("0%");
						progressbar.removeClass("uk-hidden");
					},

					progress: function(percent) {
						percent = Math.ceil(percent);
						bar.css("width", percent+"%").text(percent+"%");
					},

					allcomplete: function(response) {
						bar.css("width", "100%").text("100%");
						progressbar.addClass("uk-hidden");
						$("#jsprofilePicture").attr("src", "'.HTML_PATH_UPLOADS_PROFILES.$username.'.jpg?"+new Date().getTime());
					},

					notallowed: function(file, settings) {
						alert("'.$L->g('Supported image file types').' "+settings.allow);
					}
				};

				var select = UIkit.uploadSelect($("#upload-select"), settings);
				var drop = UIkit.uploadDrop($("#upload-drop"), settings);
			});

		});
		</script>';

		echo $html;
	}

}
