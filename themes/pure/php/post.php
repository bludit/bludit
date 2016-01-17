<div class="subhead"><?php echo $Language->get('Post') ?></div>

<section class="post">

    <!-- Plugins Post Begin -->
    <?php Theme::plugins('postBegin') ?>

    <!-- Post header -->
    <header class="post-header">

        <!-- Post title -->
        <h1 class="post-title">
            <a href="<?php echo $Post->permalink() ?>"><?php echo $Post->title() ?></a>
        </h1>

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
        <?php echo $Post->content() ?>
    </div>

    <!-- Plugins Post End -->
    <?php Theme::plugins('postEnd') ?>

</section>
