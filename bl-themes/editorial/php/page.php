<section>
        <header class="main">
                <!-- Title of the page -->
                <h1><?php echo $page->title() ?></h1>
        </header>

        <!-- Cover image of the page -->
        <span class="image main"><img src="<?php echo $page->coverImage() ?>" alt="" /></span>

        <!-- Content of the page -->
        <?php echo $page->content() ?>

        <!-- Plugins with the hook pageEnd -->
        <?php Theme::plugins('pageEnd') ?>
</section>