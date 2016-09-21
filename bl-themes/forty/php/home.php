<section id="one" class="tiles">
<?php
	foreach($parents as $Page) {
		echo '<article>';
		echo '<header class="major">';
		echo '<h3>';
		echo '<a href="'.$Page->permalink().'" class="link">'.$Page->title().'</a>';
		echo '</h3>';
		echo '<p>'.$Page->description().'</p>';
		echo '</header>';
		echo '</article>';
	}
?>
</section>