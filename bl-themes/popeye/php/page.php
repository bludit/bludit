<section class="page mt-4 mb-4">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 mx-auto">
				<!-- Load Bludit Plugins: Page Begin -->
				<?php execPluginsByHook('pageBegin'); ?>

				<!-- Page information -->
				<div class="page-information form-text">
					<span><?php echo $page->date() ?></span>
					<span class="ps-3"><?php echo $page->readingTime().' '.$L->g('read') ?></span>
				</div>

				<!-- Page title -->
				<h1 class="title"><?php echo $page->title(); ?></h1>

				<!-- Page description -->
				<?php if ($page->description()): ?>
				<p class="italic mt-1 mb-3 color-light"><?php echo $page->description(); ?></p>
				<?php endif ?>

				<!-- Page content -->
				<div class="page-content">
				<?php echo $page->content(); ?>
				</div>

				<!-- Load Bludit Plugins: Page End -->
				<?php execPluginsByHook('pageEnd'); ?>
			</div>
		</div>
	</div>
</section>