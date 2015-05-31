<h1 class="content-subhead">Recent Posts</h1>

<?php foreach ($posts as $Post): ?>

<section class="post">
    <header class="post-header">
        <h2 class="post-title">
            <a class="ptitle" href="<?php echo $Post->permalink() ?>"><?php echo $Post->title() ?></a>
        </h2>

        <p class="post-meta">
            <span><?php echo $Language->get('Posted By').' '.$Post->author() ?></span>
            <span>Date: <?php echo $Post->dateCreated() ?></span>
        </p>
    </header>

    <div class="post-description">
        <?php echo $Post->content() ?>
    </div>
</section>

<?php endforeach; ?>
