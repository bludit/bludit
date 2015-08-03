<?php defined('BLUDIT') or die('Bludit CMS.');

class Email {

	public static function send($args)
	{
		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		$headers .= 'To: '.$args['to']."\r\n";
		$headers .= 'From: '.$args['from']."\r\n";

		$message = '<html>
		<head>
			<title>Bludit</title>
		</head>
		<body>
		<div style="margin: 0px auto; border: 1px solid #F1F1F1; padding: 10px;">
			<div style="font-size: 26px; padding: 10px; background-color: #F1F1F1;">Nibbleblog</div>
			'.$args['message'].'
		</div>
		</body>
		</html>';

		return mail($args['to'], $args['subject'], $message, $headers);
	}

}
