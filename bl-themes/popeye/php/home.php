<!-- Site logo and description -->
<header class="bg-light p-3">
	<div class="container text-center">

		<div class="site-logo">
			<img class="img-thumbnail rounded mx-auto d-block" height="150px" width="150px" src="<?php echo ($site->logo()?$site->logo():HTML_PATH_THEME_IMG.'logo.svg') ?>" alt="">
		</div>

		<?php if ($site->description()) : ?>
			<div class="site-description mt-2">
				<p><?php echo $site->description(); ?></p>
			</div>
		<?php endif ?>
	</div>
</header>

<?php if (empty($content)) : ?>
	<div class="text-center p-4">
		<?php $language->p('No pages found') ?>
	</div>
<?php endif ?>

<!-- Print all the content -->
<section class="mt-4 mb-4">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 mx-auto">
				<!-- Search input -->
				<form class="d-flex mb-4">
					<input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
					<button class="btn btn-outline-success" type="submit">Search</button>
				</form>
				<!-- Pages -->
				<div class="list-group list-group-flush">
					<?php foreach ($content as $page) : ?>
						<div href="#" class="list-group-item list-group-item-action pt-3 pb-3" aria-current="true">
							<div class="d-flex w-100 justify-content-between">
								<!-- Print page's title -->
								<h5 class="mb-1"><?php echo $page->title() ?></h5>
								<!-- Print page's date -->
								<small>3 days ago</small>
							</div>

							<!-- Print page's description -->
							<p class="mb-1">Some placeholder content in a paragraph.</p>

							<!-- Print page's tags -->
							<?php
							$tmp = $page->tags(true);
							if (!empty($tmp)) {
								echo '<small>';
								foreach ($tmp as $tagKey => $tagName) {
									echo '<a class="badge bg-light text-dark text-decoration-none" href="' . DOMAIN_TAGS . $tagKey . '">' . $tagName . '</a>';
								}
								echo '</small>';
							}
							?>
						</div>
					<?php endforeach ?>
				</div>
			</div>
		</div>
	</div>
</section>


<!-- Pagination -->
<?php if (Paginator::numberOfPages() > 1) : ?>
	<nav class="paginator">
		<ul class="pagination flex-wrap justify-content-center">

			<!-- Previous button -->
			<?php if (Paginator::showPrev()) : ?>
				<li class="page-item me-2">
					<a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>" tabindex="-1">&#9664; <?php echo $L->get('Previous'); ?></a>
				</li>
			<?php endif; ?>

			<!-- Home button -->
			<li class="page-item <?php if (Paginator::currentPage() == 1) echo 'disabled' ?>">
				<a class="page-link" href="<?php echo HTML::siteUrl() ?>"><?php echo $L->get('Home'); ?></a>
			</li>

			<!-- Next button -->
			<?php if (Paginator::showNext()) : ?>
				<li class="page-item ms-2">
					<a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>"><?php echo $L->get('Next'); ?> &#9658;</a>
				</li>
			<?php endif; ?>
		</ul>
	</nav>
<?php endif ?>