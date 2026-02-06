<?php defined('BLUDIT') or die('Bludit CMS.');

echo 'var HTML_PATH_ROOT = "'.HTML_PATH_ROOT.'";'.PHP_EOL;
echo 'var HTML_PATH_ADMIN_ROOT = "'.HTML_PATH_ADMIN_ROOT.'";'.PHP_EOL;
echo 'var HTML_PATH_ADMIN_THEME = "'.HTML_PATH_ADMIN_THEME.'";'.PHP_EOL;
echo 'var HTML_PATH_CORE_IMG = "'.HTML_PATH_CORE_IMG.'";'.PHP_EOL;
echo 'var HTML_PATH_UPLOADS = "'.HTML_PATH_UPLOADS.'";'.PHP_EOL;
echo 'var HTML_PATH_UPLOADS_THUMBNAILS = "'.HTML_PATH_UPLOADS_THUMBNAILS.'";'.PHP_EOL;
echo 'var BLUDIT_VERSION = "'.BLUDIT_VERSION.'";'.PHP_EOL;
echo 'var BLUDIT_BUILD = "'.BLUDIT_BUILD.'";'.PHP_EOL;
echo 'var DOMAIN = "'.DOMAIN.'";'.PHP_EOL;
echo 'var DOMAIN_BASE = "'.DOMAIN_BASE.'";'.PHP_EOL;
echo 'var DOMAIN_PAGES = "'.DOMAIN_PAGES.'";'.PHP_EOL;
echo 'var DOMAIN_ADMIN = "'.DOMAIN_ADMIN.'";'.PHP_EOL;
echo 'var DOMAIN_CONTENT = "'.DOMAIN_CONTENT.'";'.PHP_EOL;
echo 'var DOMAIN_UPLOADS = "'.DOMAIN_UPLOADS.'";'.PHP_EOL;
echo 'var DB_DATE_FORMAT = "'.DB_DATE_FORMAT.'";'.PHP_EOL;
echo 'var AUTOSAVE_INTERVAL = "'.AUTOSAVE_INTERVAL.'";'.PHP_EOL;
echo 'var PAGE_BREAK = "'.PAGE_BREAK.'";'.PHP_EOL;
echo 'var tokenCSRF = "'.$security->getTokenCSRF().'";'.PHP_EOL;
echo 'var UPLOAD_MAX_FILESIZE = '.Text::toBytes( ini_get('upload_max_filesize') ).';'.PHP_EOL;

?>