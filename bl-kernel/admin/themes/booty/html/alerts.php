<div aria-live="polite" aria-atomic="true" class="position-relative">
	<div class="toast-container position-absolute top-0 end-0 p-3">
		<div id="alert" class="toast d-flex align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="toast-body">Hello, I'm a Bludit alert!</div>
			<button type="button" class="btn-close btn-close-white ms-auto me-2" data-bs-dismiss="toast" aria-label="Close"></button>
		</div>
	</div>
</div>

<script>
function showAlert(text) {
	$('#alert').children('.toast-body').html(text);
	$('#alert').toast('show');
}

function hideAlert(text) {
	$('#alert').toast('hide');
}
</script>