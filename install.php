<?php

/*
 * Bludit
 * https://www.bludit.com
 * Author Diego Najar
 * Bludit is opensource software licensed under the MIT license.
*/

// Check PHP version
if (version_compare(phpversion(), '5.6', '<')) {
	$errorText = 'Current PHP version ' . phpversion() . ', you need > 5.6.';
	error_log('[ERROR] ' . $errorText, 0);
	exit($errorText);
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
	exit(0);
}

// Security constant
define('BLUDIT', true);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// PHP paths
define('PATH_ROOT',		__DIR__ . DS);
define('PATH_CONTENT',		PATH_ROOT . 'bl-content' . DS);
define('PATH_KERNEL',		PATH_ROOT . 'bl-kernel' . DS);
define('PATH_LANGUAGES',	PATH_ROOT . 'bl-languages' . DS);
define('PATH_UPLOADS',		PATH_CONTENT . 'uploads' . DS);
define('PATH_TMP',		PATH_CONTENT . 'tmp' . DS);
define('PATH_PAGES',		PATH_CONTENT . 'pages' . DS);
define('PATH_WORKSPACES',	PATH_CONTENT . 'workspaces' . DS);
define('PATH_DATABASES',	PATH_CONTENT . 'databases' . DS);
define('PATH_PLUGINS_DATABASES', PATH_CONTENT . 'databases' . DS . 'plugins' . DS);
define('PATH_UPLOADS_PROFILES',	PATH_UPLOADS . 'profiles' . DS);
define('PATH_UPLOADS_THUMBNAILS', PATH_UPLOADS . 'thumbnails' . DS);
define('PATH_UPLOADS_PAGES',	PATH_UPLOADS . 'pages' . DS);
define('PATH_HELPERS',		PATH_KERNEL . 'helpers' . DS);
define('PATH_ABSTRACT',		PATH_KERNEL . 'abstract' . DS);

// Protecting against Symlink attacks
define('CHECK_SYMBOLIC_LINKS', TRUE);

// Filename for pages
define('FILENAME', 'index.txt');

// Domain and protocol
define('DOMAIN', $_SERVER['HTTP_HOST']);

if (!empty($_SERVER['HTTPS'])) {
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

// Log separator
define('LOG_SEP', ' | ');

// JSON
if (!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 128);
}

// Database format date
define('DB_DATE_FORMAT', 'Y-m-d H:i:s');

// Charset, default UTF-8.
define('CHARSET', 'UTF-8');

// Default language file
define('DEFAULT_LANGUAGE_FILE', 'en.json');

// Set internal character encoding
mb_internal_encoding(CHARSET);

// Set HTTP output character encoding
mb_http_output(CHARSET);

// Directory permissions
define('DIR_PERMISSIONS', 0755);

// --- PHP Classes ---
include(PATH_ABSTRACT . 'dbjson.class.php');
include(PATH_HELPERS . 'sanitize.class.php');
include(PATH_HELPERS . 'valid.class.php');
include(PATH_HELPERS . 'text.class.php');
include(PATH_HELPERS . 'log.class.php');
include(PATH_HELPERS . 'date.class.php');
include(PATH_KERNEL . 'language.class.php');

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
				$errorText = 'Missing file, upload the file .htaccess';
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

	// ============================================================================
	// Create directories
	// ============================================================================

	// Directories for initial pages
	$pagesToInstall = array('example-page-1-slug', 'example-page-2-slug', 'example-page-3-slug', 'example-page-4-slug');
	foreach ($pagesToInstall as $page) {
		if (!mkdir(PATH_PAGES . $L->get($page), DIR_PERMISSIONS, true)) {
			$errorText = 'Error when trying to created the directory=>' . PATH_PAGES . $L->get($page);
			error_log('[ERROR] ' . $errorText, 0);
		}
	}

	// Directories for initial plugins
	$pluginsToInstall = array('tinymce', 'about', 'visits-stats', 'robots', 'canonical', 'alternative');
	foreach ($pluginsToInstall as $plugin) {
		if (!mkdir(PATH_PLUGINS_DATABASES . $plugin, DIR_PERMISSIONS, true)) {
			$errorText = 'Error when trying to created the directory=>' . PATH_PLUGINS_DATABASES . $plugin;
			error_log('[ERROR] ' . $errorText, 0);
		}
	}

	// Directories for upload files
	if (!mkdir(PATH_UPLOADS_PROFILES, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>' . PATH_UPLOADS_PROFILES;
		error_log('[ERROR] ' . $errorText, 0);
	}

	if (!mkdir(PATH_UPLOADS_THUMBNAILS, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>' . PATH_UPLOADS_THUMBNAILS;
		error_log('[ERROR] ' . $errorText, 0);
	}

	if (!mkdir(PATH_TMP, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>' . PATH_TMP;
		error_log('[ERROR] ' . $errorText, 0);
	}

	if (!mkdir(PATH_WORKSPACES, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>' . PATH_WORKSPACES;
		error_log('[ERROR] ' . $errorText, 0);
	}

	if (!mkdir(PATH_UPLOADS_PAGES, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>' . PATH_UPLOADS_PAGES;
		error_log('[ERROR] ' . $errorText, 0);
	}

	// ============================================================================
	// Create files
	// ============================================================================

	$dataHead = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>" . PHP_EOL;

	$data = array();
	$slugs = array();
	$nextDate = $currentDate;
	foreach ($pagesToInstall as $page) {

		$slug = $page;
		$title = Text::replace('slug', 'title', $slug);
		$content = Text::replace('slug', 'content', $slug);
		$nextDate = Date::offset($nextDate, DB_DATE_FORMAT, '-1 minute');

		$data[$L->get($slug)] = array(
			'title' => $L->get($title),
			'description' => '',
			'username' => 'admin',
			'tags' => array(),
			'type' => (($slug == 'example-page-4-slug') ? 'static' : 'published'),
			'date' => $nextDate,
			'dateModified' => '',
			'allowComments' => true,
			'position' => 1,
			'coverImage' => '',
			'md5file' => '',
			'category' => 'general',
			'uuid' => md5(uniqid()),
			'parent' => '',
			'template' => '',
			'noindex' => false,
			'nofollow' => false,
			'noarchive' => false
		);

		array_push($slugs, $slug);

		file_put_contents(PATH_PAGES . $L->get($slug) . DS . FILENAME, $L->get($content), LOCK_EX);
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
		'theme' => 'alternative',
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
		'dateFormat' => 'F j, Y',
		'extremeFriendly' => true,
		'autosaveInterval' => 2,
		'titleFormatHomepage' => '{{site-slogan}} | {{site-title}}',
		'titleFormatPages' => '{{page-title}} | {{site-title}}',
		'titleFormatCategory' => '{{category-name}} | {{site-title}}',
		'titleFormatTag' => '{{tag-name}} | {{site-title}}',
		'imageRestrict' => true,
		'imageRelativeToAbsolute' => false
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
			'gitlab' => ''
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
	$data = array();
	file_put_contents(PATH_DATABASES . 'tags.php', $dataHead . json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

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

	// File plugins/alternative/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'alternative' . DS . 'db.php',
		$dataHead . json_encode(
			array(
				'googleFonts' => false,
				'showPostInformation' => false,
				'dateFormat' => 'relative',
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

	return true;
}

function redirect($url)
{
	if (!headers_sent()) {
		header("Location:" . $url, TRUE, 302);
		exit;
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
	if (Text::length($_POST['password']) < 6) {
		$errorText = $L->g('password-must-be-at-least-6-characters-long');
		error_log('[ERROR] ' . $errorText, 0);
	} else {
		install($_POST['password'], $_POST['timezone']);
		redirect(HTML_PATH_ROOT);
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<title><?php echo $L->get('Bludit Installer') ?></title>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="robots" content="noindex,nofollow">

	<!-- Favicon -->
	<link rel="icon" type="image/png" href="bl-kernel/img/favicon.png?version=<?php echo time() ?>">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="bl-kernel/css/bootstrap.min.css?version=<?php echo time() ?>">
	<link rel="stylesheet" type="text/css" href="bl-kernel/admin/themes/booty/css/bludit.css?version=<?php echo time() ?>">

	<!-- Javascript -->
	<script charset="utf-8" src="bl-kernel/js/jquery.min.js?version=<?php echo time() ?>"></script>
	<script charset="utf-8" src="bl-kernel/js/bootstrap.bundle.min.js?version=<?php echo time() ?>"></script>
	<script charset="utf-8" src="bl-kernel/js/jstz.min.js?version=<?php echo time() ?>"></script>
</head>

<body class="login">
	<div class="container">
		<div class="row justify-content-md-center pt-5">
			<div class="col-md-4 pt-5">
				<h1 class="text-center mb-5 mt-5 font-weight-normal text-uppercase" style="color: #555;"><?php echo $L->get('Bludit Installer') ?></h1>
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

						<div class="form-group">
							<input type="text" value="admin" class="form-control form-control-lg" id="jsusername" name="username" placeholder="Username" disabled>
						</div>

						<div class="form-group mb-0">
							<input type="password" class="form-control form-control-lg" id="jspassword" name="password" placeholder="<?php $L->p('Password') ?>">
						</div>

						<div class="form-check mt-2">
							<input role="button" class="form-check-input" type="checkbox" value="" id="jsshowPassword">
							<label class="form-check-label" for="jsshowPassword"><?php $L->p('Show password') ?></label>
						</div>

						<div class="form-group mt-4">
							<button type="submit" class="btn btn-primary btn-lg mr-2 w-100" name="install"><?php $L->p('Install') ?></button>
						</div>
					</form>
				<?php
				} else {
				?>
					<form id="jsformLanguage" method="get" action="" autocomplete="off">
						<label for="jslanguage"><?php echo $L->get('Choose your language') ?></label>
						<select id="jslanguage" name="language" class="form-control form-control-lg">
							<?php
							$htmlOptions = getLanguageList();
							foreach ($htmlOptions as $fname => $native) {
								echo '<option value="' . $fname . '"' . (($finalLanguage === $fname) ? ' selected="selected"' : '') . '>' . $native . '</option>';
							}
							?>
						</select>

						<div class="form-group mt-4">
							<button type="submit" class="btn btn-primary btn-lg mr-2 w-100"><?php $L->p('Next') ?></button>
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

				if (!$(this).is(':checked')) {
					input.setAttribute("type", "password");
				} else {
					input.setAttribute("type", "text");
				}
			});

		});
	</script>

</body>

</html>
