<h1 class="subhead">
<?php
	$parentKey = $page->parentKey();
	if ($parentKey) {
		echo $pagesByParentByKey[PARENT][$parentKey]->title();
		echo ' -> ';
	}
	echo $page->title();
?>
</h1>

<section class="page">
	<?php Theme::plugins('pageBegin') ?>

	<header class="page-header">
		<h2 class="page-title">
			<?php echo $Page->title() ?>
		</h2>
	</header>

	<div class="page-content">
		<?php echo $Page->content() ?>
	</div>

	<div class="edit-this-page">
		<?php
			echo '<a class="pure-button pure-button-primary" href="'.$GITHUB_BASE_URL.$Page->key().'/'.FILENAME.'"><i class="fa fa-pencil"></i> Edit this page</a>';
			echo '<a class="pure-button" href="'.$GITHUB_BASE_URL.$Page->key().'/'.FILENAME.'"><i class="fa fa-info-circle"></i> How to edit this page ?</a>';
		?>
	</div>

	<?php Theme::plugins('pageEnd') ?>
</section>