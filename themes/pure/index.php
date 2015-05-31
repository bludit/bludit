<!doctype html>
<html lang="<?php echo $Site->language() ?>">
<head>

<!-- Meta tags -->
<?php include('php/head.php') ?>

</head>
<body>

<!-- Plugins -->
<?php Theme::plugins('onSiteBody') ?>

<!-- Layout -->
<div id="layout" class="pure-g">

    <!-- Sidebar -->
    <div class="sidebar pure-u-1 pure-u-md-1-4">
        <?php include('php/sidebar.php') ?>
    </div>

    <!-- Main -->
    <div id="content" class="content pure-u-1 pure-u-md-3-4">

        <!-- Content -->
        <?php
            if($Url->whereAmI()=='home')
            {
                include('php/home.php');
            }
            elseif($Url->whereAmI()=='post')
            {
                include('php/post.php');
            }
            elseif($Url->whereAmI()=='page')
            {
                include('php/page.php');
            }
        ?>

        <!-- Footer -->
        <div class="footer">
            <?php include('php/footer.php') ?>
<?php
echo "DEBUG: Load time: ".(microtime(true) - $loadTime).'<br>';
?>
        </div>

    </div>

</div>

</body>
</html>
