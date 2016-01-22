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
		if (e.which == 13) {
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

	public static function tagsAutocomplete($args)
	{
		// Tag array for Javascript
		$tagArray = 'var tagArray = [];';
		if(!empty($args['value'])) {
			$tagArray = 'var tagArray = ["'.implode('","', $args['value']).'"]';
		}
		$args['value'] = '';

		// Text input
		self::formInputText($args);

		echo '<div id="jstagList"></div>';

$script = '<script>

'.$tagArray.'

function insertTag(tag)
{
	// Clean the input text
	$("#jstags").val("");

	if(tag.trim()=="") {
		return true;
	}

	// Check if the tag is already inserted.
	var found = $.inArray(tag, tagArray);
	if(found == -1) {
		tagArray.push(tag);
		renderTagList();
	}
}

function removeTag(tag)
{
	var found = $.inArray(tag, tagArray);

	if(found => 0) {
		tagArray.splice(found, 1);
		renderTagList();
	}
}

function renderTagList()
{
	if(tagArray.length == 0) {
		$("#jstagList").html("");
	}
	else {
		$("#jstagList").html("<span>"+tagArray.join("</span><span>")+"</span>");
	}
}

$("#jstags").autoComplete({
	minChars: 1,
	source: function(term, suggest){
	term = term.toLowerCase();
	var choices = ['.$args['words'].'];
	var matches = [];
	for (i=0; i<choices.length; i++)
	    if (~choices[i].toLowerCase().indexOf(term)) matches.push(choices[i]);
	suggest(matches);
	},
	onSelect: function(e, value, item) {
		// Insert the tag when select whit the mouse click.
		insertTag(value);
	}
});

$(document).ready(function() {

	// When the page is loaded render the tags
	renderTagList();

	// Remove the tag when click on it.
	$("body").on("click", "#jstagList > span", function() {
		value = $(this).html();
		removeTag(value);
	});

	// Insert tag when press enter key.
	$("#jstags").keypress(function(e) {
		if (e.which == 13) {
			var value = $(this).val();
			insertTag(value);
		}
	});

	// When form submit.
	$("form").submit(function(e) {
		var list = tagArray.join(",");
		$("#jstags").val(list);
	});

});

</script>';

		echo $script;

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

if(empty($thumbnailList)) {
	$html .= '<div class="empty-images uk-block uk-text-center uk-block-muted">'.$L->g('There are no images').'</div>';
}

$html .= '
<a data-uk-modal href="#bludit-images-v8" class="moreImages uk-button">'.$L->g('More images').'</a>

</div>
';

$script = '
<script>

// Add thumbnail to Quick Images
function addQuickImages(filename)
{
	var imageSrc = HTML_PATH_UPLOADS_THUMBNAILS + filename;

	// Remove element if there are more than 6 thumbnails
	if ($("#bludit-quick-images-thumbnails > img").length > 5) {
		$("img:last-child", "#bludit-quick-images-thumbnails").remove();
	}

	// Add the new thumbnail to Quick images
	$("#bludit-quick-images-thumbnails").prepend("<img class=\"bludit-thumbnail\" data-filename=\""+filename+"\" src=\""+imageSrc+"\" alt=\"Thumbnail\">");
}

</script>
';

		echo $html.$script;
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

function addCoverImage(filename)
{
	var imageSrc = HTML_PATH_UPLOADS_THUMBNAILS + filename;

	// Cover image background
	$("#cover-image-thumbnail").attr("style","background-image: url("+imageSrc+")");

	// Form attribute
	$("#cover-image-upload-filename").attr("value", filename);
}

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
			$("#cover-image-delete").show();
			$(".empty-images").hide();

			// Add Cover Image
			addCoverImage(response.filename);

			// Add thumbnail to Quick Images
			addQuickImages(response.filename);

			// Add thumbnail to Bludit Images V8
			addBluditImagev8(response.filename);
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
	$html .= '<img class="bludit-thumbnail" src="'.HTML_PATH_UPLOADS_THUMBNAILS.$filename.'" data-filename="'.$filename.'" alt="Thumbnail">';
}

$html .= '
	</div>
';

if(empty($thumbnailList)) {
	$html .= '<div class="empty-images uk-block uk-text-center uk-block-muted">'.$L->g('There are no images').'</div>';
}

$html .= '
	<div class="uk-modal-footer">
		'.$L->g('Double click on the image to add it').' <a href="" class="uk-modal-close">'.$L->g('Click here to cancel').'</a>
	</div>

</div>
</div>
';

$script = '
<script>

// Add thumbnail to Bludit Images v8
function addBluditImagev8(filename)
{
	var imageSrc = HTML_PATH_UPLOADS_THUMBNAILS + filename;

	// Add the new thumbnail to Bludit Images v8
	$("#bludit-images-v8-thumbnails").prepend("<img class=\"bludit-thumbnail\" data-filename=\""+filename+"\" src=\""+imageSrc+"\" alt=\"Thumbnail\">");
}

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
			$(".empty-images").hide();

			// Add thumbnail to Bludit Images V8
			addBluditImagev8(response.filename);

			// Add thumbnail to Quick Images
			addQuickImages(response.filename);
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