<section class="post">
        <header class="major">
                <span class="date"><?php echo $page->date() ?></span>
                <h1><?php echo $page->title() ?></h1>
                <p><?php echo $page->description() ?></p>
        </header>
        <div class="image main"><img src="<?php echo $page->coverImage() ?>" alt="" /></div>
        <?php echo $page->content() ?>
</section>
