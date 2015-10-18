<?php defined('BLUDIT') or die('Bludit CMS.');

class Email {

	// Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
	public static function send($args)
	{
		$headers   = array();
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/plain; charset=utf-8';
		$headers[] = 'From: '.$args['from'];
		$headers[] = 'X-Mailer: PHP/'.phpversion();

		$message = '<html>
		<head>
			<title>Bludit</title>
		</head>
		<body>
		<div style="margin: 0px auto; border: 1px solid #2672ec; padding: 10px;">
			<div style="font-size: 26px; padding: 10px; background-color: #2672ec;">Bludit</div>
			<p>'.$args['message'].'</p>
		</div>
		</body>
		</html>';

		return mail($args['to'], $args['subject'], $message, implode(PHP_EOL, $headers));
	}

}