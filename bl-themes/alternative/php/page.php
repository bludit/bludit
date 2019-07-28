<section class="page">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 mx-auto">
				<!-- Load Bludit Plugins: Page Begin -->
				<?php Theme::plugins('pageBegin'); ?>

				<!-- Page title -->
				<h1 class="title"><?php echo $page->title(); ?></h1>

				<!-- Page description -->
				<?php if ($page->description()): ?>
				<p class="page-description"><?php echo $page->description(); ?></p>
				<?php endif ?>

				<!-- Page cover image -->
				<?php if ($page->coverImage()): ?>
				<div class="page-cover-image py-6 mb-4" style="background-image: url('<?php echo $page->coverImage(); ?>');">
					<div style="height: 300px;"></div>
				</div>
				<?php endif ?>

				<!-- Page content -->
				<div class="page-content">
				<?php echo $page->content(); ?>
				</div>

				<!-- Load Bludit Plugins: Page End -->
				<?php Theme::plugins('pageEnd'); ?>
			</div>
		</div>
	</div>
</section>
