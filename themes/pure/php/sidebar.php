<div class="header">

<h1>Bludit</h1>
<h2>cms</h2>
<p class="about">Simple and fast content management system, create a site in 1 minute. Created by Diego Najar @dignajar</p>

<?php

// POSTS
echo '<div class="links">';
echo '<a class="homelink" href="'.HTML_PATH_ROOT.'">Home</a>';
echo '<span> | </span>';
echo '<a class="homelink" href="'.HTML_PATH_ROOT.'">Twitter</a>';
echo '</div>';

// PAGES



unset($pagesParents[NO_PARENT_CHAR]);
foreach($pagesParents as $parentKey=>$pageList)
{
	echo '<a class="parent" href="'.HTML_PATH_ROOT.$parentKey.'">'.$pages[$parentKey]->title().'</a>';

	echo '<ul>';
	foreach($pageList as $tmpPage)
	{
		echo '<li><a class="children" href="'.HTML_PATH_ROOT.$tmpPage->key().'">'.$tmpPage->title().'</a></li>';
	}
	echo '</ul>';
}

?>

</div>