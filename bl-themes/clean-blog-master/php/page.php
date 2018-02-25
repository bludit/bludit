<!-- Page Header -->
<header class="masthead" style="background-image: url('<?php echo $backgroundImage ?>')">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-10 mx-auto">
				<div class="post-heading">
					<h1><?php echo $page->title() ?></h1>
					<h2 class="subheading"><?php echo $page->description() ?></h2>
					<p class="meta"><?php echo $Language->get('Posted by').' '.$page->user('username').' - '.$page->date() ?></p>
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