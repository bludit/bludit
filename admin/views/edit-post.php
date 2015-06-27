<h2 class="title"><i class="fa fa-pencil"></i> Edit post</h2>

<form method="post" action="" class="forms">

    <input type="hidden" id="jsKey" name="key" value="<?php echo $_Post->key() ?>">

    <label>
        Title
        <input id="jsTitle" name="title" type="text" class="width-70" value="<?php echo $_Post->title() ?>">
    </label>

    <label>
        Content <span class="forms-desc">HTML and Markdown code supported.</span>
        <textarea name="content" rows="10" class="width-70"><?php echo $_Post->contentRaw() ?></textarea>
    </label>

<?php
    if($Site->advancedOptions()) {
        echo '<div id="jsAdvancedOptions">';
    }
    else
    {
        echo '<p class="advOptions">Enable more features at <a href="'.HTML_PATH_ADMIN_ROOT.'settings#advanced">Settings->Advanced->Writting Settings</a></p>';
        echo '<div id="jsAdvancedOptions" style="display:none">';
    }
?>

    <h4>Advanced options</h4>

    <label>
        Friendly url
        <div class="input-groups width-50">
            <span class="input-prepend"><?php echo $Site->urlPost() ?><span id="jsParentExample"></span></span>
            <input id="jsSlug" type="text" name="slug" value="<?php echo $_Post->slug() ?>">
        </div>
        <span class="forms-desc">Short text no more than 150 characters. Special characters not allowed.</span>
    </label>

    <label>
        Description
        <input id="jsDescription" type="text" name="description" class="width-50" value="<?php echo $_Post->description() ?>">
        <span class="forms-desc">This field is for Twitter/Facebook/Google+ descriptions. No more than 150 characters.</span>
    </label>

    <label>
        Tags
        <input id="jsTags" name="tags" type="text" class="width-50" value="<?php echo $_Post->tags() ?>">
        <span class="forms-desc">Write the tags separeted by comma. eg: tag1, tag2, tag3</span>
    </label>
    </div>

    <button class="btn btn-blue" name="publish"><i class="fa fa-sun-o fa-right"></i>Save</button>
    <button class="btn" name="draft"><i class="fa fa-circle-o fa-right"></i>Draft</button>
    <button class="btn" name="delete"><i class="fa fa-remove fa-right"></i>Delete</button>

</form>

<script>

$(document).ready(function()
{
    var key = $("#jsKey").val();

    $("#jsTitle").keyup(function() {
        var slug = $(this).val();

        checkSlugPost(slug, key, $("#jsSlug"));
    });

    $("#jsSlug").keyup(function() {
        var slug = $("#jsSlug").val();

        checkSlugPost(slug, key, $("#jsSlug"));
    });

});

</script>
