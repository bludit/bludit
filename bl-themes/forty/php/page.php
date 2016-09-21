<section id="one">

	<!-- Plugins Page Begin -->
	<?php Theme::plugins('pageBegin') ?>

		<!-- Page title -->
		<div class="inner">
			<header class="major">
				<h1><?php echo $Page->title() ?></h1>
				<p><?php echo $Page->description() ?></p>
			</header>

		<!-- Cover Image -->
		<?php
			if($Page->coverImage()) {
				echo '<a href="'.$Page->permalink().'" class="image featured"><img src="'.$Page->coverImage().'" alt="Cover Image"></a>';
			}
		?>

		<!-- Post's content, the first part if has pagebrake -->
		<?php echo $Page->content() ?>

	</div>

	<!-- Plugins Page End -->
	<?php Theme::plugins('pageEnd') ?>

</section>