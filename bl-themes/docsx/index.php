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
					if ($WHERE_AM_I == 'page') {
						include(THEME_DIR_PHP.'page.php');
					} else {
						include(THEME_DIR_PHP.'home.php');
					}
				?>
				</div>
			</div>
		</div>

		<footer>
		<p class="pull-left">
		© Prometheus Authors 2018
		</p>
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
			if ($('#page-content > h2').length > 1) {
				$('#page-content > h2').each(function() {
					$('#toc-content').append('<li><a href="' + $(this).attr('id') + '">' + $(this).text() + '</a></li>');
				});
			} else {
				$('#toc').hide();
			}
		});
	</script>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>