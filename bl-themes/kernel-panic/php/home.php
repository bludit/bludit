<!-- Section -->
<section class="content">
        <?php foreach ($pages as $page): ?>
                <article class="page">
                        <header>
                                <a href="<?php echo $page->permalink() ?>">
                                        <h2><?php echo $page->title() ?></h2>
                                </a>

                                <?php if( $page->coverImage() ) { ?>
                                        <img src="<?php echo $page->coverImage() ?>" alt="<?php echo $page->slug() ?>">
                                <?php } ?>
                        </header>
                        <?php echo $page->contentBreak() ?>
                        <footer>
                                <?php if ($page->readMore() ) { ?>
                                <div class="readmore">
                                        <a href="<?php echo $page->permalink() ?>">
                                                <i class="icon-arrow-down"></i> <?php echo $Language->get('Read more') ?>
                                        </a>
                                </div>
                                <?php } ?>
                        </footer>
                </article>
        <?php endforeach ?>
</section>