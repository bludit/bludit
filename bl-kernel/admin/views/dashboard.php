<div class="container">
	<div class="row">
		<div class="col-7">
		1 of 2
		</div>
		<div class="col-5">

			<!-- Notifications -->
			<ul class="list-group">
			<?php
			$logs = array_slice($Syslog->db, 0, NOTIFICATIONS_AMOUNT);
			foreach ($logs as $log) {
				$phrase = $L->g($log['dictionaryKey']);
				echo '<li class="list-group-item">';
				echo $phrase;
				if (!empty($log['notes'])) {
					echo ' « <b>'.$log['notes'].'</b> »';
				}
				echo '<br><span class="notification-date">';
				echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
				echo ' - by '.$log['username'];
				echo '</span>';
				echo '</li>';
			}
			?>
			</ul>

		</div>
	</div>
</div>