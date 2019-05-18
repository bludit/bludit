<div id="dashboard" class="container">
	<div class="row">
		<div class="col-md-7 d-none d-sm-block">

			<!-- Good message -->
			<div>
			<h2 id="hello-message" class="pt-0">
				<span class="fa fa-hand-spock-o"></span><span><?php echo $L->g('hello') ?></span>
			</h2>
			<script>
			$( document ).ready(function() {
				$("#hello-message").fadeOut(1000, function() {
					var date = new Date()
					var hours = date.getHours()
					if (hours > 6 && hours < 12) {
						$(this).html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-morning') ?>');
					} else if (hours > 12 && hours < 18) {
						$(this).html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-afternoon') ?>');
					} else if (hours > 18 && hours < 22) {
						$(this).html('<span class="fa fa-moon-o"></span><?php echo $L->g('good-evening') ?>');
					} else {
						$(this).html('<span class="fa fa-moon-o"></span><span><?php echo $L->g('good-night') ?></span>');
					}
				}).fadeIn(1000);
			});
			</script>
			</div>

			<!-- Quick Links -->
			<div class="container border-bottom pb-4">
				<h4 class="pb-3"><?php $L->p('Quick links') ?></h4>
				<div class="row">
					<div class="col">
						<a class="quick-links text-center" style="color: #4586d4" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>">
							<div class="fa fa-edit quick-links-icons"></div>
							<div><?php $L->p('New content') ?></div>
						</a>
					</div>
					<div class="col border-left border-right">
						<a class="quick-links text-center" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>">
							<div class="fa fa-tags quick-links-icons"></div>
							<div><?php $L->p('Categories') ?></div>
						</a>
					</div>
					<div class="col">
						<a class="quick-links text-center" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>">
							<div class="fa fa-users quick-links-icons"></div>
							<div><?php $L->p('Users') ?></div>
						</a>
					</div>
				</div>
			</div>
			<div class="container mt-4">
				<div class="row">
					<div class="col">
						<a class="quick-links text-center" target="_blank" href="https://docs.bludit.com">
							<div class="fa fa-compass quick-links-icons"></div>
							<div><?php $L->p('Documentation') ?></div>
						</a>
					</div>
					<div class="col border-left border-right">
						<a class="quick-links text-center" target="_blank" href="https://forum.bludit.org">
							<div class="fa fa-support quick-links-icons"></div>
							<div><?php $L->p('Forum support') ?></div>
						</a>
					</div>
					<div class="col">
						<a class="quick-links text-center" target="_blank" href="https://gitter.im/bludit/support">
							<div class="fa fa-comments quick-links-icons"></div>
							<div><?php $L->p('Chat support') ?></div>
						</a>
					</div>
				</div>
			</div>

			<?php Theme::plugins('dashboard') ?>
		</div>
		<div class="col-md-5">

			<!-- Notifications -->
			<ul class="list-group list-group-striped b-0">
			<li class="list-group-item pt-0"><h4><?php $L->p('Notifications') ?></h4></li>
			<?php
			$logs = array_slice($syslog->db, 0, NOTIFICATIONS_AMOUNT);
			foreach ($logs as $log) {
				$phrase = $L->g($log['dictionaryKey']);
				echo '<li class="list-group-item">';
				echo $phrase;
				if (!empty($log['notes'])) {
					echo ' « <b>'.$log['notes'].'</b> »';
				}
				echo '<br><span class="notification-date"><small>';
				echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
				echo ' [ '.$log['username'] .' ]';
				echo '</small></span>';
				echo '</li>';
			}
			?>
			</ul>

		</div>
	</div>
</div>
