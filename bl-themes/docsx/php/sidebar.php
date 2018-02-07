<nav class="col-md-2 d-none d-md-block bg-light sidebar">
	<div class="sidebar-sticky">
	<?php
		// Get all pages published
		$pagesKeys = $dbPages->getPublishedDB();
		foreach ($pagesKeys as $pageKey) {
			// Build the page
			$page = buildPage($pageKey);
			// If the page is not a child this means the page is parent-page
			if (!$page->isChild()) {
				echo '<h6>'.$page->title().'</h6>';

				// Get all children of the page
				$childrenKeys = $page->children();
				// Check if the page has children
				if ($childrenKeys!==false) {
					// Foreach child
					echo '<ul class="nav flex-column">';
					foreach ($childrenKeys as $childKey) {
						// Build the child
						$pageChild = buildPage($childKey);
						echo '<li class="nav-item">';
						echo '<a class="nav-link active" href="'.$pageChild->permalink().'">'.$pageChild->title().'</a>';
						echo '</li>';
					}
					echo '</ul>';
				}
			}
		}
	?>
	</div>
</nav>