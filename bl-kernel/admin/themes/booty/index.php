<!DOCTYPE html>
<html>
<head>
	<title><?php echo $layout['title'] ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME ?>css/bootstrap-4.0.0-min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME ?>css/jquery-auto-complete-1.0.7.css">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME ?>open-iconic-master/font/css/open-iconic-bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME ?>css/quill.snow.css">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_ADMIN_THEME ?>css/bludit.css">

	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/jquery-3.3.1.min.js?version='.BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/jquery-auto-complete-1.0.7.min.js?version='.BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/bootstrap-bundle-4.0.0.min.js?version='.BLUDIT_VERSION ?>"></script>
	<script charset="utf-8" src="<?php echo HTML_PATH_ADMIN_THEME.'js/quill.min.js?version='.BLUDIT_VERSION ?>"></script>
</head>
<body>

<!-- TOPBAR -->
<?php include('html/topbar.php'); ?>

<div class="container">
	<!-- 25%/75% split on large devices, small, medium devices hide -->
	<div class="row">

		<!-- LEFT SIDEBAR - Display only on large devices -->
		<div class="sidebar col-lg-2 d-none d-lg-block">
		<?php include('html/sidebar.php'); ?>
		</div>

		<!-- RIGHT MAIN -->
		<div class="col-lg-10 pt-4">

			<!-- TABS -->
			<ul class="nav nav-tabs" id="dynamicTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link" id="content-tab" data-toggle="tab" href="#content" role="tab" aria-controls="content" aria-selected="true">Content</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" id="images-tab" data-toggle="tab" href="#images" role="tab" aria-controls="images" aria-selected="false">Images</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " id="options-tab" data-toggle="tab" href="#options" role="tab" aria-controls="options" aria-selected="false">Options</a>
				</li>
			</ul>
			<form class="tab-content mt-3" id="dynamicTabContent">

				<!-- TABS CONTENT -->
				<div class="tab-pane" id="content" role="tabpanel" aria-labelledby="content-tab">

					<?php
						// Title
						echo Bootstrap::formInputTextBlock(array(
							'name'=>'title',
							'placeholder'=>'Enter title',
							'class'=>'form-control-lg'
						));
					?>

					<div class="form-group mt-2">
						<div id="editor"></div>
					</div>

					<div class="form-group mt-2">
						<button type="button" class="btn btn-primary">Save</button>
						<button type="button" class="btn">Save as draft</button>
						<button type="button" class="btn">Cancel</button>
					</div>

				</div>
				<!-- TABS IMAGES -->
				<div class="tab-pane show active" id="images" role="tabpanel" aria-labelledby="images-tab">

					<?php
						echo Bootstrap::formTitle(array('title'=>'Cover image'));
					?>

					<img class="img-thumbnail" alt="200x200" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1627e1b2b7e%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1627e1b2b7e%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.65%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true" style="width: 100px; height: 100px;">

					<?php
						echo Bootstrap::formTitle(array('title'=>'Select images'));
					?>

					<button type="button" class="btn" data-toggle="modal" data-target="#jsbluditMediaModal">Media Manager</button>

				</div>
				<!-- TABS OPTIONS -->
				<div class="tab-pane" id="options" role="tabpanel" aria-labelledby="options-tab">
				<h4 class="mt-4 mb-3">General</h4>
					<?php
						// Category
						echo Bootstrap::formSelect(array(
							'name'=>'category',
							'label'=>'Category',
							'selected'=>'',
							'options'=>array(
								''=>'- Uncategorized -',
								'music'=>'Music',
								'videos'=>'Videos'
							)
						));

						// Tags
						echo Bootstrap::formInputText(array(
							'name'=>'tags',
							'label'=>'Tags',
							'placeholder'=>'Tags separeted by comma'
						));

						// Description
						echo Bootstrap::formTextarea(array(
							'name'=>'description',
							'label'=>'Description',
							'placeholder'=>'Small description about the content',
							'rows'=>'4'
						));
					?>
					<h4 class="mt-4 mb-3">Advanced</h4>
					<?php
						// Date
						echo Bootstrap::formInputText(array(
							'name'=>'date',
							'label'=>'Date',
							'placeholder'=>'YYYY-MM-DD hh:mm:ss'
						));

						// Type
						echo Bootstrap::formSelect(array(
							'name'=>'type',
							'label'=>'Type',
							'selected'=>'',
							'options'=>array(
								''=>'- Default -',
								'sticky'=>'Sticky',
								'static'=>'Static'
							)
						));

						// Parent
						echo Bootstrap::formInputText(array(
							'name'=>'parent',
							'label'=>'Parent',
							'placeholder'=>'Start writing the title of the page parent'
						));

						// Position
						echo Bootstrap::formInputText(array(
							'name'=>'position',
							'label'=>'Position',
							'placeholder'=>''
						));

						// Friendly URL
						echo Bootstrap::formInputText(array(
							'name'=>'slug',
							'label'=>'Friendly URL',
							'placeholder'=>'Leave empty for automaticly complete'
						));

						// Template
						echo Bootstrap::formInputText(array(
							'name'=>'template',
							'label'=>'Template',
							'placeholder'=>''
						));
					?>

				</div>

			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {

	var quill = new Quill('#editor', {
		modules: {
		toolbar: [
		[{ header: [1, 2, false] }],
		['bold', 'italic', 'underline'],
		['image', 'code-block']
		]
		},
		placeholder: 'Content, support Markdown and HTML.',
		theme: 'snow'  // or 'bubble'
	});

	// Template autocomplete
	$('input[name="template"]').autoComplete({
		minChars: 2,
		source: function(term, suggest){
			term = term.toLowerCase();
			var choices = ['ActionScript', 'Acti', 'Asp'];
			var matches = [];
			for (i=0; i<choices.length; i++)
				if (~choices[i].toLowerCase().indexOf(term)) matches.push(choices[i]);
			suggest(matches);
		}
	});

	// Parent autocomplete
	var xhr;
	$("#jsparent").autoComplete({
		source: function(term, response){
			try { xhr.abort(); } catch(e){}
			xhr = $.getJSON('http://localhost:8000/parents.json', { q: term }, function(data){ response(data); });
		},
		onSelect: function(e, term, item){
			console.log(term);
			console.log(item);
		}
	});

});
</script>

<?php include('html/media.php'); ?>

</body>
</html>
