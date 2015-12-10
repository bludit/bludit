<h1 class="subhead"><?php echo $Language->get('Post') ?></h1>

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

                    if( Text::isNotEmpty($Post->authorFirstName()) || Text::isNotEmpty($Post->authorLastName()) ) {
                        echo $Post->authorFirstName().' '.$Post->authorLastName();
                    }
                    else {
                        echo $Post->username();
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
