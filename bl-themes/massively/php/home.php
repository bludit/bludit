<!-- First page -->
<?php $firstPage = array_shift($pages) ?>

<article class="post featured">
        <header class="major">
                <span class="date"><?php echo $firstPage->date() ?></span>
                <h2><a href="<?php echo $firstPage->permalink() ?>"><?php echo $firstPage->title() ?></a></h2>
                <p><?php echo $firstPage->description() ?></p>
        </header>
        <a href="<?php echo $firstPage->permalink() ?>" class="image main"><img src="<?php echo $firstPage->coverImage() ?>" alt="" /></a>
        <ul class="actions">
                <li><a href="#" class="button big">Full Story</a></li>
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
        <a href="<?php echo $page->permalink() ?>" class="image fit"><img src="<?php echo $page->coverImage() ?>" alt="" /></a>
        <p><?php echo $page->description() ?></p>
        <ul class="actions">
                <li><a href="#" class="button">Full Story</a></li>
        </ul>
</article>

<?php endforeach ?>
</section>

<!-- Paginator -->
<footer>
        <div class="pagination">
                <!--<a href="#" class="previous">Prev</a>-->
                <a href="#" class="page active">1</a>
                <a href="#" class="page">2</a>
                <a href="#" class="page">3</a>
                <span class="extra">&hellip;</span>
                <a href="#" class="page">8</a>
                <a href="#" class="page">9</a>
                <a href="#" class="page">10</a>
                <a href="#" class="next">Next</a>
        </div>
</footer>