<h1 class="subhead"><?php echo $Language->get('Recent posts') ?></h1>

<?php foreach ($posts as $Post): ?>

<section class="post">

    <!-- Plugins Post Begin -->
    <?php Theme::plugins('postBegin') ?>

    <!-- Post header -->
    <header class="post-header">

        <!-- Post title -->
        <h2 class="post-title">
            <a href="<?php echo $Post->permalink() ?>"><?php echo $Post->title() ?></a>
        </h2>

        <!-- Post date and author -->
        <div class="post-meta">
            <span class="date"><?php echo $Post->date() ?></span>
            <span class="author">
                <?php
                    echo $Language->get('Posted By').' ';

                    if( Text::isNotEmpty($Post->user('firstName')) || Text::isNotEmpty($Post->user('lastName')) ) {
                        echo $Post->user('firstName').' '.$Post->user('lastName');
                    }
                    else {
                        echo $Post->user('username');
                    }
                ?>
            </span>
        </div>

    </header>

    <!-- Post content -->
    <div class="post-content">
        <?php
            // Call the method with FALSE to get the first part of the post
            echo $Post->content(false)
        ?>
    </div>

    <?php if($Post->readMore()) { ?>
    <a class="read-more" href="<?php echo $Post->permalink() ?>"><?php $Language->printMe('Read more') ?></a>
    <?php } ?>

    <!-- Plugins Post End -->
    <?php Theme::plugins('postEnd') ?>

</section>

<?php endforeach; ?>

<!-- Paginator for posts -->
<?php
    echo Paginator::html();
?>
