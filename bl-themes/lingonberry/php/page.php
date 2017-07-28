<div class="content section-inner">
	<div class="posts">
		<div class="post">
		
		<!-- Plugins Post Begin -->
		<?php Theme::plugins('pageBegin') ?>
		
			<div class="content-inner comments-allowed">	
				<div class="post-header">
				
					<?php if($Page->coverImage()) {
							echo '<div class="featured-media">';
							echo	'<a href="'.$Page->permalink().'" rel="bookmark" title="'.$Page->title().'">';
							echo		'<img class="attachment-post-image wp-post-image cover-image" src="'.$Page->coverImage().'" alt="Cover Image">';
							//echo		'<div class="media-caption-container">';
							//echo			'<p class="media-caption"></p>';
							//echo		'</div>';
							echo	'</a>';
							echo '</div> <!-- /featured-media -->';
					} ?>
		
					<h2 class="post-title"><a href="<?php echo $Page->permalink() ?>" rel="bookmark" title="<?php echo $Page->title() ?>"><?php echo $Page->title() ?></a></h2>
					
				</div> <!-- /post-header -->
				
				<div class="post-content">
					<?php echo $Page->content() ?>
				</div> <!-- /post-content -->
			</div> <!-- /post content-inner -->
			
			<div class="comments">
				<!-- Post plugins -->
				<?php Theme::plugins('pageEnd') ?>
			</div>
			
		</div> <!-- /post -->
		
		<div class="clear"></div>
		
	</div> <!-- /posts -->
</div> <!-- /content section-inner -->