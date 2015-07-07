<h1 class="subhead">Page</h1>

<section class="page">

    <!-- page header -->
    <header class="page-header">

        <!-- page title -->
        <h2 class="page-title">
            <a href="<?php echo $Page->permalink() ?>"><?php echo $Page->title() ?></a>
        </h2>

        <!-- page date and author -->
        <div class="page-meta">
            <span class="author">
                <?php
                    echo $Language->get('Posted By').' ';

                    if( Text::isNotEmpty($Page->authorFirstName()) && Text::isNotEmpty($Page->authorLastName()) ) {
                        echo $Page->authorFirstName().', '.$Page->authorLastName();
                    }
                    else {
                        echo $Page->username();
                    }
                ?>
            </span>
        </div>

    </header>

    <!-- page content -->
    <div class="page-content">
        <?php echo $Page->content() ?>
    </div>

</section>