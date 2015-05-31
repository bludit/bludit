<h2 class="title"><i class="fa fa-pencil"></i> New page</h2>

<form method="post" action="" class="forms">

    <label>
        Title
        <input id="title" name="title" type="text" class="width-70">
    </label>

    <label>
        Content <span class="forms-desc">HTML and Markdown code supported.</span>
        <textarea name="content" rows="10" class="width-70"></textarea>
    </label>

<?php
    if($Site->advancedOptions()) {
        echo '<div id="advancedOptions">';
    }
    else
    {
        echo '<p class="advOptions">You can enable more features at <a href="'.HTML_PATH_ADMIN_ROOT.'settings#advanced">Settings->Advanced->Writting Settings</a></p>';
        echo '<div id="advancedOptions" style="display:none">';
    }
?>

    <h4>Advanced options</h4>

    <label for="parent">
        Page parent
        <select id="parent" name="parent" class="width-50">
        <?php
            $htmlOptions[NO_PARENT_CHAR] = '(No parent)';
            $htmlOptions += $dbPages->parentKeyList();
            foreach($htmlOptions as $value=>$text) {
                echo '<option value="'.$value.'">'.$text.'</option>';
            }
        ?>
        </select>
        <div class="forms-desc">Tip/Help ???</div>
    </label>

    <label>
        Friendly url
        <div class="input-groups width-50">
            <span class="input-prepend"><?php echo $Site->urlPage() ?><span id="parentExample"></span></span>
            <input id="slug" name="slug" type="text">
        </div>
        <span class="forms-desc">Short text no more than 150 characters. Special characters not allowed.</span>
    </label>

    <label>
        Description
        <input id="description" name="description" type="text" class="width-50">
        <span class="forms-desc">This field is for Twitter/Facebook/Google+ descriptions. No more than 150 characters.</span>
    </label>

    <label>
        Tags
        <input id="tags" name="tags" type="text" class="width-50">
        <span class="forms-desc">Write the tags separeted by comma. eg: tag1, tag2, tag3</span>
    </label>

    <label>
        Position
        <input id="position" name="position" type="text" class="width-20" value="0">
    </label>

    </div>

    <button class="btn btn-blue" name="publish"><i class="fa fa-sun-o fa-right"></i>Publish now</button>
    <button class="btn" name="draft"><i class="fa fa-circle-o fa-right"></i>Draft</button>

</form>

<script>

$(document).ready(function()
{

    $("#slug").keyup(function() {
        var text = $(this).val();
        var parent = $("#parent").val();

        checkSlugPage(text, parent, "", $("#slug"));
    });

    $("#title").keyup(function() {
        var text = $(this).val();
        var parent = $("#parent").val();

        checkSlugPage(text, parent, "", $("#slug"));
    });

    $("#parent").change(function() {
        var parent = $(this).val();
        var text = $("#slug").val();

        if(parent==NO_PARENT_CHAR) {
            $("#parentExample").text("");
        }
        else {
            $("#parentExample").text(parent+"/");
        }

        checkSlugPage(text, parent, "", $("#slug"));
    });

});

</script>
