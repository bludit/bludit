<!-- Section -->
<section class="content">
        <?php foreach ($pages as $page): ?>
                <article class="page">
                        <?php if( $page->title() ) { ?>
                        <header>
                                <h2><?php echo $page->title() ?></h2>
                        </header>
                        <?php } ?>

                        <?php if( $page->coverImage() ) { ?>
                                <img src="<?php echo $page->coverImage() ?>" alt="<?php echo $page->slug() ?>">
                        <?php } ?>

                        <?php echo $page->content() ?>

                        <footer>
                                <?php echo $page->date() ?>
                        </footer>
                </article>
        <?php endforeach ?>
</section>