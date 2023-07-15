<!DOCTYPE html>
<html>

<head>
  <title>Bludit</title>
  <meta charset="<?php echo CHARSET ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="robots" content="noindex,nofollow">

  <!-- Favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo HTML_PATH_CORE_IMG . 'favicon.png?version=' . BLUDIT_VERSION ?>">

  <!-- CSS -->
  <?php
  echo Theme::cssBootstrap();
  echo Theme::css(array(
    'bludit.css',
    'bludit.bootstrap.css'
  ), DOMAIN_ADMIN_THEME_CSS);
  ?>

  <!-- Javascript -->
  <?php
  echo Theme::jquery();
  echo Theme::jsBootstrap();
  ?>

  <!-- Plugins -->
  <?php Theme::plugins('loginHead') ?>
</head>

<body class="login">

  <!-- Plugins -->
  <?php Theme::plugins('loginBodyBegin') ?>

  <!-- Alert -->
  <?php include('html/alert.php'); ?>

  <div class="container">
    <div class="row justify-content-md-center pt-5">
      <div class="col-md-4 mt-5 p-5 shadow-sm bg-white rounded border">
        <?php
        if (Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'] . '.php')) {
          include(PATH_ADMIN_VIEWS . $layout['view'] . '.php');
        }
        ?>
      </div>
    </div>
  </div>

  <!-- Plugins -->
  <?php Theme::plugins('loginBodyEnd') ?>

</body>

</html>
