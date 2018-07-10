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
			'class'=>'tab-content mt-1'
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
			'value'=>$page->uuid()
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

		// Cover image
		echo Bootstrap::formInputHidden(array(
			'name'=>'coverImage',
			'value'=>$page->coverImage()
		));

		// Content
		echo Bootstrap::formInputHidden(array(
			'name'=>'content',
			'value'=>''
		));
	?>

	<!-- TABS CONTENT -->
	<div class="tab-pane show active" id="content" role="tabpanel" aria-labelledby="content-tab">

		<div class="form-group m-0">
			<input value="<?php echo $page->title() ?>" class="form-control form-control-lg rounded-0 " id="jstitle" name="title" placeholder="Enter title" type="text">
		</div>

		<div class="form-group m-0 mt-1">
			<button id="jsmediaManagerButton" type="button" class="btn btn-form btn-sm" data-toggle="modal" data-target="#jsbluditMediaModal"><span class="oi oi-image"></span> Media Manager</button>
			<button id="jscategoryButton" type="button" class="btn btn-form btn-sm" data-toggle="modal" data-target="#jscategoryModal"><span class="oi oi-tags"></span> Category: <span class="option">-</span></button>
			<button id="jsdescriptionButton" type="button" class="btn btn-form btn-sm" data-toggle="modal" data-target="#jsdescriptionModal"><span class="oi oi-tags"></span> Description: <span class="option">-</span></button>
		</div>

		<div class="form-group mt-1">
			<textarea id="jseditor" style="display:none;"><?php echo $page->contentRaw(false) ?></textarea>
		</div>

		<div class="form-group mt-2">
			<button id="jsbuttonSave" type="button" class="btn btn-primary"><?php echo $L->g('Save') ?></button>
			<button id="jsbuttonDraft" type="button" class="btn btn-secondary"><?php echo $L->g('Save as draft') ?></button>
			<a href="<?php echo HTML_PATH_ADMIN_ROOT ?>dashboard" class="btn btn-secondary"><?php echo $L->g('Cancel') ?></a>
			<?php
				if (count($page->children())===0) {
					echo '<button id="jsbuttonDelete" type="button" class="btn btn-secondary">'.$L->g('Delete').'</button>';
				}
			?>
		</div>

	</div>

	<!-- TABS IMAGES -->
	<div class="tab-pane" id="images" role="tabpanel" aria-labelledby="images-tab">

		<?php
			echo Bootstrap::formTitle(array('title'=>'Cover image'));

			$coverImage = $page->coverImage(false);
			$externalCoverImage = '';
			if (filter_var($coverImage, FILTER_VALIDATE_URL)) {
				$coverImage = '';
				$externalCoverImage = $page->coverImage(false);
			}
		?>

		<img id="jscoverImagePreview" style="width: 350px; height: 200px;" class="img-thumbnail" alt="coverImagePreview" src="<?php echo HTML_PATH_ADMIN_THEME_IMG ?>default.svg" />

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
			echo Bootstrap::formTitle(array('title'=>'Advanced'));

			// Tags
			echo Bootstrap::formInputText(array(
				'name'=>'tags',
				'label'=>'Tags',
				'value'=>$page->tags(),
				'placeholder'=>'Tags separeted by comma'
			));

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

	<!-- Modal for Categories -->
	<div id="jscategoryModal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Category</h5>
				</div>
				<div class="modal-body">
					<?php
						echo Bootstrap::formSelectBlock(array(
							'name'=>'category',
							'label'=>'',
							'selected'=>$page->categoryKey(),
							'class'=>'',
							'emptyOption'=>'- Without category -',
							'options'=>$dbCategories->getKeyNameArray()
						));
					?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			function setCategoryBox(value) {
				var selected = $("#jscategory option:selected");
				var value = selected.val().trim();
				if (value) {
					$("#jscategoryButton").find("span.option").html(selected.text());
				} else {
					$("#jscategoryButton").find("span.option").html("-");
				}
			}

			// Set the current category selected
			setCategoryBox();

			// When the user select the category update the category button
			$("#jscategory").on("change", function() {
				setCategoryBox();
			});
		});
	</script>

	<!-- Modal for Description -->
	<div id="jsdescriptionModal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Description</h5>
				</div>
				<div class="modal-body">
					<?php
						echo Bootstrap::formTextareaBlock(array(
							'name'=>'description',
							'label'=>'',
							'selected'=>'',
							'class'=>'',
							'value'=>$page->description(),
							'rows'=>3,
							'placeholder'=>$Language->get('this-field-can-help-describe-the-content')
						));
					?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			function setDescriptionBox(value) {
				var value = $("#jsdescription").val();
				if (!value) {
					value = '-';
				} else {
					value = jQuery.trim(value).substring(0, 60).split(" ").slice(0, -1).join(" ") + "...";
				}
				$("#jsdescriptionButton").find("span.option").html(value);
			}

			// Set the current description
			setDescriptionBox();

			// When the user write the description update the description button
			$("#jsdescription").on("change", function() {
				setDescriptionBox();
			});
		});
	</script>
</form>

<!-- Modal for Media Manager -->
<?php include(PATH_ADMIN_THEMES.'booty/html/media.php'); ?>

<script>
$(document).ready(function() {

	// Button Save
	$("#jsbuttonSave").on("click", function() {
		$("#jsstatus").val("published");
		$("#jscontent").val( editorGetContent() );
		$("#jsform").submit();
	});

	// Button Save as draft
	$("#jsbuttonDraft").on("click", function() {
		$("#jsstatus").val("draft");
		$("#jscontent").val( editorGetContent() );
		$("#jsform").submit();
	});

	// Button Delete
	$("#jsbuttonDelete").on("click", function() {
		$("#jsstatus").val("delete");
		$("#jscontent").val("");
		$("#jsform").submit();
	});

	// External cover image
	$("#jsexternalCoverImage").change(function() {
		$("#jscoverImage").val( $(this).val() );
	});

	// Type selector modified the status hidden field
	$("#jstype").on("change", function() {
		var status = $("#jstype option:selected").val();
		$("#jsstatus").val(status);
	});

	// Autosave interval
	setInterval(function() {
			var uuid = $("#jsuuid").val();
			var title = $("#jstitle").val();
			var content = editorGetContent();
			var ajax = new bluditAjax();
			// showAlert is the function to display an alert defined in alert.php
			ajax.autosave(uuid, title, content, showAlert);
	},1000*60*AUTOSAVE_INTERVAL);

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
