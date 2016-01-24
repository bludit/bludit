<title><?php echo $Site->title() ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $Site->description() ?>">

<!--[if lte IE 8]><script src="<?php echo HTML_PATH_THEME ?>assets/js/ie/html5shiv.js"></script><![endif]-->

<link rel="stylesheet" href="<?php echo HTML_PATH_THEME ?>assets/css/main.css">
<!--[if lte IE 9]><link rel="stylesheet" href="<?php echo HTML_PATH_THEME ?>assets/css/ie9.css" /><![endif]-->
<!--[if lte IE 8]><link rel="stylesheet" href="<?php echo HTML_PATH_THEME ?>assets/css/ie8.css" /><![endif]-->
<link rel="stylesheet" href="<?php echo HTML_PATH_THEME ?>assets/css/bludit.css">

<link rel="shortcut icon" href="<?php echo HTML_PATH_THEME ?>img/favicon.png" type="image/x-icon">

<?php

Theme::fontAwesome();

// Plugins for head
Theme::plugins('siteHead');

?>