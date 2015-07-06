<h1 class="subhead">Posts</h1>

<section class="post">
    <header class="post-header">
        <h2 class="post-title">
            <?php echo $Post->title() ?>
        </h2>

        <p class="post-meta">
            <span>Posted by <?php echo $Post->author() ?></span>
            <span>Date: <?php echo $Post->dateCreated() ?></span>
        </p>
    </header>

    <div class="post-description">
        <?php echo $Post->content() ?>
    </div>
</section>