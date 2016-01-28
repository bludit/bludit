<a href="<?php echo $Site->url() ?>"><h2 class="blog-title"><?php echo $Site->title() ?></h2></a>

<article class="post">

	<!-- Plugins Post Begin -->
	<?php Theme::plugins('postBegin') ?>

	<!-- Post's header -->
	<header>
		<div class="title">
			<h1><a href="<?php echo $Post->permalink() ?>"><?php echo $Post->title() ?></a></h1>
			<div class="info"><span><i class="fa fa-clock-o"></i> <?php echo $Post->date() ?></span><span><i class="fa fa-user"></i> <?php echo Text::isNotEmpty($Post->user('firstName'))?$Post->user('firstName'):$Post->user('username') ?></span></div>
		</div>
	</header>

	<div class="cover-image">
	<!-- Cover Image -->
	<?php
		if($Post->coverImage()) {
			echo '<img src="'.$Post->coverImage().'" alt="Cover Image">';
		}
	?>
	</div>

	<!-- Post's content, the first part if has pagebrake -->
	<?php echo $Post->content(true) ?>

	<!-- Post's footer -->
	<footer>
		<!-- Post's tags -->
		<ul class="stats">
		<?php
			$tags = $Post->tags(true);

			foreach($tags as $tagKey=>$tagName) {
				echo '<li><a href="'.HTML_PATH_ROOT.$Url->filters('tag').'/'.$tagKey.'">'.$tagName.'</a></li>';
			}
		?>
		</ul>
	</footer>

	<!-- Plugins Post End -->
	<?php Theme::plugins('postEnd') ?>

</article>