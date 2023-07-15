<div id="dashboard" class="container">
	<div class="row">
		<div class="col-md-7">

			<!-- Good message -->
			<div>
				<h2 id="hello-message" class="pt-0">
					<?php
					$username = $login->username();
					$user = new User($username);
					$name = '';
					if ($user->nickname()) {
						$name = $user->nickname();
					} elseif ($user->firstName()) {
						$name = $user->firstName();
					}
					?>
					<span class="fa fa-hand-spock-o"></span><span><?php echo $L->g('welcome') ?></span>
				</h2>
				<script>
					$(document).ready(function() {
						$("#hello-message").fadeOut(2400, function() {
							var date = new Date()
							var hours = date.getHours()
							if (hours > 6 && hours < 12) {
								$(this).html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-morning') . ', ' . $name ?>');
							} else if (hours > 12 && hours < 18) {
								$(this).html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-afternoon') . ', ' . $name ?>');
							} else if (hours > 18 && hours < 22) {
								$(this).html('<span class="fa fa-moon-o"></span><?php echo $L->g('good-evening') . ', ' . $name ?>');
							} else {
								$(this).html('<span class="fa fa-moon-o"></span><span><?php echo $L->g('good-night') . ', ' . $name ?></span>');
							}
						}).fadeIn(1000);
					});
				</script>
			</div>

			<!-- Quick Links -->
			<div class="container pb-5" id="jsclippyContainer">

				<div class="row">
					<div class="col p-0">
						<div class="form-group">
							<select id="jsclippy" class="clippy" name="state"></select>
						</div>
					</div>
				</div>

				<script>
					$(document).ready(function() {

						var clippy = $("#jsclippy").select2({
							placeholder: "<?php $L->p('Start typing to see a list of suggestions') ?>",
							allowClear: true,
							width: "100%",
							theme: "bootstrap4",
							minimumInputLength: 2,
							dropdownParent: "#jsclippyContainer",
							language: {
								inputTooShort: function() {
									return '';
								}
							},
							ajax: {
								url: HTML_PATH_ADMIN_ROOT + "ajax/clippy",
								data: function(params) {
									var query = {
										query: params.term
									}
									return query;
								},
								processResults: function(data) {
									return data;
								}
							},
							templateResult: function(data) {
								// console.log(data);
								var html = '';
								if (data.type == 'menu') {
									html += '<a href="' + data.url + '"><div class="search-suggestion">';
									html += '<span class="fa fa-' + data.icon + '"></span>' + data.text + '</div></a>';
								} else {
									if (typeof data.id === 'undefined') {
										return '';
									}
									html += '<div class="search-suggestion">';
									html += '<div class="search-suggestion-item">' + data.text + ' <span class="badge badge-pill badge-light">' + data.type + '</span></div>';
									html += '<div class="search-suggestion-options">';
									html += '<a target="_blank" href="' + DOMAIN_PAGES + data.id + '"><?php $L->p('view') ?></a>';
									html += '<a class="ml-2" href="' + DOMAIN_ADMIN + 'edit-content/' + data.id + '"><?php $L->p('edit') ?></a>';
									html += '</div></div>';
								}

								return html;
							},
							escapeMarkup: function(markup) {
								return markup;
							}
						}).on("select2:closing", function(e) {
							e.preventDefault();
						}).on("select2:closed", function(e) {
							clippy.select2("open");
						});
						clippy.select2("open");

					});
				</script>
			</div>

			<?php Theme::plugins('dashboard') ?>
		</div>
		<div class="col-md-5">

			<!-- Notifications -->
			<ul class="list-group list-group-striped b-0">
				<li class="list-group-item pt-0">
					<h4 class="m-0"><?php $L->p('Notifications') ?></h4>
				</li>
				<?php
				$logs = array_slice($syslog->db, 0, NOTIFICATIONS_AMOUNT);
				foreach ($logs as $log) {
					$phrase = $L->g($log['dictionaryKey']);
					echo '<li class="list-group-item">';
					echo $phrase;
					if (!empty($log['notes'])) {
						echo ' « <b>' . $log['notes'] . '</b> »';
					}
					echo '<br><span class="notification-date"><small>';
					echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
					echo ' [ ' . $log['username'] . ' ]';
					echo '</small></span>';
					echo '</li>';
				}
				?>
			</ul>

		</div>
	</div>
</div>
