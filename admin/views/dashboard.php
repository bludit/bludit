<h2 class="title"><?php $Language->p('Dashboard') ?> </h2>

<div class="units-row">

	<div class="unit-50">

		<div class="dashboardBox">
			<h2><?php $Language->p('Start here') ?></h2>
			<div class="content">
				<ul class="menu">
					<li class="title"><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>new-post"><?php $Language->p('New post') ?></a></li>
					<li class="description"><?php $Language->p('Create a new article for your blog') ?></li>
					<li class="title"><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>new-page"><?php $Language->p('New page') ?></a></li>
					<li class="description"><?php $Language->p('Create a new page for your website') ?></li>
					<li class="title"><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>add-user"><?php $Language->p('Add a new user') ?></a></li>
					<li class="description"><?php $Language->p('Invite a friend to collaborate on your website') ?></li>
					<li class="title"><a href="<?php echo HTML_PATH_ADMIN_ROOT ?>settings#regional"><?php $Language->p('Language and timezone') ?></a></li>
					<li class="description"><?php $Language->p('Change your language and region settings') ?></li>
				</ul>
			</div>
		</div>

	</div>

	<div class="unit-50">
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
							echo '<li><span class="label label-outline label-blue smaller">'.$Language->g('Post').'</span><a href="'.HTML_PATH_ADMIN_ROOT.'edit-post/'.$Post->key().'">'.($Post->title()?$Post->title():'['.$Language->g('Empty title').'] ').'</a></li>';
						}
						foreach($_draftPages as $Page)
						{
							echo '<li><span class="label label-outline label-green smaller">'.$Language->g('Page').'</span><a href="'.HTML_PATH_ADMIN_ROOT.'edit-page/'.$Page->key().'">'.($Page->title()?$Page->title():'['.$Language->g('Empty title').'] ').'</a></li>';
						}
					}
				?>
				</ul>
				</nav>
			</div>
		</div>
	</div>
</div>