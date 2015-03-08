<base href="<?php echo HTML_PATH_THEME ?>">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="A layout example that shows off a blog page with a list of posts.">

<title>Blog &ndash; Layout Examples &ndash; Pure</title>

<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,cyrillic,latin-ext">
<link rel="stylesheet" href="css/pure-min.css">
<link rel="stylesheet" href="css/grids-responsive-min.css">
<link rel="stylesheet" href="css/blog.css">
<link rel="stylesheet" href="css/rainbow.github.css">

<script src="js/rainbow.min.js"></script>

<style>
html, button, input, select, textarea,
.pure-g [class *= "pure-u"] {
    /* Set your content font stack here: */
    font-family: 'Open Sans', sans-serif;
}
</style>

<!-- Plugins -->
<?php
	Theme::plugins('onSiteHead');
?>
