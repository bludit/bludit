<?php
class pluginWelcome extends Plugin {

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

		echo '<div class="pluginWelcome mt-4 mb-4 pb-4 border-bottom">';
		echo '<h2 id="hello-message" class="m-0 p-0"><i class="bi bi-emoji-laughing"></i>' . $L->g('Welcome') . '</h2>';
        	echo '</div>';

return <<<EOF
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
