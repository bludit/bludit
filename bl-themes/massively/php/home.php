<!-- First page -->
<?php $firstPage = array_shift($pages) ?>

<article class="post featured">
        <header class="major">
                <span class="date"><?php echo $firstPage->date() ?></span>
                <h2><a href="<?php echo $firstPage->permalink() ?>#main"><?php echo $firstPage->title() ?></a></h2>
                <p><?php echo $firstPage->description() ?></p>
        </header>
        <a href="<?php echo $firstPage->permalink() ?>#main" class="image main"><img src="<?php echo $firstPage->coverImage() ?>" alt="" /></a>
        <ul class="actions">
                <li><a href="<?php echo $firstPage->permalink() ?>#main" class="button big">Full Story</a></li>
        </ul>
</article>

<!-- For each page left -->
<section class="posts">
<?php foreach ($pages as $page): ?>

<article>
        <header>
                <span class="date"><?php echo $page->date() ?></span>
                <h2><a href="<?php echo $page->permalink() ?>"><?php echo $page->title() ?></a></h2>
        </header>
        <a href="<?php echo $page->permalink() ?>#main" class="image fit"><img src="<?php echo $page->coverImage() ?>" alt="" /></a>
        <p><?php echo $page->description() ?></p>
        <ul class="actions">
                <li><a href="<?php echo $page->permalink() ?>#main" class="button">Full Story</a></li>
        </ul>
</article>

<?php endforeach ?>
</section>

<!-- Paginator -->
<footer>
        <div class="pagination">
                <?php
                        // Show previus page link
                        if(Paginator::showPrev()) {
                                echo '<a href="'.Paginator::prevPageUrl().'" class="previous">Prev</a>';
                        }

                        for($i=1; $i<=Paginator::amountOfPages(); $i++) {
                                echo '<a href='.Paginator::numberUrl($i).' class="page">'.$i.'</a>';
                        }

                        // Show next page link
                        if(Paginator::showNext()) {
                                echo '<a href="'.Paginator::nextPageUrl().'" class="next">Next</a>';
                        }
                ?>
        </div>
</footer>