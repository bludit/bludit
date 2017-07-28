<div class="content section-inner">
	<div class="posts">
		<div class="post">
		
		<!-- Plugins Post Begin -->
		<?php Theme::plugins('postBegin') ?>
		
			<div class="content-inner comments-allowed">	
				<div class="post-header">
				
					<?php if($Post->coverImage()) {
							echo '<div class="featured-media">';
							echo	'<a href="'.$Post->permalink().'" rel="bookmark" title="'.$Post->title().'">';
							echo		'<img class="attachment-post-image wp-post-image cover-image" src="'.$Post->coverImage().'" alt="Cover Image">';
							//echo		'<div class="media-caption-container">';
							//echo			'<p class="media-caption"></p>';
							//echo		'</div>';
							echo	'</a>';
							echo '</div> <!-- /featured-media -->';
					} ?>
		
					<h2 class="post-title"><a href="<?php echo $Post->permalink() ?>" rel="bookmark" title="<?php echo $Post->title() ?>"><?php echo $Post->title() ?></a></h2>
					
					<div class="post-meta">
					
						<span class="post-date"><a href="<?php echo $Post->permalink() ?>" title="<?php echo $Post->date() ?>"><?php echo $Post->date() ?></a></span>
						<span class="date-sep"> / </span>
						<span class="post-author"><?php
							if( Text::isNotEmpty($Post->user('firstName')) || Text::isNotEmpty($Post->user('lastName')) ) {
								echo $Post->user('firstName').' '.$Post->user('lastName');
							}
								else {
								echo $Post->user('username');
							}
						?></span>
						
					</div> <!-- /post-meta -->
				</div> <!-- /post-header -->
				
				<div class="post-content">
					<?php echo $Post->content() ?>
					
					<div class="post-cat-tags">
						<p class="post-tags">Tags: 
							<?php
								$tags = $Post->tags(true);
								
								foreach($tags as $tagKey=>$tagName) {
									echo '<a class="post-category" href="'.HTML_PATH_ROOT.$Url->filters('tag').'/'.$tagKey.'">'.$tagName.'</a>';
								}
							?>
						</p>
					</div>
				</div> <!-- /post-content -->
			</div> <!-- /post content-inner -->
			
			<div class="comments">
				<!-- Post plugins -->
				<?php Theme::plugins('postEnd') ?>
			</div>
			
		</div> <!-- /post -->
		
		<div class="clear"></div>
		
	</div> <!-- /posts -->
</div> <!-- /content section-inner -->