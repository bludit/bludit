<div class="sidebar-content">

<h1 class="title"><?php echo $Site->title() ?></h1>

<!-- Plugins Sidebar -->
<?php //Theme::plugins('siteSidebar') ?>

<?php

$html  = '<div class="plugin plugin-pages">';
$html .= '<div class="plugin-content">';
$html .= '<ul>';

$parents = $pagesParents[NO_PARENT_CHAR];
foreach($parents as $parent)
{
	if($parent->published())
	{
		// Print the parent
		$html .= '<li>';
		$html .= '<span class="parent">'.$parent->title().'</span>';

		// Check if the parent has children
		if(isset($pagesParents[$parent->key()]))
		{
			$children = $pagesParents[$parent->key()];

			// Print children
			$html .= '<ul>';
			foreach($children as $child)
			{
				if($child->published())
				{
					$html .= '<li>';
					$html .= '<a class="children" href="'.$child->permalink().'">'.$child->title().'</a>';
					$html .= '</li>';
				}
			}
			$html .= '</ul>';
		}

		$html .= '</li>';
	}
}

$html .= '</ul>';
$html .= '</div>';
$html .= '</div>';

echo $html;

?>

</div>