<h1 class="subhead"><?php echo isset($pages[$Page->parentKey()])?$pages[$Page->parentKey()]->title().' -> ':'' ?><?php echo $Page->title() ?></h1>

<section class="page">

    <!-- Plugins Page Begin -->
    <?php Theme::plugins('pageBegin') ?>

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

    <!-- Plugins Page End -->
    <?php Theme::plugins('pageEnd') ?>

</section>