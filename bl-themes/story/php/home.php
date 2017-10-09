<!-- First page -->
<?php $firstPage = array_shift($content) ?>

<section class="banner style1 orient-left content-align-left image-position-right fullscreen onload-image-fade-in onload-content-fade-right">
	<div class="content">
		<h2><?php echo $firstPage->title() ?></h2>
		<p><?php echo $firstPage->content() ?></p>
	</div>
	<div class="image">
		<img src="<?php echo $firstPage->coverImage() ?>" alt="" />
	</div>
</section>

<?php
$left = true;
foreach ($content as $page):
	if ($left) {
		$class = "spotlight style1 orient-right content-align-left image-position-center onscroll-image-fade-in";
	} else {
		$class = "spotlight style1 orient-left content-align-left image-position-center onscroll-image-fade-in";
	}
?>

	<section class="<?php echo $class ?>">
		<div class="content">
			<h2><?php echo $page->title() ?></h2>
			<p><?php echo $page->content() ?></p>
		</div>
		<div class="image">
			<img src="<?php echo $page->coverImage() ?>" alt="" />
		</div>
	</section>

<?php
$left = !$left;
endforeach
?>