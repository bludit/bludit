<?php
// Admin color mode from Settings > General: "system", "light" or "dark".
// Light and dark are set on <html> by the caller; "system" follows the OS and updates live.
if ($site->adminColorMode() === 'system'):
?>
<script>
(function () {
	var media = window.matchMedia('(prefers-color-scheme: dark)');
	function apply() {
		document.documentElement.setAttribute('data-bs-theme', media.matches ? 'dark' : 'light');
	}
	apply();
	media.addEventListener('change', apply);
})();
</script>
<?php endif; ?>
