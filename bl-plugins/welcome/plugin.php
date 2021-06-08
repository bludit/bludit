<?php

class pluginWelcome extends Plugin {

	private $loadOnViews = array(
		'dashboard' // Load this plugin only in the Dashboard
	);

	public function dashboard()
	{
		global $L;
		global $login;

		$username = $login->username();
		$user = new User($username);
		$name = '';
		if ($user->nickname()) {
			$name = $user->nickname();
		} elseif ($user->firstName()) {
			$name = $user->firstName();
		}

		$labelGoodMorning = $L->g('good-morning');
		$labelGoodAfternoon = $L->g('good-afternoon');
		$labelGoodEvening = $L->g('good-evening');
		$labelGoodNight = $L->g('good-night');

return <<<EOF
<div class="pluginWelcome mb-4">
	<h2 id="hello-message" class="m-0 p-0"><i class="bi bi-emoji-laughing"></i>Welcome</h2>
</div>

<script>
$(document).ready(function() {
	$("#hello-message").fadeOut(1000, function() {
		var date = new Date()
		var hours = date.getHours()
		if (hours > 6 && hours < 12) {
			$(this).html('<i class="bi bi-sunrise"></i>$labelGoodMorning, $name');
		} else if (hours >= 12 && hours < 18) {
			$(this).html('<i class="bi bi-sun"></i>$labelGoodAfternoon, $name');
		} else if (hours >= 18 && hours < 22) {
			$(this).html('<i class="bi bi-sunset"></i>$labelGoodEvening, $name');
		} else {
			$(this).html('<i class="bi bi-moon-stars"></i>$labelGoodNight, $name');
		}
	}).fadeIn(1000);
});
</script>
EOF;
	}

}