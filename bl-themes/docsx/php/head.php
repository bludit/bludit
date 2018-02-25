<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="Bludit CMS">

<!-- Dynamic title tag -->
<?php echo Theme::headTitle(); ?>

<!-- Dynamic description tag -->
<?php echo Theme::headDescription(); ?>

<!-- Favicon -->
<?php echo Theme::favicon('img/favicon.png'); ?>

<!-- CSS: Bootstrap -->
<?php echo Theme::css('css/bootstrap.min.css'); ?>

<!-- CSS: Styles for this theme -->
<?php echo Theme::css('css/style.css'); ?>

<!-- CSS: Highlight -->
<?php echo Theme::css('css/highlight.github.min.css'); ?>

<!-- Google Fonts -->
<link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

<!-- Load Bludit Plugins: Site head -->
<?php Theme::plugins('siteHead'); ?>