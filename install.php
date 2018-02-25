<?php

/*
 * Bludit
 * https://www.bludit.com
 * Author Diego Najar
 * Bludit is opensource software licensed under the MIT license.
*/

// Check PHP version
if (version_compare(phpversion(), '5.3', '<')) {
	exit('Current PHP version '.phpversion().', you need > 5.3. (ERR_202)');
}

// Check PHP modules
if (!extension_loaded('mbstring')) {
	exit('PHP module mbstring is not installed. <a href="https://docs.bludit.com/en/getting-started/requirements">Check the requirements</a>.');
}

if (!extension_loaded('json')) {
	exit('PHP module json is not installed. <a href="https://docs.bludit.com/en/getting-started/requirements">Check the requirements</a>.');
}

if (!extension_loaded('gd')) {
	exit('PHP module gd is not installed. <a href="https://docs.bludit.com/en/getting-started/requirements">Check the requirements</a>.');
}

if (!extension_loaded('dom')) {
	exit('PHP module dom is not installed. <a href="https://docs.bludit.com/en/getting-started/requirements">Check the requirements</a>.');
}

// Security constant
define('BLUDIT', true);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// PHP paths
define('PATH_ROOT',		__DIR__.DS);
define('PATH_CONTENT',		PATH_ROOT.'bl-content'.DS);
define('PATH_KERNEL',		PATH_ROOT.'bl-kernel'.DS);
define('PATH_LANGUAGES',	PATH_ROOT.'bl-languages'.DS);
define('PATH_UPLOADS',		PATH_CONTENT.'uploads'.DS);
define('PATH_TMP',		PATH_CONTENT.'tmp'.DS);
define('PATH_PAGES',		PATH_CONTENT.'pages'.DS);
define('PATH_DATABASES',	PATH_CONTENT.'databases'.DS);
define('PATH_PLUGINS_DATABASES',PATH_CONTENT.'databases'.DS.'plugins'.DS);
define('PATH_UPLOADS_PROFILES',	PATH_UPLOADS.'profiles'.DS);
define('PATH_UPLOADS_THUMBNAILS',PATH_UPLOADS.'thumbnails'.DS);
define('PATH_HELPERS',		PATH_KERNEL.'helpers'.DS);
define('PATH_ABSTRACT',		PATH_KERNEL.'abstract'.DS);

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
	$base = empty( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$base = dirname($base);
}

if (strpos($_SERVER['REQUEST_URI'], $base)!==0) {
	$base = '/';
} elseif ($base!=DS) {
	$base = trim($base, '/');
	$base = '/'.$base.'/';
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

// --- PHP Classes ---

include(PATH_ABSTRACT.'dbjson.class.php');
include(PATH_HELPERS.'sanitize.class.php');
include(PATH_HELPERS.'valid.class.php');
include(PATH_HELPERS.'text.class.php');
include(PATH_HELPERS.'log.class.php');
include(PATH_HELPERS.'date.class.php');
include(PATH_KERNEL.'dblanguage.class.php');

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
foreach ($languageFiles as $fname=>$native) {
	if ( ($languageFromHTTP==$fname) || ($localeFromHTTP==$fname) ) {
		$finalLanguage = $fname;
	}
}

$Language = new dbLanguage($finalLanguage);

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
function getLanguageList() {
	$files = glob(PATH_LANGUAGES.'*.json');
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
function alreadyInstalled() {
	return file_exists(PATH_DATABASES.'site.php');
}

// Check write permissions and .htaccess file
function checkSystem()
{
	$stdOut = array();
	$dirpermissions = 0755;

	// Check .htaccess file for different webservers
	if (!file_exists(PATH_ROOT.'.htaccess')) {

		if (	!isset($_SERVER['SERVER_SOFTWARE']) ||
			stripos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false ||
			stripos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false
		) {
			$errorText = 'Missing file, upload the file .htaccess  (ERROR_204)';
			error_log($errorText, 0);

			$tmp['title'] = 'File .htaccess';
			$tmp['errorText'] = $errorText;
			array_push($stdOut, $tmp);
		}
	}

	// Try to create the directory content
	@mkdir(PATH_CONTENT, $dirpermissions, true);

	// Check if the directory content is writeable.
	if (!is_writable(PATH_CONTENT)) {
		$errorText = 'Writing test failure, check directory content permissions. (ERROR_205)';
		error_log($errorText, 0);

		$tmp['title'] = 'PHP permissions';
		$tmp['errorText'] = $errorText;
		array_push($stdOut, $tmp);
	}

	return $stdOut;
}

// Installation function
function install($adminPassword, $email, $timezone)
{
	global $Language;

	$stdOut = array();

	if( !date_default_timezone_set($timezone) ) {
		date_default_timezone_set('UTC');
	}

	$currentDate = Date::current(DB_DATE_FORMAT);

	// ============================================================================
	// Create directories
	// ============================================================================

	// 7=read,write,execute | 5=read,execute
	$dirpermissions = 0755;

	// PAGES
	if (!mkdir(PATH_PAGES.'welcome', $dirpermissions, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_PAGES.'welcome';
		error_log($errorText, 0);
	}

	if (!mkdir(PATH_PAGES.'about', $dirpermissions, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_PAGES.'about';
		error_log($errorText, 0);
	}

	// PLUGINS
	if (!mkdir(PATH_PLUGINS_DATABASES.'simplemde', $dirpermissions, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES.'simplemde';
		error_log($errorText, 0);
	}

	if (!mkdir(PATH_PLUGINS_DATABASES.'tags', $dirpermissions, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES.'tags';
		error_log($errorText, 0);
	}

	if (!mkdir(PATH_PLUGINS_DATABASES.'about', $dirpermissions, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES.'about';
		error_log($errorText, 0);
	}

	// UPLOADS directories
	if (!mkdir(PATH_UPLOADS_PROFILES, $dirpermissions, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_UPLOADS_PROFILES;
		error_log($errorText, 0);
	}

	if (!mkdir(PATH_UPLOADS_THUMBNAILS, $dirpermissions, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_UPLOADS_THUMBNAILS;
		error_log($errorText, 0);
	}

	if (!mkdir(PATH_TMP, $dirpermissions, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_TMP;
		error_log($errorText, 0);
	}

	// ============================================================================
	// Create files
	// ============================================================================

	$dataHead = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>".PHP_EOL;

	// File pages.php
	$data = array(
		'about'=>array(
			'description'=>$Language->get('About your site or yourself'),
			'username'=>'admin',
			'tags'=>array(),
			'status'=>'static',
			'type'=>'page',
			'date'=>$currentDate,
			'dateModified'=>'',
			'allowComments'=>true,
			'position'=>2,
			'coverImage'=>'',
			'md5file'=>'',
			'category'=>'',
			'uuid'=>md5(uniqid()),
			'parent'=>'',
			'slug'=>'about'
	    	),
		'welcome'=>array(
			'description'=>$Language->get('Welcome to Bludit'),
			'username'=>'admin',
			'tags'=>array('bludit'=>'Bludit','cms'=>'CMS','flat-files'=>'Flat files'),
			'status'=>'published',
			'type'=>'post',
			'date'=>$currentDate,
			'dateModified'=>'',
			'allowComments'=>true,
			'position'=>1,
			'coverImage'=>'',
			'md5file'=>'',
			'category'=>'general',
			'uuid'=>md5(uniqid()),
			'parent'=>'',
			'slug'=>'welcome'
	    	)
	);

	file_put_contents(PATH_DATABASES.'pages.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File site.php

	// If the website is not installed inside a folder the URL not need finish with /
	// Example (root): https://domain.com
	// Example (inside a folder): https://domain.com/folder/
	if (HTML_PATH_ROOT=='/') {
		$siteUrl = PROTOCOL.DOMAIN;
	} else {
		$siteUrl = PROTOCOL.DOMAIN.HTML_PATH_ROOT;
	}
	$data = array(
		'title'=>'BLUDIT',
		'slogan'=>'CMS',
		'description'=>'',
		'footer'=>'Copyright © '.Date::current('Y'),
		'itemsPerPage'=>6,
		'language'=>$Language->currentLanguage(),
		'locale'=>$Language->locale(),
		'timezone'=>$timezone,
		'theme'=>'kernel-panic',
		'adminTheme'=>'default',
		'homepage'=>'',
		'pageNotFound'=>'',
		'uriPage'=>'/',
		'uriTag'=>'/tag/',
		'uriCategory'=>'/category/',
		'uriBlog'=>'/blog/',
		'url'=>$siteUrl,
		'emailFrom'=>'no-reply@'.DOMAIN,
		'orderBy'=>'date'
	);

	file_put_contents(PATH_DATABASES.'site.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File users.php
	$salt = uniqid();
	$passwordHash = sha1($adminPassword.$salt);
	$tokenAuth = md5( uniqid().time().DOMAIN );

	$data = array(
		'admin'=>array(
			'firstName'=>$Language->get('Administrator'),
			'lastName'=>'',
			'role'=>'admin',
			'password'=>$passwordHash,
			'salt'=>$salt,
			'email'=>$email,
			'registered'=>$currentDate,
			'tokenRemember'=>'',
			'tokenAuth'=>$tokenAuth,
			'tokenAuthTTL'=>'2009-03-15 14:00',
			'twitter'=>'',
			'facebook'=>'',
			'googlePlus'=>'',
			'instagram'=>''
		)
	);

	file_put_contents(PATH_DATABASES.'users.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File syslog.php
	$data = array(
		array(
			'date'=>$currentDate,
			'dictionaryKey'=>'welcome-to-bludit',
			'notes'=>'',
			'idExecution'=>uniqid(),
			'method'=>'POST',
			'username'=>'admin'
	));

	file_put_contents(PATH_DATABASES.'syslog.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File security.php
	$data = array(
		'minutesBlocked'=>5,
		'numberFailuresAllowed'=>10,
		'blackList'=>array()
	);

	file_put_contents(PATH_DATABASES.'security.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File categories.php
	$data = array(
		'general'=>array('name'=>'General', 'list'=>array()),
		'music'=>array('name'=>'Music', 'list'=>array()),
		'videos'=>array('name'=>'Videos', 'list'=>array())
	);
	file_put_contents(PATH_DATABASES.'categories.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File tags.php
	$data = array(
		'bludit'=>array('name'=>'Bludit', 'list'=>array('welcome')),
		'cms'=>array('name'=>'CMS', 'list'=>array('welcome')),
		'flat-files'=>array('name'=>'Flat files', 'list'=>array('welcome'))
	);
	file_put_contents(PATH_DATABASES.'tags.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File plugins/about/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'about'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'position'=>0,
				'label'=>$Language->get('About'),
				'text'=>$Language->get('this-is-a-brief-description-of-yourself-our-your-site')
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);

	// File plugins/simplemde/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'simplemde'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'position'=>0,
				'tabSize'=>4,
				'toolbar'=>'&quot;bold&quot;, &quot;italic&quot;, &quot;heading&quot;, &quot;|&quot;, &quot;quote&quot;, &quot;unordered-list&quot;, &quot;|&quot;, &quot;link&quot;, &quot;image&quot;, &quot;code&quot;, &quot;horizontal-rule&quot;, &quot;|&quot;, &quot;preview&quot;, &quot;side-by-side&quot;, &quot;fullscreen&quot;, &quot;guide&quot;'
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);

	// File plugins/tags/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'tags'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'position'=>2,
				'label'=>$Language->get('Tags')
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);

	// File for about page
	$data = 'Title: '.$Language->get('About').PHP_EOL.'Content: '.PHP_EOL.$Language->get('installer-page-about-content');
	file_put_contents(PATH_PAGES.'about'.DS.FILENAME, $data, LOCK_EX);

	// File for welcome page
	$text1 = Text::replaceAssoc(
			array(
				'{{ADMIN_AREA_LINK}}'=>PROTOCOL.DOMAIN.HTML_PATH_ROOT.'admin'
			),
			$Language->get('Manage your Bludit from the admin panel')
	);

	$data = 'Title: '.$Language->get('Welcome').'
Content:
'.$Language->get('congratulations-you-have-successfully-installed-your-bludit').'

### '.$Language->get('whats-next').'
- '.$text1.'
- '.$Language->get('Follow Bludit on').' [Twitter](https://twitter.com/bludit) / [Facebook](https://www.facebook.com/bluditcms) / [Google+](https://plus.google.com/+Bluditcms)
- '.$Language->get('Chat with developers and users on Gitter').'
- '.$Language->get('visit-the-forum-for-support').'
- '.$Language->get('Read the documentation for more information');

	file_put_contents(PATH_PAGES.'welcome'.DS.FILENAME, $data, LOCK_EX);

	return true;
}

// Check form's parameters and finish Bludit installation.
function checkPOST($args)
{
	global $Language;

	// Check empty password
	if( strlen($args['password']) < 6 ) {
		return '<div>'.$Language->g('Password must be at least 6 characters long').'</div>';
	}

	// Sanitize email
	$email = sanitize::email($args['email']);

	// Install Bludit
	install($args['password'], $email, $args['timezone']);

	return true;
}

function redirect($url) {
	if(!headers_sent()) {
		header("Location:".$url, TRUE, 302);
		exit;
	}

	exit('<meta http-equiv="refresh" content="0; url="'.$url.'">');
}

// ============================================================================
// MAIN
// ============================================================================

$error = '';

if( alreadyInstalled() ) {
	exit('Bludit is already installed');
}

if( isset($_GET['demo']) ) {
	install('demo123', '', 'UTC');
	redirect(HTML_PATH_ROOT);
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$error = checkPOST($_POST);
	if($error===true) {
		redirect(HTML_PATH_ROOT);
	}
}

?>
<!DOCTYPE HTML>
<html class="uk-height-1-1 uk-notouch">
<head>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo $Language->get('Bludit Installer') ?></title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="bl-kernel/admin/themes/default/img/favicon.png?version=<?php echo time() ?>">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="bl-kernel/admin/themes/default/css/uikit/uikit.almost-flat.min.css?version=<?php echo time() ?>">
	<link rel="stylesheet" type="text/css" href="bl-kernel/admin/themes/default/css/installer.css?version=<?php echo time() ?>">
	<link rel="stylesheet" type="text/css" href="bl-kernel/css/font-awesome/css/font-awesome.min.css?version=<?php echo time() ?>">

	<!-- Javascript -->
	<script charset="utf-8" src="bl-kernel/js/jquery.min.js?version=<?php echo time() ?>"></script>
	<script charset="utf-8" src="bl-kernel/admin/themes/default/js/uikit/uikit.min.js?version=<?php echo time() ?>"></script>
	<script charset="utf-8" src="bl-kernel/admin/themes/default/js/jstz.min.js?version=<?php echo time() ?>"></script>
</head>
<body class="uk-height-1-1">
<div class="uk-vertical-align uk-text-center uk-height-1-1">
<div class="uk-vertical-align-middle">
	<h1 class="title"><?php echo $Language->get('Bludit Installer') ?></h1>
	<div class="content">

	<?php
		$system = checkSystem();

		// Missing requirements
		if(!empty($system)) {
			foreach($system as $values) {
				echo '<div class="uk-panel">';
				echo '<div class="uk-panel-badge uk-badge uk-badge-danger">FAIL</div>';
				echo '<h3 class="uk-panel-title">'.$values['title'].'</h3>';
				echo $values['errorText'];
				echo '</div>';
			}
		}
		// Second step
		elseif(isset($_GET['language']))
		{
	?>
		<p><?php echo $Language->get('Complete the form choose a password for the username admin') ?></p>

		<?php
			if(!empty($error)) {
				echo '<div class="uk-alert uk-alert-danger">'.$error.'</div>';
			}
		?>

		<form id="jsformInstaller" class="uk-form uk-form-stacked" method="post" action="" autocomplete="off">
		<input type="hidden" name="timezone" id="jstimezone" value="UTC">

		<div class="uk-form-row">
		<input type="text" value="admin" class="uk-width-1-1 uk-form-large" disabled>
		</div>

		<div class="uk-form-row">
		<input name="password" id="jspassword" type="password" class="uk-width-1-1 uk-form-large" value="<?php echo isset($_POST['password'])?$_POST['password']:'' ?>" placeholder="<?php echo $Language->get('Password') ?>">
		</div>

		<!--
		<div class="uk-form-row">
		<input name="email" id="jsemail" type="text" class="uk-width-1-1 uk-form-large" placeholder="<?php echo $Language->get('Email') ?>" autocomplete="off" maxlength="100">
		</div>
		-->

		<div class="uk-form-row">
		<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large"><?php $Language->p('Install') ?></button>
		</div>

		</form>

		<div id="jsshowPassword"><i class="uk-icon-eye"></i> <?php $Language->p('Show password') ?></div>
	<?php
		}
		else
		{
	?>
		<p><?php echo $Language->get('Choose your language') ?></p>

		<form class="uk-form" method="get" action="" autocomplete="off">

		<div class="uk-form-row">
		<select id="jslanguage" name="language" class="uk-width-1-1">
		<?php
			$htmlOptions = getLanguageList();
			foreach($htmlOptions as $fname=>$native) {
				echo '<option value="'.$fname.'"'.( ($finalLanguage===$fname)?' selected="selected"':'').'>'.$native.'</option>';
			}
		?>
		</select>
		</div>

		<div class="uk-form-row">
		<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large"><?php $Language->p('Next') ?></button>
		</div>

		</form>
	<?php
		}
	?>
	</div>
</div>
</div>

<script>
$(document).ready(function()
{
	// Timezone
	var timezone = jstz.determine();
	$("#jstimezone").val( timezone.name() );

	// Show password
	$("#jsshowPassword").on("click", function() {
		var input = document.getElementById("jspassword");

		if(input.getAttribute("type")=="text") {
			input.setAttribute("type", "password");
		}
		else {
			input.setAttribute("type", "text");
		}
	});

});
</script>

</body>
</html>