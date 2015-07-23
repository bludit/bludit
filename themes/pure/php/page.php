<h1 class="subhead"><?php echo $Language->get('Page') ?></h1>

<section class="page">

	<?php Theme::plugins('beforePagesLoad'); // Plugin ?>

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
    
	<?php Theme::plugins('afterPagesLoad'); // Plugin ?>

</section>