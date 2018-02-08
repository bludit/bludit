<nav class="col-md-2 d-none d-md-block bg-light sidebar">
	<div class="sidebar-sticky">
	<?php
		// Get all parent pages
		$parents = buildParentPages();
		foreach ($parents as $parent) {
			// Print the parent page title
			echo '<h6>'.$parent->title().'</h6>';

			// Check if the parent page has children
			if ($parent->hasChildren()) {
				// Get the list of children
				$children = $parent->children();

				echo '<ul class="nav flex-column">';
				foreach ($children as $child) {
					echo '<li class="nav-item">';
					echo '<a class="nav-link active" href="'.$child->permalink().'">'.$child->title().'</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
		}
	?>
	</div>
</nav>