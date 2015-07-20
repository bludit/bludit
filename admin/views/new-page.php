<h2 class="title"><i class="fa fa-pencil"></i> <?php $Language->p('New page') ?></h2>

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

    <label for="jsparent">
        <?php $Language->p('Parent') ?>
        <select id="jsparent" name="parent" class="width-50">
        <?php
            $htmlOptions[NO_PARENT_CHAR] = '('.$Language->g('No parent').')';
            $htmlOptions += $dbPages->parentKeyList();
            foreach($htmlOptions as $value=>$text) {
                echo '<option value="'.$value.'">'.$text.'</option>';
            }
        ?>
        </select>
    </label>

    <label>
        <?php $Language->p('Friendly Url') ?>
        <div class="input-groups width-50">
            <span class="input-prepend"><?php echo $Site->urlPage() ?><span id="jsparentExample"></span></span>
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

    <label>
        <?php $Language->p('Position') ?>
        <input id="jsposition" name="position" type="text" class="width-20" value="0">
    </label>

    </div>

    <button class="btn btn-blue" name="publish"><?php $Language->p('Publish now') ?></button>
    <button class="btn" name="draft"><?php $Language->p('Draft') ?></button>

</form>

<script>

$(document).ready(function()
{

    $("#jsslug").keyup(function() {
        var text = $(this).val();
        var parent = $("#jsparent").val();

        checkSlugPage(text, parent, "", $("#jsslug"));
    });

    $("#jstitle").keyup(function() {
        var text = $(this).val();
        var parent = $("#jsparent").val();

        checkSlugPage(text, parent, "", $("#jsslug"));
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

        checkSlugPage(text, parent, "", $("#jsslug"));
    });

});

</script>