<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<!-- TABS -->
<ul class="nav nav-tabs" id="dynamicTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="content-tab" data-toggle="tab" href="#content" role="tab" aria-controls="content" aria-selected="true">Editor</a>
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
			'value'=>$security->getTokenCSRF()
		));

		// Parent
		echo Bootstrap::formInputHidden(array(
			'name'=>'parent',
			'value'=>''
		));

		// UUID
		echo Bootstrap::formInputHidden(array(
			'name'=>'uuid',
			'value'=>$dbPages->generateUUID()
		));

		// Status = published, draft, sticky, static
		echo Bootstrap::formInputHidden(array(
			'name'=>'type',
			'value'=>'published'
		));

		// Cover image
		echo Bootstrap::formInputHidden(array(
			'name'=>'coverImage',
			'value'=>''
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
			<input value="" class="form-control form-control-lg rounded-0 " id="jstitle" name="title" placeholder="Enter title" type="text">
		</div>

		<div class="form-group m-0 mt-1">
			<button id="jsmediaManagerButton" type="button" class="btn btn-form btn-sm" data-toggle="modal" data-target="#jsbluditMediaModal"><span class="oi oi-image"></span> Media Manager</button>
			<button id="jscategoryButton" type="button" class="btn btn-form btn-sm" data-toggle="modal" data-target="#jscategoryModal"><span class="oi oi-tag"></span> Category: <span class="option">-</span></button>
			<button id="jsdescriptionButton" type="button" class="btn btn-form btn-sm" data-toggle="modal" data-target="#jsdescriptionModal"><span class="oi oi-tag"></span> Description: <span class="option">-</span></button>
		</div>

		<div class="form-group mt-1">
			<textarea id="jseditor" style="display:none;"></textarea>
		</div>

		<div class="form-group mt-2">
			<button type="button" class="jsbuttonSave btn btn-primary"><?php echo $L->g('Publish') ?></button>
			<button type="button" class="jsbuttonDraft btn btn-secondary"><?php echo $L->g('Save as draft') ?></button>
			<a href="<?php echo HTML_PATH_ADMIN_ROOT ?>dashboard" class="btn btn-secondary"><?php echo $L->g('Cancel') ?></a>
		</div>

	</div>

	<!-- TABS IMAGES -->
	<div class="tab-pane" id="images" role="tabpanel" aria-labelledby="images-tab">

		<div>
			<div class="float-right">
				<button type="button" class="jsbuttonSave btn btn-primary btn-sm"><?php echo $L->g('Publish') ?></button>
				<button type="button" class="jsbuttonDraft btn btn-secondary btn-sm"><?php echo $L->g('Save as draft') ?></button>
			</div>
			<h4 class="mt-4 mb-4 font-weight-normal">Cover Image</h4>
		</div>

		<img id="jscoverImagePreview" style="width: 350px; height: 200px;" class="img-thumbnail" alt="coverImagePreview" src="<?php echo HTML_PATH_ADMIN_THEME_IMG ?>default.svg" />

		<?php
			echo Bootstrap::formTitle(array('title'=>$L->g('External Cover Image')));

			echo Bootstrap::formInputTextBlock(array(
				'name'=>'externalCoverImage',
				'placeholder'=>"https://",
				'value'=>'',
				'tip'=>'Set a cover image from external URL, such as a CDN or some server dedicated for images.'
			));
		?>

	</div>

	<!-- TABS OPTIONS -->
	<div class="tab-pane" id="options" role="tabpanel" aria-labelledby="options-tab">

		<div>
			<div class="float-right">
				<button type="button" class="jsbuttonSave btn btn-primary btn-sm"><?php echo $L->g('Publish') ?></button>
				<button type="button" class="jsbuttonDraft btn btn-secondary btn-sm"><?php echo $L->g('Save as draft') ?></button>
			</div>
			<h4 class="mt-4 mb-4 font-weight-normal">Advanced</h4>
		</div>

		<?php
			// Date
			echo Bootstrap::formInputText(array(
				'name'=>'date',
				'label'=>'Date',
				'placeholder'=>'',
				'value'=>Date::current(DB_DATE_FORMAT),
				'tip'=>'Date format: <code>YYYY-MM-DD Hours:Minutes:Seconds</code>'
			));

			// Type
			echo Bootstrap::formSelect(array(
				'name'=>'typeTMP',
				'label'=>'Type',
				'selected'=>'',
				'options'=>array(
					'published'=>'- Default -',
					'sticky'=>'Sticky',
					'static'=>'Static'
				),
				'tip'=>'???'
			));

			// Parent
			echo Bootstrap::formInputText(array(
				'name'=>'parentTMP',
				'label'=>$L->g('Parent'),
				'placeholder'=>'',
				'tip'=>'Start typing a page title to see a list of suggestions.',
				'value'=>''
			));

			// Position
			echo Bootstrap::formInputText(array(
				'name'=>'position',
				'label'=>$L->g('Position'),
				'tip'=>'Field used when ordering content by position',
				'value'=>$dbPages->nextPositionNumber()
			));

			// Template
			echo Bootstrap::formInputText(array(
				'name'=>'template',
				'label'=>'Template',
				'placeholder'=>'',
				'value'=>'',
				'tip'=>'Write a template name to filter the page in the theme and change the style of the page.'
			));

			echo Bootstrap::formTitle(array('title'=>'SEO'));

			// Tags
			echo Bootstrap::formInputText(array(
				'name'=>'tags',
				'label'=>'Tags',
				'placeholder'=>'',
				'tip'=>'Write the tags separated by comma'
			));

			// Friendly URL
			echo Bootstrap::formInputText(array(
				'name'=>'slug',
				'tip'=>$L->g('URL associated with the content'),
				'label'=>$L->g('Friendly URL'),
				'placeholder'=>'Leave empty for autocomplete by Bludit.'
			));

			echo Bootstrap::formCheckbox(array(
				'name'=>'noindex',
				'label'=>'Robots',
				'labelForCheckbox'=>'Apply <code>noindex</code> to this page',
				'placeholder'=>'',
				'class'=>'mt-4',
				'checked'=>false,
				'tip'=>'This tells search engines not to show this page in their search results.'
			));

			echo Bootstrap::formCheckbox(array(
				'name'=>'nofollow',
				'label'=>'',
				'labelForCheckbox'=>'Apply <code>nofollow</code> to this page',
				'placeholder'=>'',
				'checked'=>false,
				'tip'=>'This tells search engines not to follow links on this page.'
			));

			echo Bootstrap::formCheckbox(array(
				'name'=>'noarchive',
				'label'=>'',
				'labelForCheckbox'=>'Apply <code>noarchive</code> to this page',
				'placeholder'=>'',
				'checked'=>false,
				'tip'=>'This tells search engines not to save a cached copy of this page.'
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
							'selected'=>'',
							'class'=>'',
							'emptyOption'=>'- Uncategorized -',
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
							'value'=>'',
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

	// Datepicker
	$("#jsdate").datetimepicker({format:DB_DATE_FORMAT});

	// Button Save
	$(".jsbuttonSave").on("click", function() {
		var type = $("#jstypeTMP option:selected").val();
		$("#jstype").val(type);
		$("#jscontent").val( editorGetContent() );
		$("#jsform").submit();
	});

	// Button Save as draft
	$(".jsbuttonDraft").on("click", function() {
		$("#jstype").val("draft");
		$("#jscontent").val( editorGetContent() );
		$("#jsform").submit();
	});

	// External cover image
	$("#jsexternalCoverImage").change(function() {
		$("#jscoverImage").val( $(this).val() );
	});

	// Generate slug when the user type the title
	$("#jstitle").keyup(function() {
		var text = $(this).val();
		var parent = $("#jsparent").val();
		var currentKey = "";
		var ajax = new bluditAjax();
		ajax.generateSlug(text, parent, currentKey, $("#jsslug"));
	});

	// Autosave interval
	// Autosave works when the content of the page is bigger than 100 characters
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
		minChars: 1,
		source: function(term, response) {
			// Prevent call inmediatly another ajax request
			try { parentsXHR.abort(); } catch(e){}
			parentsXHR = $.getJSON(HTML_PATH_ADMIN_ROOT+"ajax/get-parents", {query: term},
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
			// parentsList = array( pageTitle => pageKey )
			var parentKey = parentsList[term];
			$("#jsparent").attr("value", parentKey);
		}
	});

});
</script>
