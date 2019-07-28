<?php

// Check if the user is logged
$user = false;
if (Cookie::get('BLUDIT-KEY')) {
	$login = new Login();
	if ($login->isLogged()) {
		try {
			$user = new User($login->username());
		} catch (Exception $e) {
			$user = false;
		}
	}
}

$api = getPlugin('pluginAPI');
$apiToken = $api->getToken();

?>