<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>">
<head>
<?php include(THEME_DIR_PHP.'head.php'); ?>
</head>
<body>

	<!-- Load Bludit Plugins: Site Body Begin -->
	<?php Theme::plugins('siteBodyBegin'); ?>

	<!-- Navbar -->
	<?php include(THEME_DIR_PHP.'navbar.php'); ?>

	<!-- Content -->
	<div class="container">
		<div class="row justify-content-md-center">

			<!-- Left -->
			<div class="col-3 pr-0">
			<?php include(THEME_DIR_PHP.'sidebar.php'); ?>
			</div>

			<!-- Right -->
			<div class="col-7 pl-2">
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

	<!-- Javascript -->
	<?php
		// Include Jquery file from Bludit Core
		echo Theme::jquery();

		// Include javascript Bootstrap file from Bludit Core
		echo Theme::jsBootstrap();

		echo Theme::javascript('vendors/lightgallery/js/lightgallery.js');
		echo Theme::javascript('vendors/lightgallery/js/lg-thumbnail.js');
		echo Theme::javascript('vendors/lightgallery/js/lg-fullscreen.js');
	?>

	<script>
		// Apply to all galleries
		var galleries = document.getElementsByClassName("image-gallery");
		for(let i = 0 ; i < galleries.length; i++){
			lightGallery(galleries[i],{
				thumbnail:true,
				share: false,
				download: true
			})
		}
	</script>

	<!-- Load Bludit Plugins: Site Body End -->
	<?php Theme::plugins('siteBodyEnd'); ?>

</body>
</html>