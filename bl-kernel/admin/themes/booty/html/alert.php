<!-- Alert -->
<script>
	function showAlert(text) {
		$("#alert").html(text);
		$("#alert").slideDown().delay(3500).slideUp();
	}

	<?php if (Alert::defined()): ?>
	setTimeout(function(){ showAlert("<?php echo Alert::get() ?>") }, 500);
	<?php endif; ?>

	$(window).click(function() {
		$("#alert").hide();
	});
</script>

<div id="alert" class="alert <?php echo (Alert::status()==ALERT_STATUS_FAIL)?'alert-danger':'alert-success' ?>"></div>