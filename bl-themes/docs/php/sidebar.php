<div class="sidebar-content">
<h1 class="title"><?php echo $Site->title() ?></h1>

<?php

$html  = '<div class="plugin plugin-pages">';
$html .= '<div class="plugin-content">';
$html .= '<ul class="parent">';

foreach($pagesByParent[PARENT] as $parent) {
	$html .= '<li class="parent">';
	$html .= '<span class="parent">'.$parent->title().'</span>';

	if(!empty($pagesByParent[$parent->key()])) {
		$html .= '<ul class="child">';
		foreach($pagesByParent[$parent->key()] as $child) {
			$html .= '<li class="child">';
			$html .= '<a class="child" href="'.$child->permalink().'">';
			$html .= $child->title();
			$html .= '</a>';
			$html .= '</li>';
		}
		$html .= '</ul>';
	}
	$html .= '</li>';
}

$html .= '</ul>';
$html .= '</div>';
$html .= '</div>';

echo $html;

?>

</div>