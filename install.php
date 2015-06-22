<?php
// Security constant
define('BLUDIT', true);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// PATHs
define('PATH_ROOT',					__DIR__.DS);
define('PATH_CONTENT',				PATH_ROOT.'content'.DS);
define('PATH_POSTS',                PATH_CONTENT.'posts'.DS);
define('PATH_UPLOADS',              PATH_CONTENT.'uploads'.DS);
define('PATH_PAGES',				PATH_CONTENT.'pages'.DS);
define('PATH_DATABASES',			PATH_CONTENT.'databases'.DS);
define('PATH_PLUGINS_DATABASES',	PATH_CONTENT.'databases'.DS.'plugins'.DS);
define('DOMAIN',                    getenv('HTTP_HOST'));

// HTML PATHs
$base = (dirname(getenv('SCRIPT_NAME'))==DS)?'/':dirname(getenv('SCRIPT_NAME')).'/';
define('HTML_PATH_ROOT', $base);

if(!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 128);
}

// ============================================================================
// FUNCTIONS
// ============================================================================

// Generate a random string
// Thanks, http://stackoverflow.com/questions/4356289/php-random-string-generator
function getRandomString($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function alreadyInstalled()
{
    return file_exists(PATH_DATABASES.'site.php');
}

function checkSystem()
{
    $stdOut = array();
    $dirpermissions = 0755;
    $phpModules = array();

    if(function_exists('get_loaded_extensions'))
    {
        $phpModules = get_loaded_extensions();
    }

    if(!file_exists(PATH_ROOT.'.htaccess'))
    {
        $errorText = 'Missing file, upload the file .htaccess (ERR_201)';
        error_log($errorText, 0);
        array_push($stdOut, $errorText);
    }

    if(!version_compare(phpversion(), '5.2', '>'))
    {
        $errorText = 'Current PHP version '.phpversion().', you need > 5.3. (ERR_202)';
        error_log($errorText, 0);
        array_push($stdOut, $errorText);
    }

    if(!in_array('dom', $phpModules))
    {
        $errorText = 'PHP module DOM does not exist. (ERR_203)';
        error_log($errorText, 0);
        array_push($stdOut, $errorText);
    }

    if(!in_array('json', $phpModules))
    {
        $errorText = 'PHP module JSON does not exist. (ERR_204)';
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

    if(!mkdir(PATH_PLUGINS_DATABASES, $dirpermissions, true))
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
        'footer'=>'Footer text - ©2015',
        'language'=>'english',
        'locale'=>'en_EN',
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

    // File index.txt for error page
    $data = 'Title: Error
    Content: The page has not been found.';

    file_put_contents(PATH_PAGES.'error'.DS.'index.txt', $data, LOCK_EX);

// File index.txt for welcome post
$data = 'title: First post
Content:

Congratulations, you have installed **Bludit** successfully!
---

What\'s next:
---
- Administrate your Bludit from the [Admin Area](./admin/)
- Follow Bludit on [Twitter](https://twitter.com/bludit) / Facebook / Google+
- Visit the forum for support
- Read the documentation for more information
- Share with your friend :D';

    file_put_contents(PATH_POSTS.$firstPostSlug.DS.'index.txt', $data, LOCK_EX);

    return true;
}

// ============================================================================
// MAIN
// ============================================================================

if( alreadyInstalled() )
{
    exit('Bludit already installed');
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    if(install($_POST['password'],$_POST['email']))
    {
        if(!headers_sent())
        {
            header("Location:".HTML_PATH_ROOT, TRUE, 302);
            exit;
        }

        exit('<meta http-equiv="refresh" content="0; url="'.HTML_PATH_ROOT.'" />');
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <base href="admin/themes/default/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bludit Installer</title>

    <link rel="stylesheet" href="./css/kube.min.css">
    <link rel="stylesheet" href="./css/installer.css">

    <script src="./js/jquery.min.js"></script>
    <script src="./js/kube.min.js"></script>
</head>
<body>
<div class="units-row">
    <div class="unit-centered unit-60">
        <div class="main">
            <h1 class="title">Bludit Installer</h1>
            <p>Welcome to the Bludit installer</p>

            <?php
            $system = checkSystem();

            if(empty($system))
            {
            ?>

            <p>Complete the form, choose a password for the username <strong>admin</strong></p>
            <div class="unit-centered unit-40">
            <form method="post" action="" class="forms" autocomplete="off">

                <label>
                    <input type="text" value="admin" disabled="disabled" class="width-100">
                </label>

                <label>
                    <input type="password" name="password" placeholder="Password" class="width-100" autocomplete="off">
                </label>

                <label>
                    <input type="text" name="email" placeholder="Email" class="width-100" autocomplete="off">
                </label>

                <p>
                    <button class="btn btn-blue width-100">Install</button>
                </p>
            </form>
            </div>

            <?php
            }
            else
            {
                echo '<div class="unit-centered unit-40">';
                echo '<table class="table-stripped">';

                foreach ($system as $value)
                {
                    echo '<tr><td>'.$value.'</td></tr>';
                }

                echo '</table>';
                echo '</div';
            }
            ?>

        </div>
    </div>
</div>
</body>
</html>