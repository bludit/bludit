<div class="pure-menu pure-menu-horizontal">
<ul>
	<li class="pure-menu-item">
		<a class="pure-menu-link" href="<?php echo HTML_PATH_ROOT ?>">Home</a>
	</li>

	<!-- Foreach Page on $pages array -->
	<?php foreach($pages as $Page): ?>

		<li class="pure-menu-item">
			<a class="pure-menu-link" href="<?php echo $Page->permalink() ?>"><?php echo $Page->title() ?></a>
		</li>
	
	<?php endforeach; ?>

</ul>

<p><?php echo $Site->footer(); ?></p>
</div>
