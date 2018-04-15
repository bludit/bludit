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

function setCookie(name, value, days) {
	var expires = "";
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days*24*60*60*1000));
		expires = "; expires=" + date.toUTCString();
	}
	document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return false;
}

function deleteCookie(name) {
	document.cookie = name+'=; Max-Age=-999;';
}

</script>