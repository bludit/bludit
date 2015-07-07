<h1 class="subhead">Page</h1>

<section class="page">

    <!-- page header -->
    <header class="page-header">

        <!-- page title -->
        <h2 class="page-title">
            <a href="<?php echo $Page->permalink() ?>"><?php echo $Page->title() ?></a>
        </h2>

    </header>

    <!-- page content -->
    <div class="page-content">
        <?php echo $Page->content() ?>
    </div>

</section>