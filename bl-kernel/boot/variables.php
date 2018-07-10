<?php defined('BLUDIT') or die('Bludit CMS.');

/*
	Environment variables
	If you are going to change some variable from here is recommended do it before the installation
*/

// Log
define('LOG_SEP', ' | ');
define('LOG_TYPE_INFO', '[INFO]');
define('LOG_TYPE_WARN', '[WARN]');
define('LOG_TYPE_ERROR', '[ERROR]');

// Protecting against Symlink attacks
define('CHECK_SYMBOLIC_LINKS', TRUE);

// Alert status ok
define('ALERT_STATUS_OK', 0);

// Alert status fail
define('ALERT_STATUS_FAIL', 1);

// Amount of thumbnails shown on Bludit Quick images
define('THUMBNAILS_AMOUNT', 6);

// Thubmnails size
define('THUMBNAILS_WIDTH', 400);
define('THUMBNAILS_HEIGHT', 400);
define('THUMBNAILS_QUALITY', 100); // 100%

// Profile image size
define('PROFILE_IMG_WIDTH', 400);
define('PROFILE_IMG_HEIGHT', 400);
define('PROFILE_IMG_QUALITY', 100); // 100%

// Password length
define('PASSWORD_LENGTH', 6);

// Password salt length
define('SALT_LENGTH', 8);

// Page brake string
define('PAGE_BREAK', '<!-- pagebreak -->');

// Parent key for the array $pagesByParents
define('PARENT', 'BLUDIT3849abb4cb7abd24c2d8dac17b216f17');

// Items per page for admin area
define('ITEMS_PER_PAGE_ADMIN', 20);

// Cli mode, status for new pages
define('CLI_STATUS', 'published');

// Cli mode, username for new pages
define('CLI_USERNAME', 'admin');

// Remember me
define('REMEMBER_COOKIE_USERNAME', 'BLUDITREMEMBERUSERNAME');
define('REMEMBER_COOKIE_TOKEN', 'BLUDITREMEMBERTOKEN');
define('REMEMBER_COOKIE_EXPIRE_IN_DAYS', 30);

// Filename
define('FILENAME', 'index.txt');

// Database date format
define('DB_DATE_FORMAT', 'Y-m-d H:i:s');

// Database date format
define('BACKUP_DATE_FORMAT', 'Y-m-d-H-i-s');

// Sitemap date format
define('SITEMAP_DATE_FORMAT', 'Y-m-d');

// Date format for Manage Content, Manage Users
define('ADMIN_PANEL_DATE_FORMAT', 'D, j M Y, H:i');

// Date format for Dashboard schedule posts
define('SCHEDULED_DATE_FORMAT', 'D, j M Y, H:i');

// Notifications date format
define('NOTIFICATIONS_DATE_FORMAT', 'D, j M Y, H:i');

// Amount of items to show on notification panel
define('NOTIFICATIONS_AMOUNT', 10);

// Token time to live for login via email. The offset is defined by http://php.net/manual/en/datetime.modify.php
define('TOKEN_EMAIL_TTL', '+15 minutes');

// Charset, default UTF-8.
define('CHARSET', 'UTF-8');

// Permissions for new directories
define('DIR_PERMISSIONS', 0755);

// Admin URI filter to access to the admin panel
define('ADMIN_URI_FILTER', 'admin');

// Default language file, in this case is English
define('DEFAULT_LANGUAGE_FILE', 'en.json');

// Session timeout server side, gc_maxlifetime
// 3600 = 1hour
define('SESSION_GC_MAXLIFETIME', 3600);

// Session lifetime of the cookie in seconds which is sent to the browser
// The value 0 means until the browser is closed
define('SESSION_COOKIE_LIFE_TIME', 0);

// New Global Variables
$GLOBALS['BLUDIT_MEDIA_MANAGER_AMOUNT_OF_FILES'] = 5;
$GLOBALS['BLUDIT_MEDIA_MANAGER_SORT_BY_DATE'] = true;

// Thubmnails size
$GLOBALS['THUMBNAILS_WIDTH'] = 400;
$GLOBALS['THUMBNAILS_HEIGHT'] = 400;
$GLOBALS['THUMBNAILS_QUALITY'] = 100;

// Alert
$GLOBALS['ALERT_DISSAPEAR_IN'] = 3; // Seconds

