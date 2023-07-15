<section class="page">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 mx-auto">
				<!-- Load Bludit Plugins: Page Begin -->
				<?php Theme::plugins('pageBegin'); ?>

				<!-- Page title -->
				<h1 class="title"><?php echo $page->title(); ?></h1>

				<?php if (!$page->isStatic() && !$url->notFound() && $themePlugin->showPostInformation()) : ?>
					<div class="form-text mb-2">
						<!-- Page creation time -->
						<span class="pr-3"><i class="bi bi-calendar"></i><?php echo $page->date() ?></span>

						<!-- Page reading time -->
						<span class="pr-3"><i class="bi bi-clock"></i><?php echo $page->readingTime() . ' ' . $L->get('minutes') . ' ' . $L->g('read') ?></span>

						<!-- Page author -->
						<span><i class="bi bi-person"></i><?php echo $page->user('nickname') ?></span>
					</div>
				<?php endif ?>

				<!-- Page description -->
				<?php if ($page->description()) : ?>
					<p class="page-description"><?php echo $page->description(); ?></p>
				<?php endif ?>

				<!-- Page cover image -->
				<?php if ($page->coverImage()) : ?>
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
