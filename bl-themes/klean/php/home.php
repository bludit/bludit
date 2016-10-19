<?php

// PRINT PAGES and SUB-PAGES
// ------------------------------------------------------

foreach($parents as $Parent) {
	echo '<div class="bl-list">';
	echo '<div class="container">';
	echo '<div class="row">';
	echo '<div class="col-md-12 text-center">';

	if(PARENT_PAGES_LINK) {
		echo '<h1><a class="page-parent" href="'.$Parent->permalink().'">'.$Parent->title().'</a></h1>';
	} else {
		echo '<h1>'.$Parent->title().'</h1>';
	}

	// Check if the parent has children
	if( isset( $pagesParents[ $Parent->key() ] ) ) {

		// Get the children of the parent
		$children = $pagesParents[ $Parent->key() ];

		echo '<ul class="list-unstyled">';

		// Foreach child
		foreach( $children as $Child ) {
			if( $Child->published() ) {
				echo '<li><h4><a href="'.$Child->permalink().'">'.$Child->title().'</a></h4></li>';
			}
		}

		echo '</ul>';
	}

	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

echo '<hr>';

foreach($posts as $Post) {
	echo '<div class="bl-list">';
	echo '<div class="container">';
	echo '<div class="row">';
	echo '<div class="col-md-12 text-center">';

	echo '<h1><a href="'.$Post->permalink().'">'.$Post->title().'</a></h1>';
	echo '<h4>Posted on '.$Post->date().'</h4>';

	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

?>