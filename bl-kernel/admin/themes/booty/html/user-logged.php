<!-- Check if the user is logged -->
<script>
	setInterval(
		function() {
			var ajax = new bluditAjax();
			ajax.userLogged(showAlert);
		}, 15000);
</script>
