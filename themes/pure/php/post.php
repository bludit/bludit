<h1 class="subhead"><?php echo $Language->get('Post') ?></h1>

<section class="post">

    <!-- Post header -->
    <header class="post-header">

        <!-- Post title -->
        <h2 class="post-title">
            <a href="<?php echo $Post->permalink() ?>"><?php echo $Post->title() ?></a>
        </h2>

		<?php Theme::plugins('beforePostsLoad'); // Plugin ?>
		
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
        <?php echo $Post->content() ?>
    </div>

	<?php Theme::plugins('afterPostsLoad'); // Plugin ?>
		
</section>