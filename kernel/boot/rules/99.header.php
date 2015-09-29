<?php defined('BLUDIT') or die('Bludit CMS.');

// Page not found 404
if($Url->notFound())
{
	header('HTTP/1.0 404 Not Found');
}
