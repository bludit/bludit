<?php defined('BLUDIT') or die('Bludit CMS.');

$posts = array();

function build_post($slug)
{
	global $dbPosts;
	global $dbUsers;
	global $Parsedown;

	if( !$dbPosts->validPost($slug) )
		return false;

	$Post = new Post($slug);
	if( !$Post->valid() )
		return false;

	// Get post's database
	$db = $dbPosts->getDb($slug);

	foreach($db as $key=>$value)
	{
		if($key=='unixstamp')
		{
			// Not overwrite
			$Post->setField('unixstamp', 	$value, false);
			$Post->setField('date', 		Date::format($value, '%d %B'), false);
			$Post->setField('timeago',		Date::timeago($value), false);
		}
		else
		{
			// Not overwrite
			$Post->setField($key, $value, false);
		}

	}

	// Parse the content
	$content = $Parsedown->text( $Post->content() );
	$Post->setField('content', $content, true);

	// User / Author
	if( $dbUsers->validUsername( $Post->username() ) )
	{
		$user = $dbUsers->get( $Post->username() );

		$Post->setField('author', $user['first_name'].', '.$user['last_name'], false);
	}

	return $Post;
}

function build_posts_per_page()
{
	global $dbPosts;
	global $posts;

	$list = $dbPosts->getPage(0, 5);

	foreach($list as $slug=>$db)
	{
		$Post = build_post($slug);

		if($Post!==false)
		{
			array_push($posts, $Post);
		}
	}
}

// Filter by post, then build it
if( ($Url->whereAmI()==='post') && ($Url->notFound()===false) )
{
	$Post = build_post( $Url->slug() );

	if($Post===false)
	{
		$Url->setNotFound(true);
		unset($Post);
	}
	elseif( !$Post->published() )
	{
		$Url->setNotFound(true);
		unset($Post);
	}
	else
	{
		$posts[0] = $Post;
	}

}
// Build post per page
else
{
	build_posts_per_page();
}
