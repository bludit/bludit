<?php

/*
 * Bludit
 * https://www.bludit.com
 * Author Diego Najar
 * Bludit is opensource software licensed under the MIT license.
*/

// ============================================================================
// Requirements basic checks
// ============================================================================

// Check PHP version
if (version_compare(phpversion(), '5.6', '<')) {
	echo 'Current PHP version ' . phpversion() . ', you need > 5.6.';
	echo '';
	echo '<a href="https://docs.bludit.com/en/getting-started/requirements">Please read Bludit requirements</a>.';
	exit(1);
}

// Check PHP modules
$modulesRequired = array('mbstring', 'json', 'gd', 'dom');
$modulesRequiredExit = false;
$modulesRequiredMissing = '';
foreach ($modulesRequired as $module) {
	if (!extension_loaded($module)) {
		$errorText = 'PHP module <b>' . $module . '</b> is not installed.';
		error_log('[ERROR] ' . $errorText, 0);

		$modulesRequiredExit = true;
		$modulesRequiredMissing .= $errorText . PHP_EOL;
	}
}
if ($modulesRequiredExit) {
	echo 'PHP modules missing:';
	echo $modulesRequiredMissing;
	echo '';
	echo '<a href="https://docs.bludit.com/en/getting-started/requirements">Please read Bludit requirements</a>.';
	exit(1);
}

// ============================================================================
// Bludit constanst, variables, language and locale settings
// ============================================================================

// Bludit version
define('BLUDIT_VERSION',		'4.0.0');
define('BLUDIT_CODENAME',		'');
define('BLUDIT_RELEASE_DATE',	'2021-05-23');
define('BLUDIT_BUILD',			'20210523');

// Log
define('LOG_SEP', ' | ');
define('LOG_TYPE_INFO', '[INFO]');
define('LOG_TYPE_WARN', '[WARN]');
define('LOG_TYPE_ERROR', '[ERROR]');

// Debug mode
define('DEBUG_MODE', TRUE);
define('DEBUG_TYPE', 'INFO'); // INFO, TRACE
error_reporting(0); // Turn off all error reporting
ini_set("display_errors", 0); // Turn off display errors in browser
ini_set('display_startup_errors', 0);
if (DEBUG_MODE) {
    // Turn on all error reporting, will be display in log server
    ini_set("html_errors", 1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
}

// Security constant
define('BLUDIT', true);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// PHP paths
define('PATH_ROOT',				__DIR__ . DS);
define('PATH_CONTENT',			PATH_ROOT . 'bl-content' . DS);
define('PATH_KERNEL',			PATH_ROOT . 'bl-kernel' . DS);
define('PATH_LANGUAGES',		PATH_ROOT . 'bl-languages' . DS);
define('PATH_UPLOADS',			PATH_CONTENT . 'uploads' . DS);
define('PATH_TMP',				PATH_CONTENT . 'tmp' . DS);
define('PATH_PAGES',			PATH_CONTENT . 'pages' . DS);
define('PATH_WORKSPACES',		PATH_CONTENT . 'workspaces' . DS);
define('PATH_DATABASES',		PATH_CONTENT . 'databases' . DS);
define('PATH_PLUGINS_DATABASES', PATH_CONTENT . 'databases' . DS . 'plugins' . DS);
define('PATH_UPLOADS_PROFILES',	PATH_UPLOADS . 'profiles' . DS);
define('PATH_UPLOADS_PAGES',	PATH_UPLOADS . 'pages' . DS);
define('PATH_HELPERS',			PATH_KERNEL . 'helpers' . DS);
define('PATH_ABSTRACT',			PATH_KERNEL . 'abstract' . DS);


// Protecting against Symlink attacks
define('CHECK_SYMBOLIC_LINKS', 	TRUE);
define('FILENAME', 				'index.txt');
define('DB_DATE_FORMAT', 		'Y-m-d H:i:s');
define('CHARSET', 				'UTF-8');
define('DEFAULT_LANGUAGE_FILE', 'en.json');
define('DIR_PERMISSIONS', 		0755);
define('EXTREME_FRIENDLY_URL', TRUE);

if (!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 128);
}

// Domain and protocol
define('DOMAIN', $_SERVER['HTTP_HOST']);
if ((!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off')) ||
	(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ||
	(!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && ($_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')) ) {
	define('PROTOCOL', 'https://');
} else {
	define('PROTOCOL', 'http://');
}

// Base URL
// Change the base URL or leave it empty if you want to Bludit try to detect the base URL.
$base = '';
if (!empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_NAME']) && empty($base)) {
	$base = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_NAME']);
	$base = dirname($base);
} elseif (empty($base)) {
	$base = empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$base = dirname($base);
}

if (strpos($_SERVER['REQUEST_URI'], $base) !== 0) {
	$base = '/';
} elseif ($base != DS) {
	$base = trim($base, '/');
	$base = '/' . $base . '/';
} else {
	// Workaround for Windows Web Servers
	$base = '/';
}

define('HTML_PATH_ROOT', $base);
define('HTML_PATH_CORE_IMG', HTML_PATH_ROOT.'bl-kernel/img/');

// Set internal character encoding
mb_internal_encoding(CHARSET);

// Set HTTP output character encoding
mb_http_output(CHARSET);

// --- PHP Classes ---
include(PATH_ABSTRACT . 'dbjson.class.php');
include(PATH_HELPERS . 'sanitize.class.php');
include(PATH_HELPERS . 'valid.class.php');
include(PATH_HELPERS . 'text.class.php');
include(PATH_HELPERS . 'log.class.php');
include(PATH_HELPERS . 'date.class.php');
include(PATH_KERNEL . 'language.class.php');
include(PATH_KERNEL.'tag.class.php');

// --- LANGUAGE and LOCALE ---
// Try to detect the language from browser or headers
$languageFromHTTP = 'en';
$localeFromHTTP = 'en_US';

if (isset($_GET['language'])) {
	$languageFromHTTP = Sanitize::html($_GET['language']);
} else {
	// Try to detect the language browser
	$languageFromHTTP = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

	// Try to detect the locale
	if (function_exists('locale_accept_from_http')) {
		$localeFromHTTP = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	}
}

$finalLanguage = 'en';
$languageFiles = getLanguageList();
foreach ($languageFiles as $fname => $native) {
	if (($languageFromHTTP == $fname) || ($localeFromHTTP == $fname)) {
		$finalLanguage = $fname;
	}
}

$L = $language = new Language($finalLanguage);

// Set locale
setlocale(LC_ALL, $localeFromHTTP);

// --- TIMEZONE ---
// Check if timezone is defined in php.ini
$iniDate = ini_get('date.timezone');
if (empty($iniDate)) {
	// Timezone not defined in php.ini, then set UTC as default.
	date_default_timezone_set('UTC');
}

// ============================================================================
// FUNCTIONS
// ============================================================================

function generateTags($tags) {
    $tmp = array();
    foreach ($tags as $tag) {
        $tagKey = Text::generateSlug($tag);
        $tmp[$tagKey] = $tag;
    }
    return $tmp;
}

// Returns an array with all languages
function getLanguageList()
{
	$files = glob(PATH_LANGUAGES . '*.json');
	$tmp = array();
	foreach ($files as $file) {
		$t = new dbJSON($file, false);
		$native = $t->db['language-data']['native'];
		$locale = basename($file, '.json');
		$tmp[$locale] = $native;
	}

	return $tmp;
}

// Check if Bludit is installed
function alreadyInstalled()
{
	return file_exists(PATH_DATABASES . 'site.php');
}

// Check write permissions and .htaccess file
function checkSystem()
{
	$output = array();

	// Try to create .htaccess
	$htaccessContent = 'AddDefaultCharset UTF-8

<IfModule mod_rewrite.c>

# Enable rewrite rules
RewriteEngine on

# Base directory
RewriteBase ' . HTML_PATH_ROOT . '

# Deny direct access to the next directories
RewriteRule ^bl-content/(databases|workspaces|pages|tmp)/.*$ - [R=404,L]

# All URL process by index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*) index.php [PT,L]

</IfModule>';

	if (!file_put_contents(PATH_ROOT . '.htaccess', $htaccessContent)) {
		if (!empty($_SERVER['SERVER_SOFTWARE'])) {
			$webserver = Text::lowercase($_SERVER['SERVER_SOFTWARE']);
			if (Text::stringContains($webserver, 'apache') || Text::stringContains($webserver, 'litespeed')) {
				$errorText = 'Missing file, upload the file .htaccess to the root directory.';
				error_log('[ERROR] ' . $errorText, 0);
				array_push($output, $errorText);
			}
		}
	}

	// Check mod_rewrite module
	if (function_exists('apache_get_modules')) {
		if (!in_array('mod_rewrite', apache_get_modules())) {
			$errorText = 'Module mod_rewrite is not installed or loaded.';
			error_log('[ERROR] ' . $errorText, 0);
			array_push($output, $errorText);
		}
	}

	// Try to create the directory content
	@mkdir(PATH_CONTENT, DIR_PERMISSIONS, true);

	// Check if the directory content is writeable.
	if (!is_writable(PATH_CONTENT)) {
		$errorText = 'Writing test failure, check directory "bl-content" permissions.';
		error_log('[ERROR] ' . $errorText, 0);
		array_push($output, $errorText);
	}

	return $output;
}

// Install Bludit
function install($adminPassword, $timezone)
{
	global $L;

	if (!date_default_timezone_set($timezone)) {
		date_default_timezone_set('UTC');
	}

	$currentDate = Date::current(DB_DATE_FORMAT);

    // Load the examples pages
    if (file_exists(PATH_LANGUAGES.'installer'.DS.$L->currentLanguage().'.php')) {
        include(PATH_LANGUAGES.'installer'.DS.$L->currentLanguage().'.php');
    } else {
        include(PATH_LANGUAGES.'installer'.DS.'en.php');
    }

	// ============================================================================
	// Create directories
	// ============================================================================

	// Directories for example pages
	foreach ($examples as $page) {
		if (!mkdir(PATH_PAGES . $page['url'], DIR_PERMISSIONS, true)) {
			$errorText = 'Error when trying to created the directory=>' . PATH_PAGES . $page['url'];
			error_log('[ERROR] ' . $errorText, 0);
		}
	}

	// Directories for initial plugins
	$pluginsToInstall = array('tinymce', 'about', 'welcome', 'api', 'visits-stats', 'robots', 'canonical', 'popeye', 'latest-pages');
	foreach ($pluginsToInstall as $plugin) {
		if (!mkdir(PATH_PLUGINS_DATABASES . $plugin, DIR_PERMISSIONS, true)) {
			$errorText = 'Error when trying to created the directory=>' . PATH_PLUGINS_DATABASES . $plugin;
			error_log('[ERROR] ' . $errorText, 0);
		}
	}

	// System directories
	$systemDirectories = array(PATH_UPLOADS_PROFILES, PATH_TMP, PATH_WORKSPACES);
	foreach ($systemDirectories as $directory) {
		if (!mkdir($directory, DIR_PERMISSIONS, true)) {
			$errorText = 'Error when trying to created the directory=>' . $directory;
			error_log('[ERROR] ' . $errorText, 0);
		}
	}

	// ============================================================================
	// Create files
	// ============================================================================

	$dataHead = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>" . PHP_EOL;

	$data = array();
	$slugs = array();
	$nextDate = $currentDate;
	foreach ($examples as $page) {
		$nextDate = Date::offset($nextDate, DB_DATE_FORMAT, '-1 minute');

		$data[$page['url']] = array(
			'title' => $page['title'],
			'description' => $page['description'],
			'username' => 'admin',
			'tags' => generateTags($page['tags']),
			'type' => $page['type'],
			'date' => $nextDate,
			'dateModified' => '',
			'allowComments' => true,
			'position' => $page['position'],
			'coverImage' => '',
			'md5file' => '',
			'category' => $page['category'],
			'uuid' => md5(uniqid()),
			'parent' => '',
			'template' => '',
			'noindex' => false,
			'nofollow' => false,
			'noarchive' => false
		);

		array_push($slugs, $page['url']);
		file_put_contents(PATH_PAGES . $page['url'] . DS . FILENAME, $page['content'], LOCK_EX);

        if (!mkdir(PATH_UPLOADS_PAGES.$data[$page['url']]['uuid'], DIR_PERMISSIONS, true)) {
			$errorText = 'Error when trying to create the directory=>' . PATH_UPLOADS_PAGES . $data[$page['url']]['uuid'];
			error_log('[ERROR] ' . $errorText, 0);
        }

        if (symlink(PATH_UPLOADS_PAGES.$data[$page['url']]['uuid'], PATH_UPLOADS_PAGES.$page['url']) === false) {
            $errorText = 'Error when trying to create the symlink =>' . PATH_UPLOADS_PAGES . $page['url'];
			error_log('[ERROR] ' . $errorText, 0);
        }

	}
	file_put_contents(PATH_DATABASES . 'pages.php', $dataHead . json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File site.php

	// If Bludit is not installed inside a folder, the URL doesn't need finish with /
	// Example (root): https://domain.com
	// Example (inside a folder): https://domain.com/folder/
	if (HTML_PATH_ROOT == '/') {
		$siteUrl = PROTOCOL . DOMAIN;
	} else {
		$siteUrl = PROTOCOL . DOMAIN . HTML_PATH_ROOT;
	}
	$data = array(
		'title' => 'BLUDIT',
		'slogan' => $L->get('welcome-to-bludit'),
		'description' => $L->get('congratulations-you-have-successfully-installed-your-bludit'),
		'footer' => 'Copyright © ' . Date::current('Y'),
		'itemsPerPage' => 6,
		'language' => $L->currentLanguage(),
		'locale' => $L->locale(),
		'timezone' => $timezone,
		'theme' => 'popeye',
		'adminTheme' => 'booty',
		'homepage' => '',
		'pageNotFound' => '',
		'uriPage' => '/',
		'uriTag' => '/tag/',
		'uriCategory' => '/category/',
		'uriBlog' => '',
		'url' => $siteUrl,
		'emailFrom' => 'no-reply@' . DOMAIN,
		'orderBy' => 'date',
		'currentBuild' => '0',
		'twitter' => 'https://twitter.com/bludit',
		'facebook' => 'https://www.facebook.com/bluditcms',
		'codepen' => '',
		'github' => 'https://github.com/bludit',
		'instagram' => '',
		'gitlab' => '',
		'linkedin' => '',
		'xing' => '',
		'mastodon' => '',
		'dribbble' => '',
		'vk' => '',
		'discord' => '',
		'youtube' => '',
		'dateFormat' => 'F j, Y',
		'timeFormat' => 'g:i a',
		'extremeFriendly' => true,
		'autosaveInterval' => 2,
		'titleFormatHomepage' => '{{site-slogan}} | {{site-title}}',
		'titleFormatPages' => '{{page-title}} | {{site-title}}',
		'titleFormatCategory' => '{{category-name}} | {{site-title}}',
		'titleFormatTag' => '{{tag-name}} | {{site-title}}',
		'imageRestrict' => true,
		'imageRelativeToAbsolute' => false,
		'thumbnailSmallWidth' => 400,
		'thumbnailSmallHeight' => 400,
		'thumbnailSmallQuality' => 100,
		'logo' => '',
		'markdownParser' => true,
		'customFields' => '{}',
		'darkModeAdmin' => false
	);
	file_put_contents(PATH_DATABASES . 'site.php', $dataHead . json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File users.php
	$salt = uniqid();
	$passwordHash = sha1($adminPassword . $salt);
	$tokenAuth = md5(uniqid() . time() . DOMAIN);

	$data = array(
		'admin' => array(
			'nickname' => 'Admin',
			'firstName' => $L->get('Administrator'),
			'lastName' => '',
			'bio' => '',
			'role' => 'admin',
			'password' => $passwordHash,
			'salt' => $salt,
			'email' => '',
			'registered' => $currentDate,
			'tokenRemember' => '',
			'tokenAuth' => $tokenAuth,
			'tokenAuthTTL' => '2009-03-15 14:00',
			'twitter' => '',
			'facebook' => '',
			'instagram' => '',
			'codepen' => '',
			'linkedin' => '',
			'xing' => '',
			'github' => '',
			'gitlab' => '',
			'mastodon' => '',
			'vk' => '',
			'youtube' => '',
			'discord' => ''
		)
	);
	file_put_contents(PATH_DATABASES . 'users.php', $dataHead . json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File syslog.php
	$data = array(
		array(
			'date' => $currentDate,
			'dictionaryKey' => 'welcome-to-bludit',
			'notes' => '',
			'idExecution' => uniqid(),
			'method' => 'POST',
			'username' => 'admin'
		)
	);
	file_put_contents(PATH_DATABASES . 'syslog.php', $dataHead . json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File security.php
	$data = array(
		'minutesBlocked' => 5,
		'numberFailuresAllowed' => 10,
		'blackList' => array()
	);
	file_put_contents(PATH_DATABASES . 'security.php', $dataHead . json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File categories.php
	$data = array(
		'general' => array('name' => 'General', 'description' => '', 'template' => '', 'list' => $slugs),
		'music' => array('name' => 'Music', 'description' => '', 'template' => '', 'list' => array()),
		'videos' => array('name' => 'Videos', 'description' => '', 'template' => '', 'list' => array())
	);
	file_put_contents(PATH_DATABASES . 'categories.php', $dataHead . json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File tags.php
    $tagsIndex = array();
    foreach ($examples as $page) {
        $tags = $page['tags'];
        foreach ($tags as $tag) {
            $tagKey = Text::generateSlug($tag);
            if (isset($tagsIndex[$tagKey])) {
                array_push($tagsIndex[$tagKey]['list'], $page['url']);
            } else {
                $tagsIndex[$tagKey]['name'] = $tag;
                $tagsIndex[$tagKey]['list'] = array($page['url']);
            }
        }
    }
    #$data = array_values($tagsIndex);
	file_put_contents(PATH_DATABASES . 'tags.php', $dataHead . json_encode($tagsIndex, JSON_PRETTY_PRINT), LOCK_EX);


	// File plugins/about/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'about' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'position' => 1,
				'label' => $L->get('About'),
				'text' => $L->get('this-is-a-brief-description-of-yourself-our-your-site')
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/visits-stats/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'visits-stats' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'numberOfDays' => 7,
				'label' => $L->get('Visits'),
				'excludeAdmins' => false,
				'position' => 1
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);
	mkdir(PATH_WORKSPACES . 'visits-stats', DIR_PERMISSIONS, true);

	// File plugins/tinymce/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'tinymce' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'position' => 1,
				'toolbar1' => 'formatselect bold italic forecolor backcolor removeformat | bullist numlist table | blockquote alignleft aligncenter alignright | link unlink pagebreak image code',
				'toolbar2' => '',
				'plugins' => 'code autolink image link pagebreak advlist lists textpattern table'
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/canonical/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'canonical' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'position' => 1
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/robots/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'robots' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'position' => 1,
				'robotstxt' => 'User-agent: *' . PHP_EOL . 'Allow: /'
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/welcome/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'welcome' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'position' => 1
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/latest-pages/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'latest-pages' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'position' => 2
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/visits-stats/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'visits-stats' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'position' => 3,
				'excludeAdmins' => false,
				'label' => $L->get('Visits')
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/popeye/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'popeye' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				"googleFonts"=> false,
				"darkMode"=> true,
				"dateFormat"=> "relative",
				"showTags"=> true,
				"position"=> 1
			),
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	return true;
}

function redirect($url)
{
	if (!headers_sent()) {
		header("Location:" . $url, TRUE, 302);
		exit(0);
	}

	exit('<meta http-equiv="refresh" content="0; url="' . $url . '">');
}

// ============================================================================
// MAIN
// ============================================================================

if (alreadyInstalled()) {
	$errorText = 'Bludit is already installed ;)';
	error_log('[ERROR] ' . $errorText, 0);
	exit($errorText);
}

// Install a demo, just call the install.php?demo=true
if (isset($_GET['demo'])) {
	install('demo123', 'UTC');
	redirect(HTML_PATH_ROOT);
}

// Install by POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	install($_POST['password'], $_POST['timezone']);
	redirect(HTML_PATH_ROOT);
}

?>
<!DOCTYPE html>
<html class="h-100">

<head>
	<title><?php echo $L->get('Bludit Installer') ?></title>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="robots" content="noindex,nofollow">

	<!-- Favicon -->
	<link rel="icon" type="image/png" href="bl-kernel/img/favicon.png?version=<?php echo time() ?>">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="bl-kernel/vendors/bootstrap/bootstrap.min.css?version=<?php echo time() ?>">

	<!-- Javascript -->
	<script charset="utf-8" src="bl-kernel/vendors/jquery/jquery.min.js?version=<?php echo time() ?>"></script>
	<script charset="utf-8" src="bl-kernel/vendors/bootstrap/bootstrap.bundle.min.js?version=<?php echo time() ?>"></script>
	<script charset="utf-8" src="bl-kernel/vendors/jstimezonedetect/jstz.min.js?version=<?php echo time() ?>"></script>
</head>

<body class="h-100 bg-light">
	<div class="container h-100">
		<div class="row h-100 justify-content-center align-items-center">
			<div class="col-8 col-md-6 col-lg-4">
                <img class="mx-auto d-block w-25 mb-4" alt="logo" src="<?php echo HTML_PATH_CORE_IMG . 'logo.svg' ?>" />
				<h1 class="text-center text-uppercase mb-4"><?php echo $L->get('Bludit Installer') ?></h1>
				<?php
				$system = checkSystem();
				if (!empty($system)) {
					foreach ($system as $error) {
						echo '
							<table class="table">
								<tbody>
									<tr>
										<th>' . $error . '</th>
									</tr>
								</tbody>
							</table>
						';
					}
				} elseif (isset($_GET['language'])) {
				?>
					<p><?php echo $L->get('choose-a-password-for-the-user-admin') ?></p>

					<?php if (!empty($errorText)) : ?>
						<div class="alert alert-danger"><?php echo $errorText ?></div>
					<?php endif ?>

					<form id="jsformInstaller" method="post" action="" autocomplete="off">
						<input type="hidden" name="timezone" id="jstimezone" value="UTC">

						<div class="form-group mb-2">
							<input type="text" value="admin" class="form-control form-control-lg" id="jsusername" name="username" placeholder="Username" disabled>
						</div>

						<div class="form-group mb-0">
							<input type="password" class="form-control form-control-lg" id="jspassword" name="password" placeholder="<?php $L->p('Password') ?>">
						</div>
						<div id="jsshowPassword" style="cursor: pointer;" class="text-center pt-0 text-muted"><?php $L->p('Show password') ?></div>

						<div class="form-group mt-4">
							<button type="submit" class="btn btn-secondary btn-lg me-2 w-100" name="install"><?php $L->p('Install') ?></button>
						</div>
					</form>
				<?php
				} else {
				?>
					<form id="jsformLanguage" method="get" action="" autocomplete="off">

						<select id="jslanguage" name="language" class="form-control form-control-lg">
							<?php
							$htmlOptions = getLanguageList();
							foreach ($htmlOptions as $fname => $native) {
								echo '<option value="' . $fname . '"' . (($finalLanguage === $fname) ? ' selected="selected"' : '') . '>' . $native . '</option>';
							}
							?>
						</select>

						<div class="form-group mt-4">
							<button type="submit" class="btn btn-secondary btn-lg me-2 w-100"><?php $L->p('Next') ?></button>
						</div>
					</form>
				<?php
				}
				?>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			// Timezone
			var timezone = jstz.determine();
			$("#jstimezone").val(timezone.name());

			// Show password
			$("#jsshowPassword").on("click", function() {
				var input = document.getElementById("jspassword");

				if (input.getAttribute("type") == "text") {
					input.setAttribute("type", "password");
				} else {
					input.setAttribute("type", "text");
				}
			});

		});
	</script>

</body>

</html>
