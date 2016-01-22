<a href="<?php echo $Site->url() ?>"><h1 class="blog-title"><?php echo $Site->title() ?></h1></a>

<article class="post">

	<!-- Plugins Page Begin -->
	<?php Theme::plugins('pageBegin') ?>

	<!-- Page's header -->
	<header>
		<div class="title">
			<h1><a href="<?php echo $Page->permalink() ?>"><?php echo $Page->title() ?></a></h1>
			<div class="info"><?php echo $Page->description() ?></div>
		</div>
	</header>

	<!-- Cover Image -->
	<?php
		if($Page->coverImage()) {
			echo '<a href="'.$Page->permalink().'" class="image featured"><img src="'.$Page->coverImage().'" alt="Cover Image"></a>';
		}
	?>

	<!-- Post's content, the first part if has pagebrake -->
	<?php echo $Page->content() ?>

	<!-- Plugins Page End -->
	<?php Theme::plugins('pageEnd') ?>

</article>