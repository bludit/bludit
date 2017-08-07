<div class="content section-inner">
	<div class="posts">
		<div class="post">
		
		<!-- Plugins Post Begin -->
		<?php Theme::plugins('pageBegin') ?>
		
			<div class="content-inner comments-allowed">	
				<div class="post-header">
				
					<?php if($Page->coverImage()) {
							echo '<div class="featured-media">';
							echo 	'<img class="attachment-post-image wp-post-image cover-image" src="'.$Page->coverImage().'" alt="Cover Image">';
							echo		'<div class="media-caption-container">';
							echo			'<a href="'.$Page->permalink().'" rel="bookmark" title="'.$Page->title().'">';
							echo				'<p class="media-caption">'.$Page->description().'</p>';
							echo			'</a>';
							echo		'</div>';
							echo '</div> <!-- /featured-media -->';
					} ?>
		
					<h2 class="post-title"><a href="<?php echo $Page->permalink() ?>" rel="bookmark" title="<?php echo $Page->title() ?>"><?php echo $Page->title() ?></a></h2>
					
					<div class="post-meta">
					
						<span class="post-date"><a href="<?php echo $Page->permalink() ?>" title="<?php echo $Page->date() ?>"><?php echo $Page->date() ?></a></span>
						<span class="date-sep"> / </span>
						<span class="post-author"><?php
							if( Text::isNotEmpty($Page->user('firstName')) || Text::isNotEmpty($Page->user('lastName')) ) {
								echo $Page->user('firstName').' '.$Page->user('lastName');
							}
								else {
								echo $Page->user('username');
							}
						?></span>
						
					</div> <!-- /post-meta -->
				</div> <!-- /post-header -->
				
				<div class="post-content">
					<?php echo $Page->content() ?>
					
					<div class="post-cat-tags">
						<p class="post-tags">Tags: 
							<?php
								$tags = $Page->tags(true);
								
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
				<?php Theme::plugins('pageEnd') ?>
			</div>
			
		</div> <!-- /post -->
		
		<div class="clear"></div>
		
	</div> <!-- /posts -->
</div> <!-- /content section-inner -->