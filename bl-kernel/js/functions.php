<?php defined('BLUDIT') or die('Bludit CMS.');

echo '<script>'.PHP_EOL;

echo 'var HTML_PATH_ROOT = "'.HTML_PATH_ROOT.'";'.PHP_EOL;
echo 'var HTML_PATH_ADMIN_ROOT = "'.HTML_PATH_ADMIN_ROOT.'";'.PHP_EOL;
echo 'var HTML_PATH_ADMIN_THEME = "'.HTML_PATH_ADMIN_THEME.'";'.PHP_EOL;
echo 'var HTML_PATH_UPLOADS = "'.HTML_PATH_UPLOADS.'";'.PHP_EOL;
echo 'var HTML_PATH_UPLOADS_THUMBNAILS = "'.HTML_PATH_UPLOADS_THUMBNAILS.'";'.PHP_EOL;
echo 'var PARENT = "'.PARENT.'";'.PHP_EOL;

echo 'var tokenCSRF = "'.$Security->getTokenCSRF().'";'.PHP_EOL;

echo '</script>';

?>

<script>

var ajaxRequest;

function generateSlug(text, parentKey, currentKey, writeResponse) {
    if(ajaxRequest) {
        ajaxRequest.abort();
    }

    ajaxRequest = $.ajax({
        type: "POST",
        data: {
            tokenCSRF: tokenCSRF,
            text: text,
            parentKey: parentKey,
            currentKey: currentKey
        },
        url: "<?php echo HTML_PATH_ADMIN_ROOT.'ajax/slug' ?>"
    });

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

function sanitizeHTML(text) {
	var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	};

	return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

</script>