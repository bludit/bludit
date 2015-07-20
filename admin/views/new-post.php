<h2 class="title"><i class="fa fa-pencil"></i> <?php $Language->p('New post') ?></h2>

<form method="post" action="" class="forms">

    <label>
        <?php $Language->p('Title') ?>
        <input id="jstitle" name="title" type="text" class="width-70">
    </label>

    <label>
        <?php $Language->p('Content') ?> <span class="forms-desc"><?php $Language->p('HTML and Markdown code supported') ?></span>
        <textarea id="jscontent" name="content" rows="10" class="width-70"></textarea>
    </label>

<?php
    if($Site->advancedOptions()) {
        echo '<div id="jsadvancedOptions">';
    }
    else
    {
        echo '<p class="advOptions">'.$Language->g('Enable more features at').' <a href="'.HTML_PATH_ADMIN_ROOT.'settings#advanced">'.$Language->g('settings-advanced-writting-settings').'</a></p>';
        echo '<div id="jsadvancedOptions" style="display:none">';
    }
?>

    <h4><?php $Language->p('Advanced options') ?></h4>

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

    <button class="btn btn-blue" name="publish"><?php $Language->p('Publish now') ?></button>
    <button class="btn" name="draft"><?php $Language->p('Draft') ?></button>

</form>

<script>

$(document).ready(function()
{

    $("#jstitle").keyup(function() {
        var slug = $(this).val();

        checkSlugPost(slug, "", $("#jsslug"));
    });

    $("#jsslug").keyup(function() {
        var slug = $("#jsslug").val();

        checkSlugPost(slug, "", $("#jsslug"));
    });

});

</script>