<!DOCTYPE html>
<html class="js" lang="<?php echo $Site->language() ?>">

	<head profile="http://gmpg.org/xfn/11">
		<?php include(THEME_DIR_PHP.'head.php') ?>
	</head>

	<?php
		if($WHERE_AM_I == 'page') {
			if( $Page->status() == "fixed" ) {
				echo '<body class="page page-template-default">';
			}
			elseif( $Page->status() == "published" ) {
				echo '<body class="single single-post">';
			}
		} else {
			echo '<body class="header-image fullwidth">';
		}
	?>

		<!-- Plugins Site Body Begin -->
		<?php Theme::plugins('siteBodyBegin') ?>

		<div class="navigation" >
			<div class="navigation-inner section-inner">
				<ul class="blog-menu">
					<?php
						echo '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="'.$Site->url().'">'.$Language->get('Homepage').'</a></li> ';

						$staticPages = $dbPages->getStaticDB();
						foreach ($staticPages as $pageKey) {
							$staticPage = buildPage($pageKey);
							echo '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="'.$staticPage->permalink().'">'.$staticPage->title().'</a></li> ';
						}
					?>
				 </ul>
				 <div class="clear"></div>
			</div> <!-- /navigation-inner -->
		</div> <!-- /navigation -->

		<div class="header section">
			<div class="header-inner section-inner">

				<?php
					$file = DOMAIN_UPLOADS_PROFILES.'admin.png';
					if( !file_exists(PATH_UPLOADS_PROFILES.'admin.png') ) {
						$file = DOMAIN_THEME.'css/images/logo.png';
					}
					echo '<a href="'.$Site->url().'" rel="home" class="logo"><img src="'.$file.'" alt="'.$Site->title().'"></a>';
				?>

				<h1 class="blog-title">
					<a href="<?php echo $Site->url() ?>" rel="home"><?php echo $Site->title() ?></a>
				</h1>

				<div class="nav-toggle">
					<div class="bar"></div>
					<div class="bar"></div>
					<div class="bar"></div>
					<div class="clear"></div>
				</div>

				<div class="clear"></div>
			</div> <!-- /header section -->
		</div> <!-- /header-inner section-inner -->

		<?php
			if($WHERE_AM_I == 'page') {
				if( $Page->status() == "static" ) {
					include(THEME_DIR_PHP.'page.php');
				}
				else {
					include(THEME_DIR_PHP.'post.php');
				}
			} else {
				include(THEME_DIR_PHP.'home.php');
			}
		?>

		<!-- Footer -->
		<?php include(THEME_DIR_PHP.'sidebar.php') ?>

		<div class="credits section">
			<div class="credits-inner section-inner">

				<p class="credits-left">
					<span><?php echo $Site->footer() ?></span>
				</p>

				<p class="credits-right">
					<span>Lingonberry by <a href="http://www.andersnoren.se">Anders Noren</a> — Proudly Powered by <a href="https://www.bludit.com/">Bludit</a> — </span><a class="tothetop">Up ↑</a>
				</p>

				<div class="clear"></div>
			</div> <!-- /credits-inner -->
		</div> <!-- /credits -->

		<!-- Plugins Site Body End -->
		<?php Theme::plugins('siteBodyEnd') ?>

		<!-- Javascript -->
		<?php echo Theme::js(array(
			'js/flexslider.min.js',
			'js/global.js'
		)); ?>

	</body>
</html>