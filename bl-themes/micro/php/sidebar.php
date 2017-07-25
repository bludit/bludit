<h1 class="site-title">
        <a href="<?php echo $Site->url() ?>">
        <?php echo $Site->title() ?>
        </a>
</h1>

<ul class="fixed-pages">

<?php
$fixedPages = $dbPages->getFixedDB();
$keys = array_keys($fixedPages);
foreach($keys as $pageKey) {
        $page = buildPage($pageKey);
        echo '<li>';
        echo '<a href="'.$page->permalink().'">';
        echo $page->title();
        echo '</a>';
        echo '</li>';
}
?>
</ul>