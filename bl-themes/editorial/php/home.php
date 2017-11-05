<!-- First page -->
<?php $firstPage = array_shift($pages) ?>

<section id="banner">
        <div class="content">
                <header>
                        <h1><?php echo $firstPage->title() ?></h1>
                        <p><?php echo $firstPage->description() ?></p>
                </header>

						<p><?php echo $page->content(false) ?></p>

						<!-- Read more button -->
						<?php if($firstPage->readMore()) { ?>
						<ul class="actions">
								<li><a href="<?php echo $firstPage->permalink() ?>" class="button"><?php $Language->p('More') ?></a></li>
						</ul>
						<?php } ?>

        </div>
        <span class="image object">
                <img src="<?php echo $firstPage->coverImage() ?>" alt="" />
        </span>
</section>

<!-- Section -->
<section>
        <div class="posts">
        <?php foreach ($pages as $page): ?>
                <article>
                        <a href="<?php echo $page->permalink() ?>" class="image"><img src="<?php echo $page->coverImage() ?>" alt="" /></a>
                        <h3><?php echo $page->title() ?></h3>

								<p><?php echo $page->content(false) ?></p>

								<!-- Read more button -->
								<?php if($page->readMore()) { ?>
								<ul class="actions">
										<li><a href="<?php echo $page->permalink() ?>" class="button"><?php $Language->p('More') ?></a></li>
								</ul>
								<?php } ?>

                </article>
        <?php endforeach ?>
        </div>
</section>
