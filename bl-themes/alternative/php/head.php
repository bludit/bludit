<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="Bludit CMS">

<!-- Dynamic title tag -->
<?php echo Theme::headTitle() ?>

<!-- Dynamic description tag -->
<?php echo Theme::headDescription() ?>

<!-- Favicon -->
<?php echo Theme::favicon('img/favicon.png') ?>

<!-- Load CSS: Bootstrap core -->
<?php echo Theme::css('css/bootstrap.min.css') ?>

<!-- Load CSS: Styles for this theme -->
<?php echo Theme::css('css/style.css') ?>

<!-- Load plugins for the site head -->
<?php Theme::plugins('siteHead') ?>