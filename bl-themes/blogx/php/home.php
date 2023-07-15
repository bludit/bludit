<?php if (empty($content)) : ?>
  <div class="mt-4">
    <?php $language->p('No pages found') ?>
  </div>
<?php endif ?>

<?php foreach ($content as $page) : ?>
  <!-- Post -->
  <div class="card my-5 border-0">

    <!-- Load Bludit Plugins: Page Begin -->
    <?php Theme::plugins('pageBegin'); ?>

    <!-- Cover image -->
    <?php if ($page->coverImage()) : ?>
      <img class="card-img-top mb-3 rounded-0" alt="Cover Image" src="<?php echo $page->coverImage(); ?>" />
    <?php endif ?>

    <div class="card-body p-0">
      <!-- Title -->
      <a class="text-dark" href="<?php echo $page->permalink(); ?>">
        <h2 class="title"><?php echo $page->title(); ?></h2>
      </a>

      <!-- Creation date -->
      <h6 class="card-subtitle mt-1 mb-4 text-muted">
        <i class="bi bi-calendar"></i><?php echo $page->date(); ?>
        <i class="ml-3 bi bi-clock-history"></i><?php echo $L->get('Reading time') . ': ' . $page->readingTime(); ?>
      </h6>

      <!-- Breaked content -->
      <?php echo $page->contentBreak(); ?>

      <!-- "Read more" button -->
      <?php if ($page->readMore()) : ?>
        <a href="<?php echo $page->permalink(); ?>"><?php echo $L->get('Read more'); ?></a>
      <?php endif ?>

    </div>

    <!-- Load Bludit Plugins: Page End -->
    <?php Theme::plugins('pageEnd'); ?>

  </div>
  <hr>
<?php endforeach ?>

<!-- Pagination -->
<?php if (Paginator::numberOfPages() > 1) : ?>
  <nav class="paginator">
    <ul class="pagination flex-wrap">

      <!-- Previous button -->
      <?php if (Paginator::showPrev()) : ?>
        <li class="page-item mr-2">
          <a class="page-link" href="<?php echo Paginator::previousPageUrl() ?>" tabindex="-1">&#9664; <?php echo $L->get('Previous'); ?></a>
        </li>
      <?php endif; ?>

      <!-- Home button -->
      <li class="page-item <?php if (Paginator::currentPage() == 1) echo 'disabled' ?>">
        <a class="page-link" href="<?php echo Theme::siteUrl() ?>">Home</a>
      </li>

      <!-- Next button -->
      <?php if (Paginator::showNext()) : ?>
        <li class="page-item ml-2">
          <a class="page-link" href="<?php echo Paginator::nextPageUrl() ?>"><?php echo $L->get('Next'); ?> &#9658;</a>
        </li>
      <?php endif; ?>

    </ul>
  </nav>
<?php endif ?>
