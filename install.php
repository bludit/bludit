<?php

// SECURITY CONSTANT
define('BLUDIT', true);

// PHP PATHS
define('PATH_ROOT',					__DIR__.'/');
define('PATH_KERNEL',				PATH_ROOT.'kernel/');
define('PATH_LANGUAGES',			PATH_ROOT.'languages/');
define('PATH_ABSTRACT',				PATH_ROOT.'kernel/abstract/');
define('PATH_BOOT',					PATH_ROOT.'kernel/boot/');
define('PATH_RULES',				PATH_ROOT.'kernel/boot/rules/');
define('PATH_CONTENT',				PATH_ROOT.'content/');
define('PATH_POSTS',				PATH_ROOT.'content/posts/');
define('PATH_PAGES',				PATH_ROOT.'content/pages/');
define('PATH_DATABASES',			PATH_ROOT.'content/databases/');
define('PATH_PLUGINS_DATABASES',	PATH_ROOT.'content/databases/plugins/');
define('PATH_HELPERS',				PATH_ROOT.'kernel/helpers/');
define('PATH_THEMES',				PATH_ROOT.'themes/');
define('PATH_PLUGINS',				PATH_ROOT.'plugins/');

// BOOT
require(PATH_BOOT.'site.php');


unset($db);
$db = new DB_SERIALIZE(PATH_DATABASES.'posts.php');
$data = array();
$data['lorem-text'] = 
array(
'allow_comments'=>true,
'unixstamp'=>Date::unixstamp(),
'description'=>'111111',
'hash'=>'asdasd23r32r23rqwda',
'status'=>'published',
'related_post'=>array('loremTest'),
'tags'=>array('lorem','impusm','lala'),
'username'=>'admin'
);

$data['lorem指出'] = 
array(
'allow_comments'=>true,
'unixstamp'=>1422836401,
'description'=>'2222222',
'hash'=>'asdasd23r32r23rqwda',
'status'=>'published',
'related_post'=>array(),
'tags'=>array('lorem','impusm','lala'),
'username'=>'diego'
);

$data['loremTest'] = 
array(
'allow_comments'=>true,
'unixstamp'=>1422836401,
'description'=>'2222222',
'hash'=>'asdasd23r32r23rqwda',
'status'=>'published',
'related_post'=>array(),
'tags'=>array('lorem','impusm','lala'),
'username'=>'diego'
);

$db->setDb(array(
	'autoincrement'=>1,
	'posts'=>$data
));


unset($db);
$db = new DB_SERIALIZE(PATH_DATABASES.'pages.php');
$data = array();
$data['error'] = 
array(
'unixstamp'=>Date::unixstamp(),
'description'=>'Error page',
'hash'=>'asdasd23r32r23rqwda',
'status'=>'published',
'tags'=>array('lorem','impusm','lala'),
'username'=>'diego'
);

$data['about'] = 
array(
'unixstamp'=>Date::unixstamp(),
'description'=>'About page',
'hash'=>'asdasd23r32r23rqwda',
'status'=>'published',
'tags'=>array('lorem','impusm','lala'),
'username'=>'diego'
);

$data['contact'] = 
array(
'unixstamp'=>Date::unixstamp(),
'description'=>'Contact page',
'hash'=>'asdasd23r32r23rqwda',
'status'=>'published',
'tags'=>array('lorem','impusm','lala'),
'username'=>'diego'
);

$db->setDb(array(
	'autoincrement'=>1,
	'pages'=>$data
));


unset($db);
$db = new DB_SERIALIZE(PATH_DATABASES.'users.php');
$data = array();
$data['admin'] = 
array(
'first_name'=>'Admin',
'last_name'=>'User',
'twitter'=>'',
'role'=>'admin',
'password'=>'3r3fasfasf',
'salt'=>'adr32t',
'email'=>''
);

$data['diego'] = 
array(
'first_name'=>'Diego',
'last_name'=>'Najar',
'twitter'=>'',
'role'=>'editor',
'password'=>'3r3fasfasf',
'salt'=>'adr32t',
'email'=>''
); 

$db->setDb(array(
	'autoincrement'=>1,
	'users'=>$data
));


unset($db);
$db = new DB_SERIALIZE(PATH_DATABASES.'site.php');
$data = array();
$data =  array(
'title'=>'Bludit CMS',
'slogan'=>'Another content management system',
'footer'=>'Copyright lala',
'language'=>'en',
'locale'=>'en_EN',
'timezone'=>'America/Argentina/Buenos_Aires',
'theme'=>'pure',
'adminTheme'=>'default',
'homepage'=>'about',
'metaTags'=>array(
	'title'=>'',
	'description'=>''
	),
'urlFilters'=>array(
	'admin'=>'/admin/',
	'post'=>'/post/',
	'tag'=>'/tag/',
	'page'=>'/'
	)
);

$db->setDb($data);


?>
