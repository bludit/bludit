<!-- TABS -->
<ul class="nav nav-tabs" id="dynamicTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="content-tab" data-toggle="tab" href="#content" role="tab" aria-controls="content" aria-selected="true">Content</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="images-tab" data-toggle="tab" href="#images" role="tab" aria-controls="images" aria-selected="false">Images</a>
	</li>
	<li class="nav-item">
		<a class="nav-link " id="options-tab" data-toggle="tab" href="#options" role="tab" aria-controls="options" aria-selected="false">Options</a>
	</li>
</ul>
<form class="tab-content mt-3" id="dynamicTabContent">

	<?php
		// Token CSRF
		echo Bootstrap::formInputHidden(array(
			'name'=>'tokenCSRF',
			'value'=>$Security->getTokenCSRF()
		));

		// Parent
		echo Bootstrap::formInputHidden(array(
			'name'=>'parent',
			'value'=>''
		));

		// Status = published, draft, sticky, static
		echo Bootstrap::formInputHidden(array(
			'name'=>'status',
			'value'=>'published'
		));
	?>

	<!-- TABS CONTENT -->
	<div class="tab-pane show active" id="content" role="tabpanel" aria-labelledby="content-tab">

		<?php
			// Title
			echo Bootstrap::formInputTextBlock(array(
				'name'=>'title',
				'placeholder'=>'Enter title',
				'class'=>'form-control-lg'
			));
		?>

		<div class="form-group mt-2">
			<div id="jscontent" name="content"></div>
		</div>

		<div class="form-group mt-2">
			<button type="button" class="btn btn-primary">Save</button>
			<button type="button" class="btn" id="jssaveAsDraft">Save as draft</button>
			<button type="button" class="btn">Cancel</button>
		</div>

	</div>

	<!-- TABS IMAGES -->
	<div class="tab-pane" id="images" role="tabpanel" aria-labelledby="images-tab">

		<?php
			echo Bootstrap::formTitle(array('title'=>'Select images'));
		?>

		<button type="button" class="btn" data-toggle="modal" data-target="#jsbluditMediaModal">Media Manager</button>

		<?php
			echo Bootstrap::formTitle(array('title'=>'Cover image'));
		?>

		<img class="img-thumbnail" alt="200x200" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1627e1b2b7e%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1627e1b2b7e%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.65%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true" style="width: 100px; height: 100px;">

		<?php
			echo Bootstrap::formTitle(array('title'=>'External Cover image'));
		?>

		<?php
			echo Bootstrap::formInputTextBlock(array(
				'name'=>'externalCoverImage',
				'placeholder'=>'https://',
				'tip'=>'You can set a cover image from external URL, such as a CDN or some server dedicate for images.'
			));
		?>

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
				'name'=>'parentTMP',
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

<script>
$(document).ready(function() {

	// Button Save as draft
	$("#jssaveAsDraft").on("click", function() {
		$("#jsstatus").val("draft");
		$("#dynamicTabContent").submit();
	});

	// Type selector modiefied the status
	$("#jstype").on("change", function() {
		var status = $("#jstype option:selected").val();
		$("#jsstatus").val(status);
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
	var parentsXHR;
	var parentsList; // Keep the parent list returned to get the key by the title page
	$("#jsparentTMP").autoComplete({
		source: function(term, response) {
			// Prevent call inmediatly another ajax request
			try { parentsXHR.abort(); } catch(e){}
			parentsXHR = $.getJSON("<?php echo HTML_PATH_ADMIN_ROOT ?>ajax/get-parents", {query: term},
				function(data) {
					parentsList = data;
					term = term.toLowerCase();
					var matches = [];
					for (var title in data) {
						if (~title.toLowerCase().indexOf(term))
							matches.push(title);
					}
					response(matches);
			});
		},
		onSelect: function(e, term, item) {
			var parentKey = parentsList[term];
			$("#jsparent").attr("value", parentKey);
		}
	});

});
</script>

<?php
	// Include Bludit Media Manager
	include(PATH_ADMIN_THEMES.'booty/html/media.php');
?>