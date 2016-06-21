<?php defined('BLUDIT') or die('Bludit CMS.');

class Email {

	// Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
	public static function send($args)
	{
		// Current time in unixtimestamp
		$now = time();

		// Domain
		$domainParse = parse_url(DOMAIN);

		$headers   = array();
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';
		$headers[] = 'Content-Transfer-Encoding: 8bit';

		$headers[] = 'From: =?UTF-8?B?'.base64_encode($args['fromName']).'?= <'.$args['from'].'>';
		$headers[] = 'Reply-To: '.$args['from'];
		$headers[] = 'Return-Path: '.$args['from'];
		$headers[] = 'message-id: <'.$now.'webmaster@'.$domainParse['host'].'>';
		$headers[] = 'X-Mailer: PHP/'.phpversion();

		$subject = '=?UTF-8?B?'.base64_encode($args['subject']).'?=';

		$message = '<html>
		<head>
			<meta charset="UTF-8">
			<title>BLUDIT</title>
		</head>
		<body>
		<div>
			'.$args['message'].'
		</div>
		</body>
		</html>';

		return mail($args['to'], $subject, $message, implode(PHP_EOL, $headers));
	}

}