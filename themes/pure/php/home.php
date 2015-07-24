<h1 class="subhead"><?php echo $Language->get('Recent posts') ?></h1>

<?php foreach ($posts as $Post): ?>

<section class="post">

    <!-- Post header -->
    <header class="post-header">

        <!-- Post title -->
        <h2 class="post-title">
            <a href="<?php echo $Post->permalink() ?>"><?php echo $Post->title() ?></a>
        </h2>

        <!-- Post date and author -->
        <div class="post-meta">
            <span class="date"><?php echo $Post->dateCreated() ?></span>
            <span class="author">
                <?php
                    echo $Language->get('Posted By').' ';

                    if( Text::isNotEmpty($Post->authorFirstName()) && Text::isNotEmpty($Post->authorLastName()) ) {
                        echo $Post->authorFirstName().', '.$Post->authorLastName();
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
        <?php echo $Post->content(false) // FALSE to get the first part of the post ?>
    </div>

    <a class="read-more" href="<?php echo $Post->permalink() ?>"><?php $Language->printMe('Read more') ?></a>

</section>

<?php endforeach; ?>

<?php
    echo Paginator::html();
?>