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

// Load language
$localeFromHTTP = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

if(isset($_GET['language'])) {
	$localeFromHTTP = Sanitize::html($_GET['language']);
}

$Language = new dbLanguage($localeFromHTTP);

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
		array_push($stdOut, $errorText);

		return $stdOut;
	}

	if(!file_exists(PATH_ROOT.'.htaccess'))
	{
		$errorText = 'Missing file, upload the file .htaccess (ERR_201)';
		error_log($errorText, 0);
		array_push($stdOut, $errorText);
	}

	if(!in_array('dom', $phpModules))
	{
		$errorText = 'PHP module DOM is not installed. (ERR_203)';
		error_log($errorText, 0);
		array_push($stdOut, $errorText);
	}

	if(!in_array('json', $phpModules))
	{
		$errorText = 'PHP module JSON is not installed. (ERR_204)';
		error_log($errorText, 0);
		array_push($stdOut, $errorText);
	}

	if(!is_writable(PATH_CONTENT))
	{
		$errorText = 'Writing test failure, check directory content permissions. (ERR_205)';
		error_log($errorText, 0);
		array_push($stdOut, $errorText);
	}

	return $stdOut;
}

function install($adminPassword, $email)
{
	global $Language;

	$stdOut = array();

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
		$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES;
		error_log($errorText, 0);
	}

	if(!mkdir(PATH_PLUGINS_DATABASES.'tinymce', $dirpermissions, true))
	{
		$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES;
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
		'tags'=>'',
		'status'=>'published',
		'unixTimeCreated'=>1430686755,
		'unixTimeModified'=>0,
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
		'tags'=>'welcome, bludit, cms',
		'allowComments'=>false,
		'unixTimeCreated'=>1430875199,
		'unixTimeModified'=>0
		)
	);
	file_put_contents(PATH_DATABASES.'posts.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File site.php
	$data = array(
		'title'=>'Bludit',
		'slogan'=>'cms',
		'description'=>'',
		'footer'=>'',
		'language'=>$Language->getCurrentLocale(),
		'locale'=>$Language->getCurrentLocale(),
		'timezone'=>'UTC',
		'theme'=>'pure',
		'adminTheme'=>'default',
		'homepage'=>'',
		'postsperpage'=>'6',
		'uriPost'=>'/post/',
		'uriPage'=>'/',
		'uriTag'=>'/tag/',
		'advancedOptions'=>'false',
		'url'=>'http://'.DOMAIN.HTML_PATH_ROOT
	);

	file_put_contents(PATH_DATABASES.'site.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	$salt = getRandomString();
	$passwordHash = sha1($adminPassword.$salt);
	$registered = time();

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
		'registered'=>$registered
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

	// File plugins/pages/db.php
	$data = array(
		'homeLink'=>true,
		'label'=>$Language->get('Pages'),
		'position'=>'0'
	);

	file_put_contents(PATH_PLUGINS_DATABASES.'pages'.DS.'db.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File plugins/tinymce/db.php
	$data = array(
		'plugins'=>'autoresize, fullscreen, pagebreak, link, textcolor, code',
		'toolbar'=>'bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | styleselect | link forecolor backcolor removeformat | pagebreak code fullscreen',
		'position'=>'0'
	);

	file_put_contents(PATH_PLUGINS_DATABASES.'tinymce'.DS.'db.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File index.txt for error page
	$data = 'Title: '.$Language->get('Error').'
	Content: '.$Language->get('The page has not been found');

	file_put_contents(PATH_PAGES.'error'.DS.'index.txt', $data, LOCK_EX);

	// File index.txt for welcome post
	$data = 'Title: '.$Language->get('First post').'
Content:

'.$Language->get('Congratulations you have successfully installed your Bludit').'
---

'.$Language->get('Whats next').'
---
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
	install($args['password'], $email, $args['language']);

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

<!doctype html>
<html lang="en">
<head>
	<base href="admin/themes/default/">
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo $Language->get('Bludit Installer') ?></title>

	<link rel="stylesheet" href="./css/kube.min.css">
	<link rel="stylesheet" href="./css/installer.css">

	<script src="./js/jquery.min.js"></script>
	<script src="./js/kube.min.js"></script>
</head>
<body>
<div class="units-row">
<div class="unit-centered unit-60">
<div class="main">

	<h1 class="title"><?php echo $Language->get('Bludit Installer') ?></h1>
	<p><?php echo $Language->get('Welcome to the Bludit installer') ?></p>

	<?php

	$system = checkSystem();

	// Missing requirements
	if(!empty($system))
	{
		echo '<div class="boxInstallerForm unit-centered unit-50">';
		echo '<table class="table-stripped">';

		foreach($system as $value) {
			echo '<tr><td>'.$value.'</td></tr>';
		}

		echo '</table>';
		echo '</div>';
	}
	// Second step
	elseif(isset($_GET['language']))
	{

	?>
		<p><?php echo $Language->get('Complete the form choose a password for the username admin') ?></p>

		<div class="boxInstallerForm unit-centered unit-40">

		<?php
			if(!empty($error)) {
				echo '<div class="tools-message tools-message-red">'.$error.'</div>';
			}
		?>

		<form id="jsformInstaller" method="post" action="" class="forms" autocomplete="off">

		<input type="hidden" name="noCheckEmail" id="jsnoCheckEmail" value="0">
		<input type="hidden" name="language" id="jslanguage" value="<?php echo $localeFromHTTP ?>">

		<label>
		<input type="text" value="admin" disabled="disabled" class="width-100">
		</label>

		<label>
		<input type="text" name="password" id="jspassword" placeholder="<?php echo $Language->get('Password visible field') ?>" class="width-100" autocomplete="off" maxlength="100" value="<?php echo isset($_POST['password'])?$_POST['password']:'' ?>">
		</label>

		<label>
		<input type="text" name="email" id="jsemail" placeholder="<?php echo $Language->get('Email') ?>" class="width-100" autocomplete="off" maxlength="100">
		</label>

		<p><button class="btn btn-blue width-100"><?php echo $Language->get('Install') ?></button>
		</p>
		</form>
		</div>
	<?php
	} // END elseif(isset($_GET['language']))
	else
	{
	?>
		<p><?php echo $Language->get('Choose your language') ?></p>

		<div class="boxInstallerForm unit-centered unit-40">

		<form id="jsformLanguage" method="get" action="" class="forms" autocomplete="off">

		<label for="jslanguage">
		<select id="jslanguage" name="language" class="width-100">
		<?php
			$htmlOptions = getLanguageList();
			foreach($htmlOptions as $locale=>$nativeName) {
				echo '<option value="'.$locale.'"'.( ($localeFromHTTP===$locale)?' selected="selected"':'').'>'.$nativeName.'</option>';
			}
		?>
		</select>
		</label>

		<p><button class="btn btn-blue width-100"><?php echo $Language->get('Next') ?></button>
		</p>
		</form>
		</div>
	<?php
	} // END else
	?>

</div>
</div>

<script>
$(document).ready(function()
{
    $("#jscompleteEmail").on("click", function() {
    	$("#jsnoCheckEmail").val("1");
    	if(!$("jspassword").val()) {
    		$("#jsformInstaller").submit();
    	}
    });
});
</script>

</div>
</body>
</html>