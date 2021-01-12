<div aria-live="polite" aria-atomic="true" class="position-relative">
	<div class="toast-container position-absolute top-0 end-0 p-3" style="z-index:1050;">
		<div id="alert" class="toast d-flex align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="toast-body">Hello, I'm a Bludit alert!</div>
			<button type="button" class="btn-close btn-close-white ms-auto me-2" data-bs-dismiss="toast" aria-label="Close"></button>
		</div>
	</div>
</div>

<script>
function showAlert(text, background='primary') {
	$('#alert').removeClass('bg-danger bg-warning bg-primary').addClass('bg-'+background);
	$('#alert').children('.toast-body').html(text);
	$('#alert').toast('show');
}

function showAlertError(text) {
	showAlert(text, 'danger');
}

function showAlertWarning(text) {
	showAlert(text, 'warning');
}

function showAlerInfo(text) {
	showAlert(text, 'primary');
}

function hideAlert(text) {
	$('#alert').toast('hide');
}
</script>