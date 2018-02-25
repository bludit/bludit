<div class="footer section">
	<div class="footer-inner section-inner">
		<div class="footer-a widgets">
			<div class="widget widget_recent_entries">
				<div class="widget-content">
					<h3 class="widget-title">Recent Posts</h3>
						<ul>
							<?php
								$recentPosts = $dbPages->getList(1, 5, true);
								foreach ($recentPosts as $pageKey) {
									$page = buildPage($pageKey);
									echo '<li><a href="'.$page->permalink().'">'.$page->title().'</a></li>';
								}
							?>
						</ul>
				</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>

		<div class="footer-b widgets">
			<div class="widget widget_recent_entries">
				<div class="widget-content">
					<h3 class="widget-title">Tags</h3>
						<ul>
							<?php
								$db = $dbTags->db;
								$filter = $Url->filters('tag');

								$tagArray = array();

								foreach($db as $tagKey=>$fields) {
									$tagArray[] = array('tagKey'=>$tagKey, 'name'=>$fields['name']);
								}

								$tagArray = array_slice($tagArray, 0, 5);

								usort($tagArray, function($a, $b) {
									return strcmp($a['tagKey'], $b['tagKey']);
								});

								foreach($tagArray as $tagKey=>$fields) {
									echo '<li><a href="'.HTML_PATH_ROOT.$filter.'/'.$fields['tagKey'].'">'.$fields['name'].'</a></li>';
								}
							?>
						</ul>
				</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>

		<div class="footer-c widgets">
			<div class="widget-content">
					<h3 class="widget-title">Feed</h3>
						<ul>
							<li><a href="<?php echo Theme::sitemapUrl() ?>">Sitemap</a></li>
							<li><a href="<?php echo Theme::rssUrl()?>">RSS Feed</a></li>
						</ul>
				</div>
			<div class="clear"></div>
		</div>

		<div class="clear"></div>
	</div> <!-- /footer-inner -->
</div> <!-- /footer -->