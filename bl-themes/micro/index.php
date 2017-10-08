<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<?php include(THEME_DIR_PHP.'head.php') ?>
</head>
<body>

	<div id="fh5co-main">
	<div id="fh5co-content">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-3">
					<div class="row">
						<div class="col-md-2">
						<?php include(THEME_DIR_PHP.'sidebar.php') ?>
						</div>

						<div class="col-md-6">
						<?php include(THEME_DIR_PHP.'home.php') ?>
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
					<p>
						<?php echo $Site->footer() ?>
						<br>
						Powered by <a href="https://www.bludit.com" target="_blank">BLUDIT</a>
						<?php
							if (pluginEnabled('RSS')) {
								echo ' - <a href="'.Theme::rssUrl().'" target="_blank">RSS</a>';
							}
							if (pluginEnabled('Sitemap')) {
								echo ' - <a href="'.Theme::sitemapUrl().'" target="_blank">Sitemap</a>';
							}
						?>
					</p>
				</div>
			</div>
		</div>
	</footer>

</body>
</html>