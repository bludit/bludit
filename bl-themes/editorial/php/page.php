<section>
        <header class="main">
                <h1><?php echo $page->title() ?></h1>
        </header>

        <span class="image main"><img src="<?php echo $page->coverImage() ?>" alt="" /></span>

        <?php echo $page->content() ?>
</section>