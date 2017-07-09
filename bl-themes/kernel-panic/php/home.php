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
                        <?php echo $page->content() ?>
                        <footer>
                                <div class="category"><i class="icon-price-tag"></i> <?php echo $page->category() ?></div>
                        </footer>
                </article>
        <?php endforeach ?>
</section>