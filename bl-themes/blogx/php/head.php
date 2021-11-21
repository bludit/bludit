<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Bludit">

<!-- Generate <title>...</title> -->
<?php echo HTML::metaTagTitle(); ?>

<!-- Generate <meta name="description" content="..."> -->
<?php echo HTML::metaTagDescription(); ?>

<!-- Generate <link rel="icon" href="..."> -->
<?php echo HTML::favicon('img/favicon.png'); ?>

<!-- Include CSS Bootstrap file from Bludit Core -->
<?php echo HTML::cssBootstrap(); ?>

<!-- Include CSS Bootstrap ICONS file from Bludit Core -->
<?php echo HTML::cssBootstrapIcons(); ?>

<!-- Include CSS Styles from this theme -->
<?php echo HTML::css('css/style.css'); ?>
<?php echo HTML::css('css/plugins.css'); ?>

<!-- Enable or disable Google Fonts from theme's settings -->
<?php if ($theme->googleFonts()): ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:sans,bold">
    <style>
        body { font-family: "Open Sans", sans-serif; }
    </style>
<?php endif; ?>

<!-- Execute Bludit plugins for the hook "Site head" -->
<?php execPluginsByHook('siteHead'); ?>