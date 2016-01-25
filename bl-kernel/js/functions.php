<?php defined('BLUDIT') or die('Bludit CMS.');

echo '<script>'.PHP_EOL;

echo 'var HTML_PATH_ROOT = "'.HTML_PATH_ROOT.'";'.PHP_EOL;
echo 'var HTML_PATH_ADMIN_ROOT = "'.HTML_PATH_ADMIN_ROOT.'";'.PHP_EOL;
echo 'var HTML_PATH_ADMIN_THEME = "'.HTML_PATH_ADMIN_THEME.'";'.PHP_EOL;
echo 'var HTML_PATH_UPLOADS = "'.HTML_PATH_UPLOADS.'";'.PHP_EOL;
echo 'var HTML_PATH_UPLOADS_THUMBNAILS = "'.HTML_PATH_UPLOADS_THUMBNAILS.'";'.PHP_EOL;
echo 'var NO_PARENT_CHAR = "'.NO_PARENT_CHAR.'";'.PHP_EOL;

echo '</script>';

?>

<script>

var ajaxRequest;

function checkSlugPage(text, parent, oldKey, writeResponse)
{
    parent = typeof parent !== 'undefined' ? parent : NO_PARENT_CHAR;
    oldKey = typeof oldKey !== 'undefined' ? oldKey : "";

    checkSlug("page", text, parent, oldKey, writeResponse);
}

function checkSlugPost(text, oldKey, writeResponse)
{
    checkSlug("post", text, null, oldKey, writeResponse);
}

function checkSlug(type, text, parentPage, key, writeResponse)
{
    if(ajaxRequest) {
        ajaxRequest.abort();
    }

    if(type=="page")
    {
        ajaxRequest = $.ajax({
            type: "POST",
            data:{ type: "page", text: text, parent: parentPage, key: key },
            url: "<?php echo HTML_PATH_ADMIN_ROOT.'ajax/slug' ?>"
        });
    }
    else
    {
        ajaxRequest = $.ajax({
            type: "POST",
            data:{ type: "post", text: text, key: key },
            url: "<?php echo HTML_PATH_ADMIN_ROOT.'ajax/slug' ?>"
        });
    }

    // Callback handler that will be called on success
    ajaxRequest.done(function (response, textStatus, jqXHR){
        writeResponse.val(response["slug"]);
        console.log("DEBUG: AJAX Done function");
    });

    // Callback handler that will be called on failure
    ajaxRequest.fail(function (jqXHR, textStatus, errorThrown){
        console.log("DEBUG: AJAX error function");
    });

    // Callback handler that will be called regardless
    // if the request failed or succeeded
    ajaxRequest.always(function () {
        console.log("DEBUG: AJAX always function");
    });
}

</script>
