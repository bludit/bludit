<?php defined('BLUDIT') or die('Bludit CMS.');

class Email {

	// Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
	public static function send($args)
	{
		$now = time();

		$headers   = array();
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';

		$headers[] = 'From: '.$args['from'];
		$headers[] = 'Reply-To: '.$args['from'];
		$headers[] = 'Return-Path: '.$args['from'];
		$headers[] = 'message-id: <'.$now.'webmaster@'.DOMAIN.'>';
		$headers[] = 'X-Mailer: PHP/'.phpversion();

		$message = '<html>
		<head>
			<title>BLUDIT</title>
		</head>
		<body style="background-color: #f1f1f1;">
		<div style="margin: 0px auto; padding: 10px; font-size: 14px; width: 70%; max-width: 600px;">
			<div style="font-size: 26px;">BLUDIT</div>
			'.$args['message'].'
		</div>
		</body>
		</html>';

		return mail($args['to'], $args['subject'], $message, implode(PHP_EOL, $headers));
	}

}