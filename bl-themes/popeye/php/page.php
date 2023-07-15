<section class="page mt-4 mb-4">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 mx-auto">
				<!-- Load Bludit Plugins: Page Begin -->
				<?php Theme::plugins('pageBegin'); ?>

				<?php if (!$page->isStatic() && !$url->notFound()) : ?>
					<div class="form-text mb-2">
						<!-- Page creation time -->
						<span class="pr-3"><i class="bi bi-calendar"></i><?php echo $page->date() ?></span>

						<!-- Page reading time -->
						<span class="pr-3"><i class="bi bi-clock"></i><?php echo $page->readingTime() . ' ' . $L->get('minutes') . ' ' . $L->g('read') ?></span>

						<!-- Page author -->
						<span><i class="bi bi-person"></i><?php echo $page->user('nickname') ?></span>
					</div>
				<?php endif ?>

				<!-- Page title -->
				<h1 class="page-title bold"><?php echo $page->title(); ?></h1>

				<!-- Page description -->
				<?php if ($page->description()) : ?>
					<p class="page-description italic mt-1 color-light"><?php echo $page->description(); ?></p>
				<?php endif ?>

				<!-- Page content -->
				<div class="page-content mt-3">
					<?php echo $page->content(); ?>
				</div>

				<!-- Load Bludit Plugins: Page End -->
				<?php Theme::plugins('pageEnd'); ?>
			</div>
		</div>
	</div>
</section>

<!-- Related pages -->
<?php
$relatedPages = $page->related(true, 3);
?>
<?php if (!empty($relatedPages)) : ?>
	<section class="related mt-4 mb-4">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto p-4 bg-light">
					<h4><?php $L->p('Related pages') ?></h4>
					<div class="list-group list-group-flush">
						<?php foreach ($relatedPages as $pageKey) : ?>
							<?php $tmp = new Page($pageKey); ?>
							<div class="list-group-item pt-4 pb-4" aria-current="true">
								<div class="d-flex w-100 justify-content-between">

									<!-- Related page title -->
									<a href="<?php echo $tmp->permalink() ?>">
										<h5 class="mb-1"><?php echo $tmp->title() ?></h5>
									</a>

									<!-- Related page date -->
									<small class="color-blue"><?php echo $tmp->relativeTime() ?></small>
								</div>

								<!-- Related page description -->
								<?php if ($tmp->description()) : ?>
									<p class="mb-1 form-text"><?php echo $tmp->description(); ?></p>
								<?php endif ?>

							</div>
						<?php endforeach ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>
