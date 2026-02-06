<footer class="footer footer-modern">
    <div class="container">
        <div class="row align-items-center">
<?php if (defined('BLUDIT_PRO')): ?>
            <div class="col-12 text-center">
                <p class="m-0 text-uppercase"><?php echo $site->footer(); ?></p>
            </div>
<?php else: ?>
            <div class="col-md-6 text-center text-md-left mb-3 mb-md-0">
                <p class="m-0 text-uppercase"><?php echo $site->footer(); ?></p>
            </div>
            <div class="col-md-6 text-center text-md-right">
                <p class="m-0">
                    <img class="mini-logo mr-2" src="<?php echo DOMAIN_THEME_IMG.'favicon.png'; ?>" alt="Bludit"/>
                    Powered by <a href="https://www.bludit.com" target="_blank" rel="noopener"><strong>BLUDIT</strong></a>
                </p>
            </div>
<?php endif; ?>
        </div>
    </div>
</footer>
