<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		// No events for the view yet
	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<div id="dashboard" class="container-fluid">
	<div class="row">
		<div class="col-7">
			<?php execPluginsByHook('dashboard') ?>
		</div>

		<div class="col-5">

			<!-- Notifications -->
			<ul class="list-group">
			<li class="list-group-item">
				<h4 class="m-0 p-0"><i class="bi bi-bell"></i><?php $L->p('Notifications') ?></h4>
			</li>
			<?php
			$logs = array_slice($syslog->db, 0, NOTIFICATIONS_AMOUNT);
			foreach ($logs as $log) {
				echo '<li class="list-group-item">';
				echo '<div>';
				echo $L->g($log['dictionaryKey']);
				if (!empty($log['notes'])) {
					echo ' « <b>'.$log['notes'].'</b> »';
				}
				echo '</div>';
				echo '<div class="form-text">';
				echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
				echo ' [ '.$log['username'] .' ]';
				echo '</div>';
				echo '</li>';
			}
			?>
			</ul>
			<!-- End Notifications -->

		</div>
	</div>
</div>
