<!DOCTYPE html>
<html>
<head>
<?php include(THEME_DIR_PHP.'head.php'); ?>
</head>
<body>

	<!-- Load Bludit Plugins: Site Body Begin -->
	<?php Theme::plugins('siteBodyBegin'); ?>

	<!-- Navbar -->
	<div>
	<?php include(THEME_DIR_PHP.'navbar.php'); ?>
	</div>

	<!-- Content -->
	<div class="container">
		<div class="row">
			<div class="col-md-3 side-nav-col">
			<?php
				include(THEME_DIR_PHP.'sidebar.php');
			?>
			</div>

			<div class="col-md-9 doc-content">
				<div class="main">
				<?php
					include(THEME_DIR_PHP.'page.php');
				?>
				</div>
			</div>
		</div>

		<footer>
			<p class="m-0 text-right text-black text-uppercase"><?php echo $site->footer(); ?><span class="ml-3 text-warning">Powered by <a target="_blank" class="text-warning" href="https://www.bludit.com">Bludit</a></span></p>
		</footer>
	</div>

	<!-- Javascript -->
	<?php
		echo Theme::js('js/jquery.min.js');
		echo Theme::js('js/bootstrap.min.js');
		echo Theme::js('js/highlight.min.js');
	?>

	<!-- Init Highlight -->
	<script>
		hljs.initHighlighting();
	</script>

	<!-- TOC generator -->
	<script>
		$(document).ready(function() {
			var enableToc = false;
			if ($('#page-content > h2').length > 1) {
				$('#page-content > h2').each(function() {
					if ($(this).attr('id')) {
						enableToc = true;
						$('#toc-content').append('<li><a href="#' + $(this).attr('id') + '">' + $(this).text() + '</a></li>');
					}
				});
			}
			if (enableToc) {
				$('#toc').show();
			}
		});
	</script>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>
