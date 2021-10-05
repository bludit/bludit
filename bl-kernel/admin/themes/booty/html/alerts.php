<div aria-live="polite" aria-atomic="true" class="position-relative z-index-master">
	<div class="toast-container position-absolute start-50 translate-middle-x mt-3" style="z-index:1100;">
		<div id="alert" class="toast text-center text-white border-0 p-3" role="alert" aria-live="assertive" aria-atomic="true">
			Hello, I'm a Bludit alert!
		</div>
	</div>
</div>

<script>
	function showAlert(text, background='primary') {
		$('#alert').removeClass('bg-danger bg-warning bg-primary').addClass('bg-'+background);
		$('#alert').html(text);
		$('#alert').toast('show');
	}

	function showAlertError(text) {
		showAlert(text, 'danger');
	}

	function showAlertWarning(text) {
		showAlert(text, 'warning');
	}

	function showAlertInfo(text) {
		showAlert(text, 'primary');
	}

	function hideAlert(text) {
		$('#alert').toast('hide');
	}
</script>