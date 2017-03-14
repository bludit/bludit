<?php
	// <meta charset="utf-8">
	Theme::charset('utf-8');

	// <meta name="viewport" content="width=device-width, initial-scale=1">
	Theme::viewport('width=device-width, initial-scale=1');

	// <title>...</title>
	Theme::title();

	// <meta name="description" content=".....">
	Theme::description();

	// <link rel="shortcut icon" href="favicon.png">
	Theme::favicon('favicon.png');

	// CSS files
	Theme::css('style.css');
	Theme::css('bludit.css');
?>

<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- Google Webfont
-->
<link href='//fonts.googleapis.com/css?family=Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Roboto+Slab:400,300,100,500' rel='stylesheet' type='text/css'>

<!-- Load plugins
- Hook: Site head
-->
<?php Theme::plugins('siteHead') ?>