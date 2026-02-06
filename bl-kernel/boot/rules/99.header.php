<?php defined('BLUDIT') or die('Bludit CMS.');

header('HTTP/1.0 '.$url->httpCode().' '.$url->httpMessage());
header('X-Powered-By: Bludit');
