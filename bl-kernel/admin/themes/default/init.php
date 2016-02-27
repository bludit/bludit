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

		$script = '<script>

		$(document).ready(function() {

			// Prevent the form submit when press enter key.
			$("form").keypress(function(e) {
				if( (e.which == 13) && (e.target.type !== "textarea") )  {
					return false;
				}
			});

		});

		</script>';

		echo $html.$script;
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

	public static function tags($args)
	{
		global $L;
		// Javascript code
		include(PATH_JS.'bludit-tags.js');

		$html  = '<div id="bludit-tags" class="uk-form-row">';

		$html .= '<input type="hidden" id="jstags" name="tags" value="">';

		$html .= '<label for="jstagInput" class="uk-form-label">'.$args['label'].'</label>';

		$html .= '<div class="uk-form-controls">';
		$html .= '<input id="jstagInput" type="text" class="uk-width-1-2" autocomplete="off">';
		$html .= '<button id="jstagAdd" class="uk-button">'.$L->g('Add').'</button>';

		$html .= '<div id="jstagList">';

		foreach($args['allTags'] as $tag) {
			$html .= '<span data-tag="'.$tag.'" class="'.( in_array($tag, $args['selectedTags'])?'select':'unselect' ).'">'.$tag.'</span>';
		}

		$html .= '</div>';
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

	public static function bluditQuickImages()
	{
		// Javascript code
		include(PATH_JS.'bludit-quick-images.js');

		global $L;

		$html = '<!-- BLUDIT QUICK IMAGES -->';
		$html .= '
		<div id="bludit-quick-images">
		<div id="bludit-quick-images-thumbnails" onmousedown="return false">
		';

		$thumbnailList = Filesystem::listFiles(PATH_UPLOADS_THUMBNAILS,'*','*',true);
		array_splice($thumbnailList, THUMBNAILS_AMOUNT);
		foreach($thumbnailList as $file) {
			$filename = basename($file);
			$html .= '<img class="bludit-thumbnail" data-filename="'.$filename.'" src="'.HTML_PATH_UPLOADS_THUMBNAILS.$filename.'" alt="Thumbnail">';
		}

		$html .= '
		</div>
		';

		$html .= '<div class="empty-images uk-block uk-text-center uk-block-muted" '.( !empty($thumbnailList)?'style="display:none"':'' ).'>'.$L->g('There are no images').'</div>';

		$html .= '
		<a data-uk-modal href="#bludit-images-v8" class="moreImages uk-button"><i class="uk-icon-folder-o"></i> '.$L->g('More images').'</a>

		</div>
		';

		echo $html;
	}

	public static function bluditCoverImage($coverImage="")
	{
		// Javascript code
		include(PATH_JS.'bludit-cover-image.js');

		global $L;

		$style = '';
		if(!empty($coverImage)) {
			$style = 'background-image: url('.HTML_PATH_UPLOADS_THUMBNAILS.$coverImage.')';
		}

		$html = '<!-- BLUDIT COVER IMAGE -->';
		$html .= '
		<div id="bludit-cover-image">
		<div id="cover-image-thumbnail" class="uk-form-file uk-placeholder uk-text-center" style="'.$style.'">

			<input type="hidden" name="coverImage" id="cover-image-upload-filename" value="'.$coverImage.'">

			<div id="cover-image-upload" '.( empty($coverImage)?'':'style="display: none;"' ).'>
				<div><i class="uk-icon-picture-o"></i> '.$L->g('Cover image').'</div>
				<div style="font-size:0.8em;">'.$L->g('Drag and drop or click here').'<input id="cover-image-file-select" type="file"></div>
			</div>

			<div id="cover-image-delete" '.( empty($coverImage)?'':'style="display: block;"' ).'>
				<div><i class="uk-icon-trash-o"></i></div>
			</div>

			<div id="cover-image-progressbar" class="uk-progress">
				<div class="uk-progress-bar" style="width: 0%;">0%</div>
			</div>

		</div>
		</div>
		';

		echo $html;
	}

	public static function bluditMenuV8()
	{
		// Javascript code
		include(PATH_JS.'bludit-menu-v8.js');

		global $L;

		$html  = '<!-- BLUDIT MENU V8 -->';
		$html .= '
		<ul id="bludit-menuV8">
			<li id="bludit-menuV8-insert"><i class="uk-icon-plus"></i>'.$L->g('Insert image').'</li>
			<li id="bludit-menuV8-cover"><i class="uk-icon-picture-o"></i>'.$L->g('Set as cover image').'</li>
			<li id="bludit-menuV8-delete"><i class="uk-icon-trash"></i>'.$L->g('Delete image').'</li>
		</ul>
		';

		echo $html;
	}

	public static function bluditImagesV8()
	{
		// Javascript code
		include(PATH_JS.'bludit-images-v8.js');

		global $L;

		$html = '<!-- BLUDIT IMAGES V8 -->';
		$html .= '
		<div id="bludit-images-v8" class="uk-modal">
		<div class="uk-modal-dialog">

			<div id="bludit-images-v8-upload" class="uk-form-file uk-placeholder uk-text-center">

				<div id="bludit-images-v8-drag-drop">
					<div><i class="uk-icon-picture-o"></i> '.$L->g('Upload image').'</div>
					<div style="font-size:0.8em;">'.$L->g('Drag and drop or click here').'<input id="bludit-images-v8-file-select" type="file"></div>
				</div>

				<div id="bludit-images-v8-progressbar" class="uk-progress">
					<div class="uk-progress-bar" style="width: 0%;">0%</div>
				</div>

			</div>

			<div id="bludit-images-v8-thumbnails">
		';

		$thumbnailList = Filesystem::listFiles(PATH_UPLOADS_THUMBNAILS,'*','*',true);
		foreach($thumbnailList as $file) {
			$filename = basename($file);
			$html .= '<img class="bludit-thumbnail" src="'.HTML_PATH_UPLOADS_THUMBNAILS.$filename.'" data-filename="'.$filename.'" alt="Thumbnail">';
		}

		$html .= '
			</div>
		';

		$html .= '<div class="empty-images uk-block uk-text-center uk-block-muted" '.( !empty($thumbnailList)?'style="display:none"':'' ).'>'.$L->g('There are no images').'</div>';

		$html .= '
			<div class="uk-modal-footer">
				'.$L->g('Click on the image for options').' <a href="" class="uk-modal-close">'.$L->g('Click here to cancel').'</a>
			</div>

		</div>
		</div>
		';

		echo $html;
	}



	public static function profileUploader($username)
	{
		global $L;

$html = '<!-- BLUDIT PROFILE UPLOADER -->';

$html .= '
<div id="bludit-profile-picture">

	<div id="bludit-profile-picture-image">';

	if(file_exists(PATH_UPLOADS_PROFILES.$username.'.png')) {
		$html .= '<img class="uk-border-rounded" src="'.HTML_PATH_UPLOADS_PROFILES.$username.'.png" alt="Profile picture">';
	}
	else {
		$html .= '<div class="uk-block uk-border-rounded uk-block-muted uk-block-large">'.$L->g('Profile picture').'</div>';
	}

$html .= '
	</div>

	<div id="bludit-profile-picture-progressbar" class="uk-progress">
		<div class="uk-progress-bar" style="width: 0%;">0%</div>
	</div>

	<div id="bludit-profile-picture-drag-drop" class="uk-form-file uk-placeholder uk-text-center">
		<div>'.$L->g('Upload image').'</div>
		<div style="font-size:0.8em;">'.$L->g('Drag and drop or click here').'<input id="bludit-profile-picture-file-select" type="file"></div>
	</div>

</div>
';

$script = '
<script>
$(document).ready(function() {

	var settings =
	{
		type: "json",
		action: HTML_PATH_ADMIN_ROOT+"ajax/uploader",
		allow : "*.(jpg|jpeg|gif|png)",
		params: {"type":"profilePicture", "username":"'.$username.'"},

		loadstart: function() {
			$("#bludit-profile-picture-progressbar").find(".uk-progress-bar").css("width", "0%").text("0%");
			$("#bludit-profile-picture-progressbar").show();
		},

		progress: function(percent) {
			percent = Math.ceil(percent);
			$("#bludit-profile-picture-progressbar").find(".uk-progress-bar").css("width", percent+"%").text(percent+"%");
		},

		allcomplete: function(response) {
			$("#bludit-profile-picture-progressbar").find(".uk-progress-bar").css("width", "100%").text("100%");
			$("#bludit-profile-picture-progressbar").hide();

			$("#bludit-profile-picture-image").html("<img class=\"uk-border-rounded\" src=\"'.HTML_PATH_UPLOADS_PROFILES.$username.'.png?time='.time().'\">");
		},

		notallowed: function(file, settings) {
			alert("'.$L->g('Supported image file types').' "+settings.allow);
		}
	};

	UIkit.uploadSelect($("#bludit-profile-picture-file-select"), settings);
	UIkit.uploadDrop($("#bludit-profile-picture-drag-drop"), settings);

});
</script>
';

		echo $html.$script;
	}

}