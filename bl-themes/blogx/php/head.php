<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="generator" content="Bludit">

<!-- Dynamic title tag -->
<?php echo HTML::metaTags('title'); ?>

<!-- Dynamic description tag -->
<?php echo HTML::metaTags('description'); ?>

<!-- Include Favicon -->
<?php echo HTML::favicon('img/favicon.png'); ?>

<!-- Include Bootstrap CSS file bootstrap.css -->
<?php echo HTML::cssBootstrap(); ?>

<!-- Include CSS Styles from this theme -->
<?php echo HTML::css('css/style.css'); ?>

<!-- Load Bludit Plugins: Site head -->
<?php execPluginsByHook('siteHead'); ?>
