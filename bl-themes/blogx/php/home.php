<?php if (empty($content)) : ?>
  <div class="mt-4">
    <?php $language->p('No pages found') ?>
  </div>
<?php endif ?>

<?php foreach ($content as $page) : ?>
  <!-- Post -->
  <div class="card card-modern my-5">

    <!-- Load Bludit Plugins: Page Begin -->
    <?php Theme::plugins('pageBegin'); ?>

    <!-- Cover image with gradient overlay -->
    <?php if ($page->coverImage()) : ?>
      <div class="cover-image-wrapper">
        <img class="card-img-top" alt="<?php echo $page->title(); ?>" src="<?php echo $page->coverImage(); ?>" />
      </div>
    <?php endif ?>

    <div class="card-body">
      <!-- Title -->
      <a href="<?php echo $page->permalink(); ?>">
        <h2 class="title"><?php echo $page->title(); ?></h2>
      </a>

      <!-- Creation date and reading time -->
      <div class="metadata mb-4">
        <span><i class="bi bi-calendar"></i><?php echo $page->date(); ?></span>
        <span><i class="bi bi-clock-history"></i><?php echo $L->get('Reading time') . ': ' . $page->readingTime(); ?></span>
      </div>

      <!-- Breaked content -->
      <?php echo $page->contentBreak(); ?>

      <!-- "Read more" button -->
      <?php if ($page->readMore()) : ?>
        <a class="btn-primary-gradient mt-3" href="<?php echo $page->permalink(); ?>">
          <?php echo $L->get('Read more'); ?>
          <i class="bi bi-arrow-right"></i>
        </a>
      <?php endif ?>

    </div>

    <!-- Load Bludit Plugins: Page End -->
    <?php Theme::plugins('pageEnd'); ?>

  </div>
<?php endforeach ?>

<!-- Pagination -->
<?php if (Paginator::numberOfPages() > 1) : ?>
  <nav class="paginator mt-5">
    <ul class="pagination flex-wrap justify-content-center">

      <!-- Previous button -->
      <?php if (Paginator::showPrev()) : ?>
        <li class="page-item mr-2">
          <a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>" tabindex="-1">
            <i class="bi bi-chevron-left"></i> <?php echo $L->get('Previous'); ?>
          </a>
        </li>
      <?php endif; ?>

      <!-- Home button -->
      <li class="page-item mx-2 <?php if (Paginator::currentPage() == 1) echo 'disabled' ?>">
        <a class="page-link" href="<?php echo Theme::siteUrl() ?>">
          <i class="bi bi-house-door"></i> Home
        </a>
      </li>

      <!-- Next button -->
      <?php if (Paginator::showNext()) : ?>
        <li class="page-item ml-2">
          <a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>">
            <?php echo $L->get('Next'); ?> <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      <?php endif; ?>

    </ul>
  </nav>
<?php endif ?>
