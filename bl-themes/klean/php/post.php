<!-- Post title and description
-->
<div class="bl-container-title text-center">
<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">
	<h1 class="title"><?php echo $Post->title() ?></h1>
	<p class="description"><?php echo $Post->description() ?></p>
</div>
</div>
</div>
</div>

<!-- Post content and author
-->
<div class="bl-container-main">
<div class="container">
<div class="row">

	<div class="col-md-10 col-md-offset-1">
	<div class="row">

	<!-- Author
	-->
	<div class="col-md-3 col-md-push-9">

		<div class="bl-author text-center">
			<?php
				$User = $Post->user();
				$author = $User->username();
				if( Text::isNotEmpty($User->firstName()) || Text::isNotEmpty($User->lastName()) ) {
					$author = $User->firstName().' '.$User->lastName();
				}
			?>

			<!-- Author profile
			-->
			<img src="<?php echo $User->profilePicture() ?>" alt="">

			<!-- Author name
			-->
			<h4 class="name"><?php echo $author ?></h4>

			<!-- Social networks
			-->
			<?php
				if( Text::isNotEmpty( $User->twitter()) )
					echo '<div class="social"><a href="'.$User->twitter().'">Twitter</a></div>';

				if( Text::isNotEmpty( $User->facebook()) )
					echo '<div class="social"><a href="'.$User->facebook().'">Facebook</a></div>';

				if( Text::isNotEmpty( $User->googleplus()) )
					echo '<div class="social"><a href="'.$User->googleplus().'">Google+</a></div>';

				if( Text::isNotEmpty( $User->instagram()) )
					echo '<div class="social"><a href="'.$User->instagram().'">Instagram</a></div>';
			?>
		</div>
	</div>

	<!-- Post content
	-->
	<div class="col-md-9 col-md-pull-3">

		<!-- Load plugins
		- Hook: Post Begin
		-->
		<?php Theme::plugins('pageBegin') ?>

		<!-- Cover Image
		-->
		<?php
			if( $Post->coverImage() ) {
				echo '<div class="bl-cover-image">';
				echo '<img src="'.$Post->coverImage().'" alt="Cover Image">';
				echo '</div>';
			}
		?>

		<!-- Post content
		- The flag TRUE is to get the full content.
		- This content is Markdown parsed.
		-->
		<div class="bl-page-post-content">
			<?php echo $Post->content(true) ?>
		</div>

		<!-- Load plugins
		- Hook: Post End
		-->
		<?php Theme::plugins('pageEnd') ?>

	</div>

	</div>
	</div>

</div>
</div>
</div>