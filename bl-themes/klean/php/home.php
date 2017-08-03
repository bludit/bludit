<?php

// PRINT PAGES and SUB-PAGES
// ------------------------------------------------------

foreach ($pagesByParent[PARENT] as $Parent) {
	echo '<div class="bl-list">';
	echo '<div class="container">';
	echo '<div class="row">';
	echo '<div class="col-md-12 text-center">';

	if (PARENT_PAGES_LINK) {
		echo '<h1><a class="page-parent" href="'.$Parent->permalink().'">'.$Parent->title().'</a></h1>';
	} else {
		echo '<h1>'.$Parent->title().'</h1>';
	}

	if (!empty($pagesByParent[$Parent->key()])) {
		echo '<ul class="list-unstyled">';
		foreach($pagesByParent[$Parent->key()] as $Child) {
			echo '<li><h4><a href="'.$Child->permalink().'">'.$Child->title().'</a></h4></li>';
		}
		echo '</ul>';
	}

	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

?>