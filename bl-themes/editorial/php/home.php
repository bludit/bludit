<!-- First page -->
<?php $firstPage = array_shift($pages) ?>

<section id="banner">
        <div class="content">
                <header>
                        <h1><?php echo $firstPage->title() ?></h1>
                </header>
                <p><?php echo $firstPage->description() ?></p>
                <ul class="actions">
                        <li><a href="<?php echo $firstPage->permalink() ?>" class="button big">Learn More</a></li>
                </ul>
        </div>
        <span class="image object">
                <img src="<?php echo $firstPage->coverImage() ?>" alt="" />
        </span>
</section>

<!-- Section -->
<section>
        <header class="major">
                <h2>Ipsum sed dolor</h2>
        </header>

        <div class="posts">
        <?php foreach ($pages as $page): ?>
                <article>
                        <a href="#" class="image"><img src="<?php echo $page->coverImage() ?>" alt="" /></a>
                        <h3><?php echo $page->title() ?></h3>
                        <p><?php echo $page->description() ?></p>
                        <ul class="actions">
                                <li><a href="<?php echo $page->permalink() ?>" class="button">More</a></li>
                        </ul>
                </article>
        <?php endforeach ?>
        </div>
</section>
