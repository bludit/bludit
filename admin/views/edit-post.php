<h2 class="title"><i class="fa fa-pencil"></i> <?php $Language->p('Edit post') ?></h2>

<form method="post" action="" class="forms">

    <input type="hidden" id="jskey" name="key" value="<?php echo $_Post->key() ?>">

    <label>
        <?php $Language->p('Title') ?>
        <input id="jstitle" name="title" type="text" class="width-70" value="<?php echo $_Post->title() ?>">
    </label>

    <label>
        <?php $Language->p('Content') ?> <span class="forms-desc"><?php $Language->p('HTML and Markdown code supported') ?></span>
        <textarea id="jscontent" name="content" rows="10" class="width-70"><?php echo $_Post->contentRaw(true, false) ?></textarea>
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
            <input id="jsslug" type="text" name="slug" value="<?php echo $_Post->slug() ?>">
        </div>
        <span class="forms-desc">Short text no more than 150 characters. Special characters not allowed.</span>
    </label>

    <label>
        <?php $Language->p('Description') ?>
        <input id="jsdescription" type="text" name="description" class="width-50" value="<?php echo $_Post->description() ?>">
        <span class="forms-desc">This field is for Twitter/Facebook/Google+ descriptions. No more than 150 characters.</span>
    </label>

    <label>
        <?php $Language->p('Tags') ?>
        <input id="jstags" name="tags" type="text" class="width-50" value="<?php echo $_Post->tags() ?>">
        <span class="forms-desc">Write the tags separeted by comma. eg: tag1, tag2, tag3</span>
    </label>
    </div>

    <button class="btn btn-blue" name="publish"><i class="fa fa-sun-o fa-right"></i><?php echo ($_Post->published()?$Language->p('Save'):$Language->p('Publish now')) ?></button>
    <button class="btn" name="draft"><i class="fa fa-circle-o fa-right"></i><?php $Language->p('Draft') ?></button>
    <button id="jsdelete" class="btn" name="delete"><i class="fa fa-remove fa-right"></i><?php $Language->p('Delete') ?></button>

</form>

<script>

$(document).ready(function()
{
    var key = $("#jskey").val();

    $("#jstitle").keyup(function() {
        var slug = $(this).val();

        checkSlugPost(slug, key, $("#jsslug"));
    });

    $("#jsslug").keyup(function() {
        var slug = $("#jsslug").val();

        checkSlugPost(slug, key, $("#jsslug"));
    });

    $("#jsdelete").click(function() {
        if(!confirm("<?php $Language->p('confirm-delete-this-action-cannot-be-undone') ?>")) {
            event.preventDefault();
        }
    });

});

</script>