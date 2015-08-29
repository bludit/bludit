<h2 class="title"><i class="fa fa-pencil"></i> <?php $Language->p('Edit page') ?></h2>

<form id="jsform" method="post" action="" class="forms">

    <input type="hidden" id="jskey" name="key" value="<?php echo $_Page->key() ?>">

    <label>
        <?php $Language->p('Title') ?>
        <input id="jstitle" name="title" type="text" class="width-90" value="<?php echo $_Page->title() ?>">
    </label>

    <label class="width-90">
        <?php $Language->p('Content') ?> <span class="forms-desc"><?php $Language->p('HTML and Markdown code supported') ?></span>
        <textarea id="jscontent" name="content" rows="15"><?php echo $_Page->contentRaw(false) ?></textarea>
    </label>

    <button id="jsadvancedButton" class="btn btn-smaller"><?php $Language->p('Advanced options') ?></button>

    <div id="jsadvancedOptions">

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
    </label>

<?php } ?>

    <label>
        <?php $Language->p('Friendly URL') ?>
        <div class="input-groups width-50">
            <span class="input-prepend"><?php echo $Site->url() ?><span id="jsparentExample"><?php echo $_Page->parentKey()?$_Page->parentKey().'/':''; ?></span></span>
            <input id="jsslug" type="text" name="slug" value="<?php echo $_Page->slug() ?>">
        </div>
        <span class="forms-desc"><?php $Language->p('you-can-modify-the-url-which-identifies') ?></span>
    </label>

    <label>
        <?php $Language->p('Description') ?>
        <input id="jsdescription" type="text" name="description" class="width-50" value="<?php echo $_Page->description() ?>">
        <span class="forms-desc"><?php $Language->p('this-field-can-help-describe-the-content') ?></span>
    </label>

    <label>
        <?php $Language->p('Tags') ?>
        <input id="jstags" name="tags" type="text" class="width-50" value="<?php echo $_Page->tags() ?>">
        <span class="forms-desc"><?php $Language->p('write-the-tags-separeted-by-comma') ?></span>
    </label>

    <label>
        <?php $Language->p('Position') ?>
        <input id="jsposition" name="position" type="text" class="width-20" value="<?php echo $_Page->position() ?>">
    </label>

    </div>

    <button class="btn btn-blue" name="publish"><?php echo ($_Page->published()?$Language->p('Save'):$Language->p('Publish now')) ?></button>

<?php if(count($_Page->children())===0) { ?>
    <button class="btn" name="draft"><?php $Language->p('Draft') ?></button>
    <button id="jsdelete" class="btn" name="delete"><?php $Language->p('Delete') ?></button>
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
        if(confirm("<?php $Language->p('confirm-delete-this-action-cannot-be-undone') ?>")==false) {
            return false;
        }
    });

    $("#jsadvancedButton").click(function() {
        $("#jsadvancedOptions").slideToggle();
        return false;
    });

});

</script>