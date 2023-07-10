<!-- Post -->
<div class="card my-5 border-0">

	<!-- Load Bludit Plugins: Page Begin -->
	<?php execPluginsByHook('pageBegin'); ?>

	<div class="card-body p-0">
		<!-- Title -->
		<a class="text-dark" href="<?php echo $page->permalink(); ?>">
			<h1 class="title"><?php echo $page->title(); ?></h1>
		</a>

		<?php if (!$page->isStatic() && !$url->notFound()): ?>
        <!-- Creation date -->
        <h6 class="card-subtitle mt-3 mb-4 text-muted">
            <i class="bi bi-calendar"></i><?php echo $page->date(); ?>
            <i class="ms-3 bi bi-clock-history"></i><?php echo $L->get('Reading time') . ': ' . $page->readingTime(); ?>
        </h6>
		<?php endif ?>

        <!-- Cover image -->
        <?php if ($page->coverImage()): ?>
        <div class="cover-image mt-4 mb-4" style="background-image: url('<?php echo $page->coverImage(); ?>')"/></div>
        <?php endif ?>

		<!-- Full content -->
		<?php echo $page->content(); ?>

	</div>

	<!-- Load Bludit Plugins: Page End -->
	<?php execPluginsByHook('pageEnd'); ?>

</div>
