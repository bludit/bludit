<nav class="sidebar">
<?php
	// Get all parent pages
	$parents = buildParentPages();
	foreach ($parents as $parent) {
		// Print the parent page title
		echo '<h6 class="text-uppercase">'.$parent->title().'</h6>';

		// Check if the parent page has children
		if ($parent->hasChildren()) {
			// Get the list of children
			$children = $parent->children();

			echo '<ul class="nav flex-column">';
			foreach ($children as $child) {
				if ($child->key()==$url->slug()) {
					echo '<li class="nav-item-active">';
				} else {
					echo '<li class="nav-item">';
				}
				echo '<a class="nav-link" href="'.$child->permalink().'">'.$child->title().'</a>';
				echo '</li>';
			}
			echo '</ul>';
		}
	}
?>
</nav>