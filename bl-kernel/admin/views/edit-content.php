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
	<?php
		echo Bootstrap::formOpen(array(
			'id'=>'jsform',
			'class'=>'tab-content mt-4'
		));

		// Token CSRF
		echo Bootstrap::formInputHidden(array(
			'name'=>'tokenCSRF',
			'value'=>$Security->getTokenCSRF()
		));

		// Parent
		echo Bootstrap::formInputHidden(array(
			'name'=>'parent',
			'value'=>$page->parent()
		));

		// UUID
		echo Bootstrap::formInputHidden(array(
			'name'=>'uuid',
			'value'=>$dbPages->generateUUID()
		));

		// Status = published, draft, sticky, static
		echo Bootstrap::formInputHidden(array(
			'name'=>'status',
			'value'=>$page->status()
		));

		// Page current key
		echo Bootstrap::formInputHidden(array(
			'name'=>'key',
			'value'=>$page->key()
		));
	?>

	<!-- TABS CONTENT -->
	<div class="tab-pane show active" id="content" role="tabpanel" aria-labelledby="content-tab">

		<?php
			// Title
			echo Bootstrap::formInputTextBlock(array(
				'name'=>'title',
				'placeholder'=>'Enter title',
				'class'=>'form-control-lg',
				'value'=>$page->title()
			));
		?>

		<div class="form-group mt-2">
			<div id="jscontent" name="content"><?php echo $page->contentRaw(true) ?></div>
		</div>

		<div class="form-group mt-2">
			<button type="submit" class="btn btn-primary">Save</button>
			<button type="button" class="btn" id="jssaveAsDraft">Save as draft</button>
			<a href="<?php echo HTML_PATH_ADMIN_ROOT ?>dashboard" class="btn"><?php echo $L->g('Cancel') ?></a>
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

			$coverImage = $page->coverImage(false);
			$externalCoverImage = '';
			if (filter_var($coverImage, FILTER_VALIDATE_URL)) {
				$coverImage = '';
				$externalCoverImage = $page->coverImage(false);
			}
		?>

		<img class="img-thumbnail" alt="200x200" src="<?php echo $coverImage ?>" data-holder-rendered="true" style="width: 100px; height: 100px;">

		<?php
			echo Bootstrap::formTitle(array('title'=>'External Cover image'));
		?>

		<?php
			echo Bootstrap::formInputTextBlock(array(
				'name'=>'externalCoverImage',
				'placeholder'=>'https://',
				'value'=>$externalCoverImage,
				'tip'=>'Set a cover image from external URL, such as a CDN or some server dedicate for images.'
			));
		?>

	</div>

	<!-- TABS OPTIONS -->
	<div class="tab-pane" id="options" role="tabpanel" aria-labelledby="options-tab">
		<?php
			echo Bootstrap::formTitle(array('title'=>'General'));

			// Category
			echo Bootstrap::formSelect(array(
				'name'=>'category',
				'label'=>'Category',
				'selected'=>$page->categoryKey(),
				'options'=>$dbCategories->getKeyNameArray()
			));

			// Tags
			echo Bootstrap::formInputText(array(
				'name'=>'tags',
				'label'=>'Tags',
				'value'=>$page->tags(),
				'placeholder'=>'Tags separeted by comma'
			));

			// Description
			echo Bootstrap::formTextarea(array(
				'name'=>'description',
				'label'=>'Description',
				'placeholder'=>'Small description about the content',
				'rows'=>'4',
				'value'=>$page->description()
			));

			echo Bootstrap::formTitle(array('title'=>'Advanced'));

			// Date
			echo Bootstrap::formInputText(array(
				'name'=>'date',
				'label'=>'Date',
				'placeholder'=>'YYYY-MM-DD hh:mm:ss',
				'value'=>$page->dateRaw()
			));

			// Type
			echo Bootstrap::formSelect(array(
				'name'=>'type',
				'label'=>'Type',
				'selected'=>$page->status(),
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
				'placeholder'=>'Start writing the title of the page parent',
				'value'=>($page->parent()?$page->parentMethod('title'):'')
			));

			// Position
			echo Bootstrap::formInputText(array(
				'name'=>'position',
				'label'=>'Position',
				'placeholder'=>'',
				'value'=>$page->position()
			));

			// Friendly URL
			echo Bootstrap::formInputText(array(
				'name'=>'slug',
				'label'=>'Friendly URL',
				'value'=>$page->slug(),
				'placeholder'=>'Leave empty for automaticly complete'
			));

			// Template
			echo Bootstrap::formInputText(array(
				'name'=>'template',
				'label'=>'Template',
				'placeholder'=>'',
				'value'=>$page->template()
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

	// Autosave
	setInterval(
		function() {
			var uuid = $("#jsuuid").val();
			var title = $("#jstitle").val();
			var content = $("#jscontent").val();
			var ajax = new bluditAjax()
			ajax.autosave(uuid, title, content);
		},
		10*1000
	);

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
