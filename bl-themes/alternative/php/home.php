<?php foreach ($pages as $page): ?>

	<?php Theme::plugins('pageBegin') ?>

	<section id="<?php echo $page->key() ?>" class="bg-light">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto">
					<!-- Page title -->
					<h2>
						<?php echo $page->title() ?>
					</h2>

					<!-- Page content -->
					<p class="lead">
						<?php echo $page->content() ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<?php Theme::plugins('pageEnd') ?>

<?php endforeach ?>
