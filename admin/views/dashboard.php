<h2 class="title"><?php $Language->p('Dashboard') ?> </h2>

<div class="units-row">

	<div class="unit-40">

		<div class="dashboardBox">
			<div class="content contentBlue">
				<div class="bigContent"><?php echo $dbPosts->count() ?></div>
				<div class="littleContent"><?php $Language->p('Posts') ?></div>
				<i class="iconContent fa fa-pie-chart"></i>
			</div>
		</div>

		<div class="dashboardBox">
			<div class="content contentGreen">
				<div class="bigContent"><?php echo $dbUsers->count() ?></div>
				<div class="littleContent"><?php $Language->p('Users') ?></div>
				<i class="iconContent fa fa-user"></i>
			</div>
		</div>

	</div>

	<div class="unit-60">
		<?php if($_newPosts || $_newPages) { ?>
		<div class="dashboardBox">
			<div class="content contentGreen">
				<div class="bigContent"><?php $Language->p('database-regenerated') ?></div>
				<div class="littleContent"><?php $Language->p('new-posts-and-pages-synchronized') ?></div>
				<i class="iconContent fa fa-pie-chart"></i>
			</div>
		</div>
		<?php } ?>
		<div class="dashboardBox">
			<h2>Drafts</h2>
			<div class="content">
				<nav class="nav">
				<ul>
				<?php
					if( empty($_draftPosts) && empty($_draftPages) )
					{
						echo '<li>'.$Language->g('There are no drafts').'</li>';
					}
					else
					{
						foreach($_draftPosts as $Post)
						{
							echo '<li>('.$Language->g('Post').') <a href="'.HTML_PATH_ADMIN_ROOT.'edit-post/'.$Post->key().'">'.($Post->title()?$Post->title():'['.$Language->g('Empty title').'] ').'</a></li>';
						}
						foreach($_draftPages as $Page)
						{
							echo '<li>('.$Language->g('Page').') <a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$Page->key().'">'.($Page->title()?$Page->title():'['.$Language->g('Empty title').'] ').'</a></li>';
						}
					}
				?>
				</ul>
				</nav>
			</div>
		</div>

	</div>

</div>