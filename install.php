<?php

/*
 * Bludit
 * http://www.bludit.com
 * Author Diego Najar
 * Bludit is opensource software licensed under the MIT license.
*/

// Security constant
define('BLUDIT', true);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// PHP paths
define('PATH_ROOT',		__DIR__.DS);
define('PATH_CONTENT',		PATH_ROOT.'content'.DS);
define('PATH_POSTS',		PATH_CONTENT.'posts'.DS);
define('PATH_UPLOADS',		PATH_CONTENT.'uploads'.DS);
define('PATH_PAGES',		PATH_CONTENT.'pages'.DS);
define('PATH_DATABASES',	PATH_CONTENT.'databases'.DS);
define('PATH_PLUGINS_DATABASES',PATH_CONTENT.'databases'.DS.'plugins'.DS);
define('PATH_KERNEL',		PATH_ROOT.'kernel'.DS);
define('PATH_HELPERS',		PATH_KERNEL.'helpers'.DS);
define('PATH_LANGUAGES',	PATH_ROOT.'languages'.DS);
define('PATH_ABSTRACT',		PATH_KERNEL.'abstract'.DS);
define('DOMAIN',		getenv('HTTP_HOST'));

// HTML PATHs
$base = (dirname(getenv('SCRIPT_NAME'))==DS)?'/':dirname(getenv('SCRIPT_NAME')).'/';
define('HTML_PATH_ROOT', $base);

// Log separator
define('LOG_SEP', ' | ');

// JSON
if(!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 128);
}

// Check if JSON encode and decode are enabled.
define('JSON', function_exists('json_encode'));

// Database format date
define('DB_DATE_FORMAT', 'Y-m-d H:i');

// Charset, default UTF-8.
define('CHARSET', 'UTF-8');

// Multibyte string extension loaded.
define('MB_STRING', extension_loaded('mbstring'));

if(MB_STRING)
{
	// Set internal character encoding.
	mb_internal_encoding(CHARSET);

	// Set HTTP output character encoding.
	mb_http_output(CHARSET);
}

// PHP Classes
include(PATH_HELPERS.'sanitize.class.php');
include(PATH_HELPERS.'valid.class.php');
include(PATH_HELPERS.'text.class.php');
include(PATH_ABSTRACT.'dbjson.class.php');
include(PATH_KERNEL.'dblanguage.class.php');
include(PATH_HELPERS.'log.class.php');
include(PATH_HELPERS.'date.class.php');

// Try to detect language from HTTP
$explode = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
$localeFromHTTP = empty($explode[0])?'en_US':str_replace('-', '_', $explode[0]);

if(isset($_GET['language'])) {
	$localeFromHTTP = Sanitize::html($_GET['language']);
}

$Language = new dbLanguage($localeFromHTTP);

// Timezone
$iniDate = ini_get('date.timezone');
if(empty($iniDate)) {
	date_default_timezone_set('UTC');
}

// Locales
setlocale(LC_ALL, $localeFromHTTP);

// ============================================================================
// FUNCTIONS
// ============================================================================

function getLanguageList()
{
	$files = glob(PATH_LANGUAGES.'*.json');

	$tmp = array();

	foreach($files as $file)
	{
		$t = new dbJSON($file, false);
		$native = $t->db['language-data']['native'];
		$locale = basename($file, '.json');
		$tmp[$locale] = $native;
	}

	return $tmp;
}

// Generate a random string.
// Thanks, http://stackoverflow.com/questions/4356289/php-random-string-generator
function getRandomString($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Check if Bludit is installed.
function alreadyInstalled() {
    return file_exists(PATH_DATABASES.'site.php');
}

// Check the system, permissions, php version, modules, etc.
// Returns an array with the problems otherwise empty array.
function checkSystem()
{
	$stdOut = array();
	$dirpermissions = 0755;
	$phpModules = array();

	if(function_exists('get_loaded_extensions')) {
		$phpModules = get_loaded_extensions();
	}

	if(!version_compare(phpversion(), '5.3', '>='))
	{
		$errorText = 'Current PHP version '.phpversion().', you need > 5.3. (ERR_202)';
		error_log($errorText, 0);

		$tmp['title'] = 'PHP version';
		$tmp['errorText'] = $errorText;
		array_push($stdOut, $tmp);

		return $stdOut;
	}

	if(!file_exists(PATH_ROOT.'.htaccess'))
	{
		$errorText = 'Missing file, upload the file .htaccess (ERR_201)';
		error_log($errorText, 0);

		$tmp['title'] = 'File .htaccess';
		$tmp['errorText'] = $errorText;
		array_push($stdOut, $tmp);
	}

	if(!in_array('dom', $phpModules))
	{
		$errorText = 'PHP module DOM is not installed. (ERR_203)';
		error_log($errorText, 0);

		$tmp['title'] = 'PHP module';
		$tmp['errorText'] = $errorText;
		array_push($stdOut, $tmp);
	}

	if(!in_array('json', $phpModules))
	{
		$errorText = 'PHP module JSON is not installed. (ERR_204)';
		error_log($errorText, 0);

		$tmp['title'] = 'PHP module';
		$tmp['errorText'] = $errorText;
		array_push($stdOut, $tmp);
	}

	if(!is_writable(PATH_CONTENT))
	{
		$errorText = 'Writing test failure, check directory content permissions. (ERR_205)';
		error_log($errorText, 0);

		$tmp['title'] = 'PHP permissions';
		$tmp['errorText'] = $errorText;
		array_push($stdOut, $tmp);
	}

	return $stdOut;
}

function install($adminPassword, $email, $timezoneOffset)
{
	global $Language;

	$stdOut = array();

	$currentDate = Date::current(DB_DATE_FORMAT);

	$timezone = timezone_name_from_abbr("", $timezoneOffset, 0);

	// ============================================================================
	// Create directories
	// ============================================================================

	// 7=read,write,execute | 5=read,execute
	$dirpermissions = 0755;
	$firstPostSlug = 'first-post';

	if(!mkdir(PATH_POSTS.$firstPostSlug, $dirpermissions, true))
	{
		$errorText = 'Error when trying to created the directory=>'.PATH_POSTS.$firstPostSlug;
		error_log($errorText, 0);
	}

	if(!mkdir(PATH_PAGES.'error', $dirpermissions, true))
	{
		$errorText = 'Error when trying to created the directory=>'.PATH_PAGES.'error';
		error_log($errorText, 0);
	}

	if(!mkdir(PATH_PLUGINS_DATABASES.'pages', $dirpermissions, true))
	{
		$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES.'pages';
		error_log($errorText, 0);
	}

	if(!mkdir(PATH_PLUGINS_DATABASES.'simplemde', $dirpermissions, true))
	{
		$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES.'simplemde';
		error_log($errorText, 0);
	}

	if(!mkdir(PATH_PLUGINS_DATABASES.'tags', $dirpermissions, true))
	{
		$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES.'tags';
		error_log($errorText, 0);
	}

	if(!mkdir(PATH_UPLOADS, $dirpermissions, true))
	{
		$errorText = 'Error when trying to created the directory=>'.PATH_UPLOADS;
		error_log($errorText, 0);
	}

	// ============================================================================
	// Create files
	// ============================================================================

	$dataHead = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>".PHP_EOL;

	// File pages.php
	$data = array(
		'error'=>array(
		'description'=>'Error page',
		'username'=>'admin',
		'tags'=>array(),
		'status'=>'published',
		'date'=>$currentDate,
		'position'=>0
	    	)
	);

	file_put_contents(PATH_DATABASES.'pages.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File posts.php
	$data = array(
	$firstPostSlug=>array(
		'description'=>'Welcome to Bludit',
		'username'=>'admin',
		'status'=>'published',
		'tags'=>array('bludit'=>'Bludit','cms'=>'CMS','flat-files'=>'Flat files'),
		'allowComments'=>'false',
		'date'=>$currentDate
		)
	);
	file_put_contents(PATH_DATABASES.'posts.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File site.php
	$data = array(
		'title'=>'Bludit',
		'slogan'=>'cms',
		'description'=>'',
		'footer'=>Date::current('Y'),
		'language'=>$Language->getCurrentLocale(),
		'locale'=>$Language->getCurrentLocale(),
		'timezone'=>$timezone,
		'theme'=>'pure',
		'adminTheme'=>'default',
		'homepage'=>'',
		'postsperpage'=>'6',
		'uriPost'=>'/post/',
		'uriPage'=>'/',
		'uriTag'=>'/tag/',
		'url'=>'http://'.DOMAIN.HTML_PATH_ROOT,
		'cliMode'=>'true',
		'emailFrom'=>'no-reply@'.DOMAIN
	);

	file_put_contents(PATH_DATABASES.'site.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	$salt = getRandomString();
	$passwordHash = sha1($adminPassword.$salt);

	// File users.php
	$data = array(
		'admin'=>array(
		'firstName'=>'',
		'lastName'=>'',
		'twitter'=>'',
		'role'=>'admin',
		'password'=>$passwordHash,
		'salt'=>$salt,
		'email'=>$email,
		'registered'=>$currentDate
		)
	);

	file_put_contents(PATH_DATABASES.'users.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File security.php
	$data = array(
		'minutesBlocked'=>5,
		'numberFailuresAllowed'=>10,
		'blackList'=>array()
	);

	file_put_contents(PATH_DATABASES.'security.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File tags.php
	file_put_contents(
		PATH_DATABASES.'tags.php',
		$dataHead.json_encode(
			array(
				'postsIndex'=>array(
					'bludit'=>array('name'=>'Bludit', 'posts'=>array('first-post')),
					'cms'=>array('name'=>'CMS', 'posts'=>array('first-post')),
					'flat-files'=>array('name'=>'Flat files', 'posts'=>array('first-post'))
				),
				'pagesIndex'=>array()
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);


	// PLUGINS

	// File plugins/pages/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'pages'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'position'=>0,
				'homeLink'=>true,
				'label'=>$Language->get('Pages')
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
				'position'=>0,
				'label'=>$Language->get('Tags')
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);

	// File index.txt for error page
	$data = 'Title: '.$Language->get('Error').'
	Content: '.$Language->get('The page has not been found');

	file_put_contents(PATH_PAGES.'error'.DS.'index.txt', $data, LOCK_EX);

	// File index.txt for welcome post
	$data = 'Title: '.$Language->get('First post').'
Content:

## '.$Language->get('Congratulations you have successfully installed your Bludit').'

### '.$Language->get('Whats next').'
- '.$Language->get('Manage your Bludit from the admin panel').'
- '.$Language->get('Follow Bludit on').' [Twitter](https://twitter.com/bludit) / [Facebook](https://www.facebook.com/pages/Bludit/239255789455913) / [Google+](https://plus.google.com/+Bluditcms)
- '.$Language->get('Visit the support forum').'
- '.$Language->get('Read the documentation for more information').'
- '.$Language->get('Share with your friends and enjoy');

	file_put_contents(PATH_POSTS.$firstPostSlug.DS.'index.txt', $data, LOCK_EX);

	return true;
}

function checkPOST($args)
{
	global $Language;

	// Check empty password
	if(empty($args['password']))
	{
		return '<div>'.$Language->g('The password field is empty').'</div>';
	}

	// Check invalid email
	if( !Valid::email($args['email']) && ($args['noCheckEmail']=='0') )
	{
		return '<div>'.$Language->g('Your email address is invalid').'</div><div id="jscompleteEmail">'.$Language->g('Proceed anyway').'</div>';
	}

	// Sanitize email
	$email = sanitize::email($args['email']);

	// Install Bludit
	install($args['password'], $email, $args['timezone']);

	return true;
}

// ============================================================================
// MAIN
// ============================================================================

$error = '';

if( alreadyInstalled() ) {
	exit('Bludit already installed');
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	$error = checkPOST($_POST);

	if($error===true)
	{
		if(!headers_sent())
		{
			header("Location:".HTML_PATH_ROOT, TRUE, 302);
			exit;
		}

		exit('<meta http-equiv="refresh" content="0; url="'.HTML_PATH_ROOT.'">');
	}
}

?>
<!DOCTYPE HTML>
<html class="uk-height-1-1 uk-notouch">
<head>
	<base href="admin/themes/default/">
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo $Language->get('Bludit Installer') ?></title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="./img/favicon.png">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="./css/uikit.almost-flat.min.css">
	<link rel="stylesheet" type="text/css" href="./css/installer.css">
	<link rel="stylesheet" type="text/css" href="./css/form-password.almost-flat.min.css">

	<!-- Javascript -->
	<script charset="utf-8" src="./js/jquery.min.js"></script>
	<script charset="utf-8" src="./js/uikit.min.js"></script>
	<script charset="utf-8" src="./js/form-password.min.js"></script>

</head>
<body class="uk-height-1-1">
<div class="uk-vertical-align uk-text-center uk-height-1-1">
<div class="uk-vertical-align-middle">
	<h1 class="title"><?php echo $Language->get('Bludit Installer') ?></h1>
	<div class="content">

	<?php
		$system = checkSystem();

		// Missing requirements
		if(!empty($system))
		{
			foreach($system as $values)
			{
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
		<input type="hidden" name="noCheckEmail" id="jsnoCheckEmail" value="0">
		<input type="hidden" name="timezone" id="jstimezone" value="0">

		<div class="uk-form-row">
		<input type="text" value="admin" class="uk-width-1-1 uk-form-large" disabled>
		</div>

		<div class="uk-form-row">
		<input name="password" id="jspassword" type="password" class="uk-width-1-1 uk-form-large" value="<?php echo isset($_POST['password'])?$_POST['password']:'' ?>" placeholder="<?php echo $Language->get('Password') ?>">
		</div>

		<div class="uk-form-row">
		<input name="email" id="jsemail" type="text" class="uk-width-1-1 uk-form-large" placeholder="<?php echo $Language->get('Email') ?>" autocomplete="off" maxlength="100">
		</div>

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
			foreach($htmlOptions as $locale=>$nativeName) {
				echo '<option value="'.$locale.'"'.( ($localeFromHTTP===$locale)?' selected="selected"':'').'>'.$nativeName.'</option>';
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
	// Set timezone
	var timezoneOffset = -(new Date().getTimezoneOffset() * 60);
	$("#jstimezone").val(timezoneOffset);

	// Proceed without email field.
	$("#jscompleteEmail").on("click", function() {

		console.log("Click proceed anyway");

		$("#jsnoCheckEmail").val("1");

		$("#jsformInstaller").submit();
	});

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