<nav class="navbar navbar-light bg-light d-block d-lg-none">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="#"><?php echo (defined('BLUDIT_PRO'))?'BLUDIT PRO':'BLUDIT' ?></a>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
      <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>"><?php $L->p('Dashboard') ?></a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>"><?php $L->p('New content') ?></a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>"><?php $L->p('Content') ?></a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>"><?php $L->p('Categories') ?></a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>"><?php $L->p('Users') ?></a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'settings' ?>"><?php $L->p('Settings') ?></a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>"><?php $L->p('Plugins') ?></a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>"><?php $L->p('Themes') ?></a>
      </li>
    </ul>
  </div>
</nav>
