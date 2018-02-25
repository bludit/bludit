<!-- Show each post on this page -->
<?php foreach ($pages as $Page): ?>

<article class="post">

	<!-- Show plugins, Hook: Post Begin -->
	<?php Theme::plugins('pageBegin') ?>

	<!-- Post's header -->
	<header>
		<div class="title">
			<h1><a href="<?php echo $Page->permalink() ?>"><?php echo $Page->title() ?></a></h1>
			<p><?php echo $Page->description() ?></p>
		</div>
		<div class="meta">
	                <?php
	                	// Get the user who created the post.
	                	$User = $Page->user();

	                	// Default author is the username.
	                	$author = $User->username();

	                	// If the user complete the first name or last name this will be the author.
				if( Text::isNotEmpty($User->firstName()) || Text::isNotEmpty($User->lastName()) ) {
					$author = $User->firstName().' '.$User->lastName();
				}
			?>
			<time class="published" datetime="<?php echo $Page->date() ?>"><?php echo $Page->date() ?></time>
		</div>
	</header>

	<!-- Cover Image -->
	<?php
		if($Page->coverImage()) {
			echo '<a href="'.$Page->permalink().'" class="image featured"><img src="'.$Page->coverImage().'" alt="Cover Image"></a>';
		}
	?>

	<!-- Post's content, the first part if has pagebrake -->
	<?php echo $Page->content(false) ?>

	<!-- Post's footer -->
	<footer>

		<!-- Read more button -->
	        <?php if($Page->readMore()) { ?>
		<ul class="actions">
			<li><a href="<?php echo $Page->permalink() ?>" class="button"><?php $Language->p('Read more') ?></a></li>
		</ul>
		<?php } ?>

		<!-- Post's tags -->
		<ul class="stats">
		<?php
			$tags = $Page->tags(true);

			foreach($tags as $tagKey=>$tagName) {
				echo '<li><a href="'.HTML_PATH_ROOT.$Url->filters('tag').'/'.$tagKey.'">'.$tagName.'</a></li>';
			}
		?>
		</ul>
	</footer>

	<!-- Plugins Post End -->
	<?php Theme::plugins('pageEnd') ?>

</article>

<?php endforeach; ?>

<!-- Pagination -->
<ul class="actions pagination">

	<!-- Show previus page link -->
	<?php if(Paginator::showPrev()) { ?>
		<li><a href="<?php echo Paginator::prevPageUrl() ?>" class="button big previous"><?php $Language->p('Previous Page') ?></a></li>
    <?php } ?>

	<!-- Show next page link -->
	<?php if(Paginator::showNext()) { ?>
		<li><a href="<?php echo Paginator::nextPageUrl() ?>" class="button big next"><?php $Language->p('Next Page') ?></a></li>
    <?php } ?>

</ul>
