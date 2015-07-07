<h2 class="title"><i class="fa fa-pencil"></i> <?php $Language->p('Edit page') ?></h2>

<form id="jsform" method="post" action="" class="forms">

    <input type="hidden" id="jskey" name="key" value="<?php echo $_Page->key() ?>">

    <label>
        <?php $Language->p('Title') ?>
        <input id="jstitle" name="title" type="text" class="width-70" value="<?php echo $_Page->title() ?>">
    </label>

    <label>
        <?php $Language->p('Content') ?> <span class="forms-desc"><?php $Language->p('HTML and Markdown code supported') ?></span>
        <textarea name="content" rows="10" class="width-70"><?php echo $_Page->contentRaw(false) ?></textarea>
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

<?php
    // Remove setting pages parents if the page is a parent.
    if(count($_Page->children())===0)
    {
?>

    <label for="jsparent">
        <?php $Language->p('Parent') ?>
        <select id="jsparent" name="parent" class="width-50">
        <?php
            $htmlOptions[NO_PARENT_CHAR] = '('.$Language->g('No parent').')';
            $htmlOptions += $dbPages->parentKeyList();
            unset($htmlOptions[$_Page->key()]);
            foreach($htmlOptions as $value=>$text) {
                echo '<option value="'.$value.'"'.( ($_Page->parentKey()===$value)?' selected="selected"':'').'>'.$text.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc">Tip/Help ???</div>
    </label>

<?php } ?>

    <label>
        <?php $Language->p('Friendly URL') ?>
        <div class="input-groups width-50">
            <span class="input-prepend"><?php echo $Site->url() ?><span id="jsparentExample"><?php echo $_Page->parentKey()?$_Page->parentKey().'/':''; ?></span></span>
            <input id="jsslug" type="text" name="slug" value="<?php echo $_Page->slug() ?>">
        </div>
        <span class="forms-desc">You can modify the URL which identifies a page or post using human-readable keywords. No more than 150 characters.</span>
    </label>

    <label>
        <?php $Language->p('Description') ?>
        <input id="jsdescription" type="text" name="description" class="width-50" value="<?php echo $_Page->description() ?>">
        <span class="forms-desc">This field can help describe the content in a few words. No more than 150 characters.</span>
    </label>

    <label>
        <?php $Language->p('Tags') ?>
        <input id="jstags" name="tags" type="text" class="width-50" value="<?php echo $_Page->tags() ?>">
        <span class="forms-desc">Write the tags separeted by comma. eg: tag1, tag2, tag3</span>
    </label>

    <label>
        <?php $Language->p('Position') ?>
        <input id="jsposition" name="position" type="text" class="width-20" value="<?php echo $_Page->position() ?>">
    </label>

    </div>

    <button class="btn btn-blue" name="publish"><i class="fa fa-sun-o fa-right"></i><?php echo ($_Page->published()?$Language->p('Save'):$Language->p('Publish now')) ?></button>

<?php if(count($_Page->children())===0) { ?>
    <button class="btn" name="draft"><i class="fa fa-circle-o fa-right"></i><?php $Language->p('Draft') ?></button>
    <button id="jsdelete" class="btn" name="delete"><i class="fa fa-remove fa-right"></i><?php $Language->p('Delete') ?></button>
<?php } ?>

</form>

<script>

$(document).ready(function()
{
    var key = $("#jskey").val();

    $("#jsslug").keyup(function() {
        var text = $(this).val();
        var parent = $("#jsparent").val();

        checkSlugPage(text, parent, key, $("#jsslug"));
    });

    $("#jstitle").keyup(function() {
        var text = $(this).val();
        var parent = $("#jsparent").val();

        checkSlugPage(text, parent, key, $("#jsslug"));
    });

    $("#jsparent").change(function() {
        var parent = $(this).val();
        var text = $("#jsslug").val();

        if(parent==NO_PARENT_CHAR) {
            $("#jsparentExample").text("");
        }
        else {
            $("#jsparentExample").text(parent+"/");
        }

        checkSlugPage(text, parent, key, $("#jsslug"));
    });

    $("#jsdelete").click(function() {
        if(!confirm("<?php $Language->p('confirm-delete-this-action-cannot-be-undone') ?>")) {
            event.preventDefault();
        }
    });

});

</script>