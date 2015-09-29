<h2 class="title"><i class="fa fa-pencil"></i><?php $Language->p('New post') ?></h2>

<form method="post" action="" class="forms">

	<input type="hidden" id="jstoken" name="token" value="<?php $Security->printToken() ?>">

	<label>
		<?php $Language->p('Title') ?>
		<input id="jstitle" name="title" type="text" class="width-90">
	</label>

	<label class="width-90">
		<?php $Language->p('Content') ?> <span class="forms-desc"><?php $Language->p('HTML and Markdown code supported') ?></span>
		<textarea id="jscontent" name="content" rows="15" ></textarea>
	</label>

	<button id="jsadvancedButton" class="btn btn-smaller"><?php $Language->p('Advanced options') ?></button>

	<div id="jsadvancedOptions">

    	<label>
	    	<?php $Language->p('Date') ?>
		<input name="date" id="jsdate" type="text">
		<span class="forms-desc"><?php $Language->p('You can schedule the post just select the date and time') ?></span>
	</label>

	<label>
		<?php $Language->p('Friendly Url') ?>
		<div class="input-groups width-50">
		<span class="input-prepend"><?php echo $Site->urlPost() ?><span id="jsparentExample"></span></span>
		<input id="jsslug" name="slug" type="text">
		</div>
		<span class="forms-desc"><?php $Language->p('you-can-modify-the-url-which-identifies') ?></span>
	</label>

	<label>
		<?php $Language->p('Description') ?>
		<input id="jsdescription" name="description" type="text" class="width-50">
		<span class="forms-desc"><?php $Language->p('this-field-can-help-describe-the-content') ?></span>
	</label>

	<label>
		<?php $Language->p('Tags') ?>
		<input id="jstags" name="tags" type="text" class="width-50">
		<span class="forms-desc"><?php $Language->p('write-the-tags-separeted-by-comma') ?></span>
	</label>

	</div>

	<button class="btn btn-blue" name="publish"><?php $Language->p('Publish') ?></button>
	<button class="btn" name="draft"><?php $Language->p('Draft') ?></button>

</form>

<script>

$(document).ready(function()
{
	$("#jsdate").datetimepicker({format:"<?php echo DB_DATE_FORMAT ?>"});

	$("#jstitle").keyup(function() {
		var slug = $(this).val();

		checkSlugPost(slug, "", $("#jsslug"));
	});

	$("#jsslug").keyup(function() {
		var slug = $("#jsslug").val();

		checkSlugPost(slug, "", $("#jsslug"));
	});

	$("#jsadvancedButton").click(function() {
		$("#jsadvancedOptions").slideToggle();
		return false;
	});

});

</script>