<!-- Page Header -->
<header class="masthead" style="background-image: url('<?php echo $backgroundImage ?>')">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-10 mx-auto">
				<div class="site-heading">
					<h1><?php echo $Site->title() ?></h1>
					<span class="subheading"><?php echo $Site->description() ?></span>
				</div>
			</div>
		</div>
	</div>
</header>

<!-- Main Content -->
<div class="container">
	<div class="row">
		<div class="col-lg-8 col-md-10 mx-auto">

			<!-- Content -->
			<?php
				foreach ($content as $page):
			?>

				<div class="post-preview">
					<a href="<?php echo $page->permalink() ?>">
						<h2 class="post-title"><?php echo $page->title() ?></h2>
						<h3 class="post-subtitle"><?php echo $page->description() ?></h3>
					</a>
					<p class="post-meta"><?php echo $Language->get('Posted by').' '.$page->user('username').' - '.$page->date() ?></p>
				</div>
				<hr>

			<?php
				endforeach
			?>

			<!-- Pager -->
			<div class="clearfix">
			<?php
				if(Paginator::showPrev()) {
					echo '<a class="btn btn-secondary float-left" href="'.Paginator::prevPageUrl().'">&larr; '.$Language->get('Previuos page').'</a>';
				}

				if(Paginator::showNext()) {
					echo '<a class="btn btn-secondary float-right" href="'.Paginator::nextPageUrl().'">'.$Language->get('Next page').' &rarr;</a>';
				}
			?>
			</div>

		</div>
	</div>
</div>

<hr>