<section id="posts">
<ul>

<?php foreach ($posts as $Post): ?>

<li><?php echo '<a href="'.$Post->permalink().'">'.$Post->title().'</a>' ?></li>

<?php endforeach; ?>

</ul>
</section>