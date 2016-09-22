<section id="one" class="tiles">
<?php
	foreach($parents as $page) {
		echo '<article>';
		echo '<header class="major">';
		echo '<h3>';
		echo '<a href="'.$page->permalink().'" class="link">'.$page->title().'</a>';
		echo '</h3>';
		echo '<p>'.$page->description().'</p>';
		echo '</header>';
		echo '</article>';
	}
?>
</section>