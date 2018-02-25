<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include(THEME_DIR_PHP.'head.php')
?>
</head>
  <body>

    <?php
        Theme::plugins('siteBodyBegin')
    ?>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand" href="<?php echo $Site->url() ?>"><?php echo $Site->title() ?></a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
	<?php echo $Language->get('Menu') ?>
          <i class="fa fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $Site->url() ?>"><?php echo $Language->get('Home') ?></a>
	    </li>
            <?php
              foreach ($staticPages as $staticPage) {
                echo '<li class="nav-item">';
                echo '<a class="nav-link" href="'.$staticPage->permalink().'">'.$staticPage->title().'</a>';
                echo '</li>';
              }
            ?>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <?php
      if ($WHERE_AM_I=='page') {
        include(THEME_DIR_PHP.'page.php');
      } else {
        include(THEME_DIR_PHP.'home.php');
      }
    ?>

    <!-- Footer -->
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-md-10 mx-auto">
            <ul class="list-inline text-center">
            <?php
            if ($Site->twitter()) {
              echo '
              <li class="list-inline-item">
              <a href="'.$Site->twitter().'">
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
                </span>
              </a>
              </li>
              ';
            }
            if ($Site->facebook()) {
              echo '
              <li class="list-inline-item">
              <a href="'.$Site->facebook().'">
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
                </span>
              </a>
              </li>
              ';
            }
            if ($Site->github()) {
              echo '
              <li class="list-inline-item">
              <a href="'.$Site->github().'">
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-github fa-stack-1x fa-inverse"></i>
                </span>
              </a>
              </li>
              ';
            }
            if ($Site->codepen()) {
              echo '
              <li class="list-inline-item">
              <a href="'.$Site->codepen().'">
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-codepen fa-stack-1x fa-inverse"></i>
                </span>
              </a>
              </li>
              ';
            }
            if ($Site->instagram()) {
              echo '
              <li class="list-inline-item">
              <a href="'.$Site->instagram().'">
                <span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-instagram fa-stack-1x fa-inverse"></i>
                </span>
              </a>
              </li>
              ';
            }
            ?>
            </ul>
	          <p class="copyright text-muted"><?php echo $Site->footer() ?> </a></p>
          </div>
        </div>
      </div>
    </footer>

    <!-- Scripts -->
    <?php
      echo Theme::jquery(); // Jquery from Bludit Core
      echo Theme::js('vendor/popper/popper.min.js');
      echo Theme::js('vendor/bootstrap/js/bootstrap.min.js');
      echo Theme::js('js/clean-blog.min.js');
    ?>

    <?php
      Theme::plugins('siteBodyEnd')
    ?>

  </body>
</html>