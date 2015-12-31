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

	public static function bluditQuickImages()
	{

$html = '<!-- BLUDIT QUICK IMAGES -->';
$html .= '
<div id="bludit-quick-images">
<h4 class="label">Images</h4>

<div id="bludit-quick-images-thumbnails">
';

$thumbnailList = Filesystem::listFiles(PATH_UPLOADS_THUMBNAILS,'*','*',true);
array_splice($thumbnailList, THUMBNAILS_AMOUNT);
foreach($thumbnailList as $file) {
	$filename = basename($file);
	$html .= '<img class="bludit-thumbnail uk-thumbnail" data-filename="'.$filename.'" src="'.HTML_PATH_UPLOADS_THUMBNAILS.$filename.'" alt="Thumbnail">';
}

$html .= '
</div>

<a data-uk-modal href="#bludit-images-v8" class="moreImages uk-button">More images</a>

</div>
';

		echo $html;
	}

	public static function bluditCoverImage($coverImage="")
	{
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

$script = '
<script>
$(document).ready(function() {

	$("#cover-image-delete").on("click", function() {
		$("#cover-image-thumbnail").attr("style","");
		$("#cover-image-upload-filename").attr("value","");
		$("#cover-image-delete").hide();
		$("#cover-image-upload").show();
	});

	var settings =
	{
		type: "json",
		action: HTML_PATH_ADMIN_ROOT+"ajax/uploader",
		allow : "*.(jpg|jpeg|gif|png)",
		params: {"type":"cover-image"},

		loadstart: function() {
			$("#cover-image-progressbar").find(".uk-progress-bar").css("width", "0%").text("0%");
			$("#cover-image-progressbar").show();
			$("#cover-image-delete").hide();
			$("#cover-image-upload").hide();
		},

		progress: function(percent) {
			percent = Math.ceil(percent);
			$("#cover-image-progressbar").find(".uk-progress-bar").css("width", percent+"%").text(percent+"%");
		},

		allcomplete: function(response) {
			$("#cover-image-progressbar").find(".uk-progress-bar").css("width", "100%").text("100%");
			$("#cover-image-progressbar").hide();

			var imageSrc = HTML_PATH_UPLOADS_THUMBNAILS+response.filename;
			$("#cover-image-thumbnail").attr("style","background-image: url("+imageSrc+")");
			$("#cover-image-delete").show();

			$("img:last-child", "#bludit-quick-images-thumbnails").remove();
			$("#bludit-quick-images-thumbnails").prepend("<img class=\"bludit-thumbnail uk-thumbnail\" data-filename=\""+response.filename+"\" src=\""+imageSrc+"\" alt=\"Thumbnail\">");

			$("#cover-image-upload-filename").attr("value",response.filename);
		},

		notallowed: function(file, settings) {
			alert("'.$L->g('Supported image file types').' "+settings.allow);
		}
	};

	UIkit.uploadSelect($("#cover-image-file-select"), settings);
	UIkit.uploadDrop($("#cover-image-thumbnail"), settings);

});
</script>
';
		echo $html.$script;
	}

	public static function bluditImagesV8()
	{
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
		$html .= '<img class="bludit-thumbnail uk-thumbnail" src="'.HTML_PATH_UPLOADS_THUMBNAILS.$filename.'" data-filename="'.$filename.'" alt="Thumbnail">';
	}

$html .= '
	</div>

	<div class="uk-modal-footer">
		Double click on the image to add it or <a href="" class="uk-modal-close">click here to cancel</a>
	</div>

</div>
</div>
';

$script = '
<script>
$(document).ready(function() {

	// Add border when select an thumbnail
	$("body").on("click", "img.bludit-thumbnail", function() {
		$(".bludit-thumbnail").css("border", "1px solid #ddd");
		$(this).css("border", "solid 3px orange");
	});

	// Hide the modal when double click on thumbnail.
	$("body").on("dblclick", "img.bludit-thumbnail", function() {
		var modal = UIkit.modal("#bludit-images-v8");
		if ( modal.isActive() ) {
			modal.hide();
		}
	});

	// Event for double click for insert the image is in each editor plugin
	// ..

	var settings =
	{
		type: "json",
		action: HTML_PATH_ADMIN_ROOT+"ajax/uploader",
		allow : "*.(jpg|jpeg|gif|png)",
		params: {"type":"bludit-images-v8"},

		loadstart: function() {
			$("#bludit-images-v8-progressbar").find(".uk-progress-bar").css("width", "0%").text("0%");
			$("#bludit-images-v8-drag-drop").hide();
			$("#bludit-images-v8-progressbar").show();
		},

		progress: function(percent) {
			percent = Math.ceil(percent);
			$("#bludit-images-v8-progressbar").find(".uk-progress-bar").css("width", percent+"%").text(percent+"%");
		},

		allcomplete: function(response) {
			$("#bludit-images-v8-progressbar").find(".uk-progress-bar").css("width", "100%").text("100%");
			$("#bludit-images-v8-progressbar").hide();
			$("#bludit-images-v8-drag-drop").show();

			// Images V8 Thumbnails
			var imageSrc = HTML_PATH_UPLOADS_THUMBNAILS+response.filename;
			$("#bludit-images-v8-thumbnails").prepend("<img class=\"bludit-thumbnail uk-thumbnail\" data-filename=\""+response.filename+"\" src=\""+imageSrc+"\" alt=\"Thumbnail\">");

			// Quick images Thumbnails
			$("img:last-child", "#bludit-quick-images-thumbnails").remove();
			$("#bludit-quick-images-thumbnails").prepend("<img class=\"bludit-thumbnail uk-thumbnail\" data-filename=\""+response.filename+"\" src=\""+imageSrc+"\" alt=\"Thumbnail\">");
		},

		notallowed: function(file, settings) {
			alert("'.$L->g('Supported image file types').' "+settings.allow);
		}
	};

	UIkit.uploadSelect($("#bludit-images-v8-file-select"), settings);
	UIkit.uploadDrop($("#bludit-images-v8-upload"), settings);
});
</script>
';
		echo $html.$script;
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
						$("#jsprofilePicture").html("<img class=\"uk-border-rounded\" src=\"'.HTML_PATH_UPLOADS_PROFILES.$username.'.jpg\">");
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