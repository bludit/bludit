<!DOCTYPE html>
<html lang="en">

<head>
  <title><?php echo (defined('BLUDIT_PRO') ? $site->title() : 'BLUDIT') ?> - Login</title>
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

  <style>
    body.login {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #1e88e5 0%, #1565c0 50%, #0d47a1 100%);
      padding: 20px;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
    }

    .login-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      padding: 40px;
      animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-logo {
      text-align: center;
      margin-bottom: 30px;
    }

    .login-logo .logo-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
      box-shadow: 0 8px 20px rgba(21, 101, 192, 0.4);
    }

    .login-logo .logo-icon img {
      width: 36px;
      height: 36px;
      filter: brightness(0) invert(1);
    }

    .login-logo .logo-icon.custom-logo {
      background: transparent;
      box-shadow: none;
      width: auto;
      height: auto;
      max-width: 150px;
      max-height: 80px;
    }

    .login-logo .logo-icon.custom-logo img {
      width: auto;
      height: auto;
      max-width: 150px;
      max-height: 80px;
      filter: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .login-logo h1 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #1a1a2e;
      margin: 0;
    }

    .login-logo p {
      color: #6c757d;
      font-size: 0.9rem;
      margin-top: 5px;
    }

    .login-card .form-control {
      border: 2px solid #e9ecef;
      border-radius: 10px;
      padding: 12px 16px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background-color: #f8f9fa;
    }

    .login-card .form-control:focus {
      border-color: #1e88e5;
      box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.15);
      background-color: #fff;
    }

    .login-card .form-control::placeholder {
      color: #adb5bd;
    }

    .login-card .form-group {
      margin-bottom: 20px;
    }

    .login-card .form-group label {
      font-weight: 500;
      color: #495057;
      margin-bottom: 10px;
      font-size: 0.9rem;
    }

    .login-card .btn-login {
      background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
      border: none;
      border-radius: 10px;
      padding: 12px 18px;
      font-size: 0.95rem;
      font-weight: 600;
      color: white;
      width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(21, 101, 192, 0.4);
    }

    .login-card .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(21, 101, 192, 0.5);
    }

    .login-card .btn-login:active {
      transform: translateY(0);
    }

    .login-card .form-check {
      margin-bottom: 25px;
    }

    .login-card .form-check-input {
      width: 18px;
      height: 18px;
      margin-top: 0;
      border: 2px solid #dee2e6;
      border-radius: 4px;
    }

    .login-card .form-check-input:checked {
      background-color: #1e88e5;
      border-color: #1e88e5;
    }

    .login-card .form-check-label {
      color: #6c757d;
      font-size: 0.9rem;
      padding-left: 8px;
    }

    .login-footer {
      text-align: center;
      margin-top: 25px;
      padding-top: 20px;
      border-top: 1px solid #e9ecef;
    }

    .login-footer p {
      color: #6c757d;
      font-size: 0.85rem;
      margin: 0;
    }

    .login-footer a {
      color: #1e88e5;
      text-decoration: none;
    }

    /* Alert styles for login page */
    .login-alert {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1050;
      min-width: 300px;
      max-width: 90%;
      border-radius: 10px;
      padding: 12px 20px;
      font-weight: 500;
      font-size: 0.9rem;
      animation: slideDown 0.4s ease-out;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateX(-50%) translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
      }
    }

    .login-alert.alert-danger {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
      color: white;
      border: none;
      box-shadow: 0 4px 15px rgba(238, 90, 90, 0.4);
    }

    .login-alert.alert-success {
      background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
      color: white;
      border: none;
      box-shadow: 0 4px 15px rgba(64, 192, 87, 0.4);
    }

    /* Input icons */
    .input-icon-wrapper {
      position: relative;
    }

    .input-icon-wrapper .form-control {
      padding-left: 40px;
    }

    .input-icon-wrapper .input-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #adb5bd;
      pointer-events: none;
    }

    .input-icon-wrapper .form-control:focus + .input-icon,
    .input-icon-wrapper .form-control:not(:placeholder-shown) + .input-icon {
      color: #1e88e5;
    }
  </style>

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
  <?php if (Alert::defined()): ?>
  <div id="login-alert" class="login-alert alert <?php echo (Alert::status() == ALERT_STATUS_FAIL) ? 'alert-danger' : 'alert-success' ?>">
    <?php echo Alert::get() ?>
  </div>
  <script>
    setTimeout(function() {
      document.getElementById('login-alert').style.display = 'none';
    }, <?php echo ALERT_DISAPPEAR_IN * 1000 ?>);
  </script>
  <?php endif; ?>

  <div class="login-container">
    <div class="login-card">
      <?php
      if (Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'] . '.php')) {
        include(PATH_ADMIN_VIEWS . $layout['view'] . '.php');
      }
      ?>
    </div>
  </div>

  <!-- Plugins -->
  <?php Theme::plugins('loginBodyEnd') ?>

</body>

</html>
