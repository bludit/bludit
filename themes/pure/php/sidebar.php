<div class="header">

<h1><?php echo $Site->title() ?></h1>
<h2><?php echo $Site->slogan() ?></h2>

<?php

// Links
echo '<div class="links">';
echo '<a class="homelink" href="'.HTML_PATH_ROOT.'">Home</a>';
echo '<span> - </span>';
echo '<a class="homelink" href="'.HTML_PATH_ROOT.'">Twitter</a>';
echo '</div>';

// Pages
$parents = $pagesParents[NO_PARENT_CHAR];

foreach($parents as $parent)
{
	// Print the parent
	echo '<a class="parent" href="'.HTML_PATH_ROOT.$parent->key().'">'.$parent->title().'</a>';

	// Check if the parent hash children
	if(isset($pagesParents[$parent->key()]))
	{
		$children = $pagesParents[$parent->key()];

		// Print the children
		echo '<ul>';
		foreach($children as $child)
		{
			echo '<li><a class="children" href="'.HTML_PATH_ROOT.$child->key().'">— '.$child->title().'</a></li>';
		}
		echo '</ul>';
	}
}

?>

</div>
