<?php
// Security constant
define('BLUDIT', true);

// PATHs
define('PATH_ROOT',					__DIR__.'/');
define('PATH_CONTENT',				PATH_ROOT.'content/');
define('PATH_POSTS',				PATH_CONTENT.'posts/');
define('PATH_PAGES',				PATH_CONTENT.'pages/');
define('PATH_DATABASES',			PATH_CONTENT.'databases/');
define('PATH_PLUGINS_DATABASES',	PATH_CONTENT.'databases/plugins/');


//
// Create directories
//

// 7=read,write,execute | 5=read,execute
$dirpermissions = 0755;

if(mkdir(PATH_POSTS, $dirpermissions, true))
{
	$errorText = 'Error when trying to created the directory=>'.PATH_POSTS;
	error_log($errorText, 0);
}

if(mkdir(PATH_PAGES.'error', $dirpermissions, true))
{
	$errorText = 'Error when trying to created the directory=>'.PATH_PAGES;
	error_log($errorText, 0);
}

if(mkdir(PATH_PLUGINS_DATABASES, $dirpermissions, true))
{
	$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES;
	error_log($errorText, 0);
}

//
// Create files
//

$dataHead = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>".PHP_EOL;

// File pages.php
$data = array(
	'error'=>array(
		"description"=>"Error page",
        "username"=>"admin",
        "tags"=>"",
        "status"=>"published",
        "unixTimeCreated"=>1430686755,
        "unixTimeModified"=>0,
        "position"=>0
        )
);

file_put_contents(PATH_DATABASES.'pages.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

// File posts.php
$data = array();
file_put_contents(PATH_DATABASES.'posts.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

// File site.php
$data = array(
    "title"=>"Bludit CMS",
    "description"=>"",
    "footer"=>"Footer text - 2015",
    "language"=>"english",
    "locale"=>"en_EN",
    "timezone"=>"America/Argentina/Buenos_Aires",
    "theme"=>"pure",
    "adminTheme"=>"default",
    "homepage"=>"",
    "postsperpage"=>"6",
    "uriPost"=>"/post/",
    "uriPage"=>"/",
    "uriTag"=>"/tag/",
    "advancedOptions"=>"false",
    "url"=>"http:/localhost/cms/bludit-bitbucket/"
);

file_put_contents(PATH_DATABASES.'site.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

// File users.php
$data = array(
	'admin'=>array(
        "firstName"=>"",
        "lastName"=>"",
        "twitter"=>"",
        "role"=>"admin",
        "password"=>"7607d34033344d9a4615a8795d865ec4a47851e7",
        "salt"=>"adr32t",
        "email"=>"",
        "registered"=>1430686755
        )
);

file_put_contents(PATH_DATABASES.'users.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

// File index.txt for error page
$data = "Title: Error".PHP_EOL."Content: The page has not been found.";
file_put_contents(PATH_PAGES.'error/index.txt', $data, LOCK_EX);

?>