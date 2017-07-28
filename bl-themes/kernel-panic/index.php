<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<?php include(THEME_DIR_PHP.'head.php') ?>
</head>
<body>
	<?php Theme::plugins('siteBodyBegin') ?>

	<div id="fh5co-main">
		<div class="fh5co-intro text-center">
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<h1 class="intro-lead"><a class="home-title" href="<?php echo $Site->url() ?>"><?php echo $Site->title() ?></a></h1>
						<p class=""><?php echo $Site->description() ?></p>
					</div>
				</div>
			</div>
		</div>

		<div id="fh5co-content">
			<div class="container">
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
						<div class="row">
							<div class="col-md-3">
							<?php
								include(THEME_DIR_PHP.'sidebar.php');
							?>
							</div>
							<div class="col-md-9">
							<?php
								if ($WHERE_AM_I=='page') {
									include(THEME_DIR_PHP.'page.php');
								} else {
									include(THEME_DIR_PHP.'home.php');
								}
							?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<footer id="fh5co-footer">
		<div class="container">
			<div class="row">
				<div class="col-md-10 col-md-offset-1 text-center">
					<p><?php echo $Site->footer() ?><br>Powered by <a href="https://www.bludit.com" target="_blank">BLUDIT</a></p>
				</div>
			</div>
		</div>
	</footer>

	<?php Theme::plugins('siteBodyEnd') ?>
</body>
</html>