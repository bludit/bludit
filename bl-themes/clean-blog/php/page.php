<!-- Page Header -->
<header class="masthead" style="background-image: url('img/post-bg.jpg')">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-10 mx-auto">
				<div class="post-heading">
					<h1><?php echo $page->title() ?></h1>
					<h2 class="subheading"><?php echo $page->description() ?></h2>
					<span class="meta">Posted by <a href="#">Start Bootstrap</a> on August 24, 2017</span>
				</div>
			</div>
		</div>
	</div>
</header>

<!-- Post Content -->
<article>
<div class="container">
<div class="row">
	<div class="col-lg-8 col-md-10 mx-auto">
	<?php echo $page->content($fullContent=true) ?>
	</div>
</div>
</div>
</article>

<hr>