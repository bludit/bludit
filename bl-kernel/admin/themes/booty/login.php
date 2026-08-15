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
  // Admin uses Bootstrap 5, while the public-facing themes still ship with Bootstrap 4.
  echo Theme::css('bootstrap5.min.css', DOMAIN_CORE_CSS);
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
      background: linear-gradient(135deg, #64748B 0%, #334155 55%, #1E293B 100%);
      padding: 20px;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
    }

    .login-card {
      background: #ffffff;
      border-radius: var(--radius-md, 8px);
      box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);
      padding: 36px;
      animation: fadeInUp 0.4s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(12px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-logo {
      text-align: center;
      margin-bottom: 24px;
    }

    .login-logo .logo-icon {
      width: 52px;
      height: 52px;
      background: var(--text-primary, #1E293B);
      border-radius: var(--radius-sm, 4px);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
      box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
    }

    .login-logo .logo-icon img {
      width: 26px;
      height: 26px;
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
      border-radius: var(--radius-sm, 4px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .login-logo h1 {
      font-size: 1.25rem;
      font-weight: var(--font-weight-semibold, 650);
      color: var(--text-primary, #1E293B);
      margin: 0;
    }

    .login-logo p {
      color: var(--text-secondary, #475569);
      font-size: 0.9rem;
      margin-top: 4px;
    }

    .login-card .form-control {
      border: 2px solid var(--border-color, #E2E8F0);
      border-radius: var(--radius-sm, 4px);
      padding: 10px 14px;
      font-size: 0.95rem;
      transition: all 0.2s ease;
      background-color: var(--bg-main, #F8FAFC);
      color: var(--text-primary, #1E293B);
    }

    .login-card .form-control:focus {
      border-color: var(--color-secondary-dark, #475569) !important;
      box-shadow: none !important;
      background-color: #fff;
    }

    .login-card .form-control::placeholder {
      color: var(--text-muted, #94A3B8);
    }

    .login-card .form-group {
      margin-bottom: 16px;
    }

    .login-card .form-group label {
      font-weight: var(--font-weight-medium, 550);
      color: var(--text-secondary, #475569);
      margin-bottom: 6px;
      font-size: 0.85rem;
    }

    .login-card .btn-login {
      background: var(--color-secondary-dark, #475569);
      border: none;
      border-radius: var(--radius-sm, 4px);
      padding: 11px 16px;
      font-size: 0.95rem;
      font-weight: var(--font-weight-semibold, 650);
      color: white;
      width: 100%;
      transition: all 0.2s ease;
      box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
    }

    .login-card .btn-login:hover {
      background: var(--text-primary, #1E293B);
      transform: translateY(-1px);
    }

    .login-card .btn-login:active {
      transform: translateY(0);
    }

    .login-card .form-check {
      display: flex;
      align-items: center;
      padding-left: 0;
      margin-bottom: 20px;
    }

    .login-card .form-check-input {
      width: 16px;
      height: 16px;
      margin: 0;
      float: none;
      flex-shrink: 0;
      border: 2px solid var(--border-light, #CBD5E1);
      border-radius: 4px;
    }

    .login-card .form-check-input:checked {
      background-color: var(--color-secondary, #64748B);
      border-color: var(--color-secondary, #64748B);
    }

    .login-card .form-check-label {
      color: var(--text-secondary, #475569);
      font-size: 0.9rem;
      margin: 0;
      padding-left: 8px;
    }

    .login-footer {
      text-align: center;
      margin-top: 22px;
      padding-top: 16px;
      border-top: 1px solid var(--border-color, #E2E8F0);
    }

    .login-footer p {
      color: var(--text-secondary, #475569);
      font-size: 0.85rem;
      margin: 0;
    }

    .login-footer a {
      color: var(--text-secondary, #475569);
      text-decoration: underline;
    }

    .login-footer a:hover {
      color: var(--text-primary, #1E293B);
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
      border-radius: var(--radius-sm, 4px);
      padding: 12px 20px;
      font-weight: var(--font-weight-medium, 550);
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
      background: var(--color-danger, #DC2626);
      color: white;
      border: none;
      box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
    }

    .login-alert.alert-success {
      background: var(--color-success, #16A34A);
      color: white;
      border: none;
      box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
    }

    /* Input icons */
    .input-icon-wrapper {
      position: relative;
    }

    .input-icon-wrapper .form-control {
      padding-left: 38px;
    }

    .input-icon-wrapper .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted, #94A3B8);
      pointer-events: none;
    }

    .input-icon-wrapper .form-control:focus + .input-icon,
    .input-icon-wrapper .form-control:not(:placeholder-shown) + .input-icon {
      color: var(--color-secondary, #64748B);
    }
  </style>

  <!-- Javascript -->
  <?php
  echo Theme::jquery();
  // Admin uses Bootstrap 5, while the public-facing themes still ship with Bootstrap 4.
  echo Theme::js('bootstrap5.bundle.min.js', DOMAIN_CORE_JS);
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
