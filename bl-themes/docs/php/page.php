<h1 class="subhead">
<?php
	echo $pagesByParent[PARENT][$page->parentKey()]->title();
	echo ' -> ';
	echo $page->title();
?>
</h1>

<section class="page">
	<?php Theme::plugins('pageBegin') ?>

	<header class="page-header">
		<h2 class="page-title">
			<a href="<?php echo $Page->permalink() ?>"><?php echo $Page->title() ?></a>
		</h2>
	</header>

	<div class="page-content">
		<?php echo $Page->content() ?>
	</div>

	<div class="edit-this-page">
		<?php
			echo '<a href="'.$GITHUB_BASE_URL.$Page->key().'/'.FILENAME.'">Edit this page</a>';
		?>
	</div>

	<?php Theme::plugins('pageEnd') ?>
</section>