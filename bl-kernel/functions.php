<?php defined('BLUDIT') or die('Bludit CMS.');

// (object) Returns a Page object, the class is page.class.php, FALSE if something fail to load the page
function buildPage($key) {
	global $dbPages;
	global $dbUsers;
	global $dbCategories;
	global $Parsedown;
	global $Site;

	// Page object, content from index.txt file
	$page = new Page($key);
	if (!$page->isValid()) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from file with key: '.$key);
		return false;
	}

	// Get the database from dbPages
	$db = $dbPages->getPageDB($key);
	if (!$db) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from database with key: '.$key);
		return false;
	}

	// Foreach field from database set on the object
	foreach ($db as $field=>$value) {
		$page->setField($field, $value);
	}

	// Parse Markdown
	$contentRaw = $page->contentRaw();
	$content = Text::pre2htmlentities($contentRaw); // Parse pre code with htmlentities
	$content = $Parsedown->text($content); // Parse Markdown
	$content = Text::imgRel2Abs($content, HTML_PATH_UPLOADS); // Parse img src relative to absolute.
	$page->setField('content', $content, true);

	// Pagebrake
	$explode = explode(PAGE_BREAK, $content);
	$page->setField('contentBreak', $explode[0], true);
	$page->setField('readMore', !empty($explode[1]), true);

	// Date format
	$pageDate = $page->date();
	$page->setField('dateRaw', $pageDate, true);

	$pageDateFormated = $page->dateRaw( $Site->dateFormat() );
	$page->setField('date', $pageDateFormated, true);

	// Generate and set the User object
	$username = $page->username();
	$page->setField('user', $dbUsers->getUser($username));

	// Category
	$categoryKey = $page->categoryKey();
	$page->setField('categoryMap', $dbCategories->getMap($categoryKey));

	return $page;
}

function reindexCategories() {
	global $dbCategories;
	return $dbCategories->reindex();
}

function reindexTags() {
	global $dbTags;
	return $dbTags->reindex();
}

function buildErrorPage() {
	global $dbPages;
	global $Language;

	$page = new Page(false);
	$page->setField('title', 'Page not found');
	$page->setField('content', $Language->get('installer-page-error-content'));

	return $page;
}

function buildThePage() {
	global $Url;
	global $page, $Page;
	global $pages;

	$page = $Page = buildPage( $Url->slug() );

	// The page doesn't exist
	if($page===false) {
		$Url->setNotFound();
		return false;
	}
	// The page is not published
	elseif( $page->scheduled() || $page->draft() ) {
		$Url->setNotFound();
		return false;
	}

	$pages[0] = $page;
	return true;
}

function buildPagesForAdmin() {
	return buildPagesFor('admin');
}

function buildPagesForHome() {
	return buildPagesFor('home');
}

function buildPagesByCategory() {
	global $Url;

	$categoryKey = $Url->slug();
	return buildPagesFor('category', $categoryKey, false);
}

function buildPagesByTag() {
	global $Url;

	$tagKey = $Url->slug();
	return buildPagesFor('tag', false, $tagKey);
}

function buildPagesFor($for, $categoryKey=false, $tagKey=false) {
	global $dbPages;
	global $dbCategories;
	global $dbTags;
	global $Site;
	global $Url;
	global $pagesByKey;
	global $pages;

	// Get the page number from URL
	$pageNumber = $Url->pageNumber();

	if($for=='admin') {
		$onlyPublished = false;
		$amountOfItems = ITEMS_PER_PAGE_ADMIN;
		$list = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);
	}
	elseif($for=='home') {
		$onlyPublished = true;
		$amountOfItems = $Site->itemsPerPage();
		$list = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);
	}
	elseif($for=='category') {
		$amountOfItems = $Site->itemsPerPage();
		$list = $dbCategories->getList($categoryKey, $pageNumber, $amountOfItems);
	}
	elseif($for=='tag') {
		$amountOfItems = $Site->itemsPerPage();
		$list = $dbTags->getList($tagKey, $pageNumber, $amountOfItems);
	}

	// There are not items, invalid tag, invalid category, out of range, etc...
	if ($list===false) {
		$Url->setNotFound();
		return false;
	}

	$pages = array(); // global variable
	$pagesByKey = array(); // global variable
	foreach($list as $pageKey=>$fields) {
		$page = buildPage($pageKey);
		if($page!==false) {
			// $pagesByKey
			$pagesByKey[$pageKey] = $page;

			// $pages
			array_push($pages, $page);
		}
	}
	return $pages;
}

// Generate the global variable $pagesByParent, defined on 69.pages.php
// (boolean) $allPages, TRUE include all status, FALSE only include published status
function buildPagesByParent($onlyPublished=true) {
	global $dbPages;
	global $pagesByParent;
	global $pagesByParentByKey;

	// Get DB
	$pageNumber = 1;
	$amountOfItems = -1;
	$db = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);

	// Get Keys
	$keys = array_keys($db);
	foreach($keys as $pageKey) {
		$page = buildPage($pageKey);
		if($page!==false) {
			$parentKey = $page->parentKey();
			// FALSE if the page is parent
			if($parentKey===false) {
				array_push($pagesByParent[PARENT], $page);
				$pagesByParentByKey[PARENT][$page->key()] = $page;
			} else {
				if( !isset($pagesByParent[$parentKey]) ) {
					$pagesByParent[$parentKey] = array();
				}
				array_push($pagesByParent[$parentKey], $page);
				$pagesByParentByKey[$parentKey][$page->key()] = $page;
			}
		}
	}
}

// Returns an Array with all pages existing on the system
// (boolean) $allPages, TRUE returns all pages with any status, FALSE all published pages
/*
	array(
		pageKey1 => Page object,
		pageKey2 => Page object,
		...
		pageKeyN => Page object,
	)
*/
function buildAllpages($onlyPublished=true) {
	global $dbPages;

	// Get DB
	$pageNumber = 1;
	$amountOfItems = -1;
	$db = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);

	$tmp = array();
	$keys = array_keys($db);
	foreach($keys as $pageKey) {
		$page = buildPage($pageKey);
		if($page!==false) {
			$tmp[$page->key()] = $page;
		}
	}
	return $tmp;
}

// Returns TRUE if the plugin is enabled, FALSE otherwise
function pluginEnabled($pluginName) {
	global $plugins;

	$pluginClass = 'plugin'.Text::firstCharUp($pluginName);
	if( isset($plugins['all'][$pluginClass]) ) {
		return $plugins['all'][$pluginClass]->installed();
	}

	return false;
}

function printDebug($array) {
	echo '<pre>';
	var_dump($array);
	echo '</pre>';
}

function createPage($args) {
	global $dbPages;
	global $Syslog;

	// The user is always the one loggued
	$args['username'] = Session::get('username');
	if ( Text::isEmpty($args['username']) ) {
		return false;
	}

	// External Cover Image
	if ( Text::isNotEmpty(($args['externalCoverImage'])) ) {
		$args['coverImage'] = $args['externalCoverImage'];
		unset($args['externalCoverImage']);
	}

	$key = $dbPages->add($args);
	if ($key) {
		// Call the plugins after page created
		Theme::plugins('afterPageCreate');

		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'new-page-created',
			'notes'=>$args['title']
		));

		return $key;
	}

	Log::set('Function createNewPage()'.LOG_SEP.'Error occurred when trying to create the page');
	Log::set('Function createNewPage()'.LOG_SEP.'Cleaning database...');
	$dbPages->delete($key);
	Log::set('Function createNewPage()'.LOG_SEP.'Cleaning finished...');

	return false;
}

function editPage($args) {
	global $dbPages;
	global $Syslog;

	// The user is always the one loggued
	$args['username'] = Session::get('username');
	if ( Text::isEmpty($args['username']) ) {
		Log::set('Function editPage()'.LOG_SEP.'Empty username.');
		return false;
	}

	// External Cover Image
	if ( Text::isNotEmpty(($args['externalCoverImage'])) ) {
		$args['coverImage'] = $args['externalCoverImage'];
		unset($args['externalCoverImage']);
	}

	if (!isset($args['parent'])) {
		$args['parent'] = '';
	}

	$key = $dbPages->edit($args);
	if ($key) {
		// Call the plugins after page modified
		Theme::plugins('afterPageModify');

		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'page-edited',
			'notes'=>$args['title']
		));

		return $key;
	}

	Log::set('Function editPage()'.LOG_SEP.'ERROR: Something happen when try to edit the page.');
	return false;
}

function deletePage($key) {
	global $dbPages;
	global $Syslog;

	if( $dbPages->delete($key) ) {
		// Call the plugins after page deleted
		Theme::plugins('afterPageDelete');

		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'page-deleted',
			'notes'=>$key
		));

		return true;
	}

	return false;
}

function disableUser($username) {
	global $dbUsers;
	global $Login;
	global $Syslog;

	// The editors can't disable users
	if($Login->role()!=='admin') {
		return false;
	}

	if( $dbUsers->disableUser($username) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'user-disabled',
			'notes'=>$username
		));

		return true;
	}

	return false;
}

function editUser($args) {
	global $dbUsers;
	global $Syslog;

	if( $dbUsers->set($args) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'user-edited',
			'notes'=>$args['username']
		));

		return true;
	}

	return false;
}

function deleteUser($args, $deleteContent=false) {
	global $dbUsers;
	global $Login;
	global $Syslog;

	// The user admin cannot be deleted
	if($args['username']=='admin') {
		return false;
	}

	// The editors can't delete users
	if($Login->role()!=='admin') {
		return false;
	}

	if($deleteContent) {
		//$dbPosts->deletePostsByUser($args['username']);
	}
	else {
		//$dbPosts->linkPostsToUser($args['username'], 'admin');
	}

	if( $dbUsers->delete($args['username']) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'user-deleted',
			'notes'=>$args['username']
		));

		return true;
	}

	return false;
}

function createUser($args) {
	global $dbUsers;
	global $Language;
	global $Syslog;

	// Check empty username
	if( Text::isEmpty($args['new_username']) ) {
		Alert::set($Language->g('username-field-is-empty'), ALERT_STATUS_FAIL);
		return false;
	}

	// Check already exist username
	if( $dbUsers->exists($args['new_username']) ) {
		Alert::set($Language->g('username-already-exists'), ALERT_STATUS_FAIL);
		return false;
	}

	// Password length
	if( strlen($args['new_password']) < 6 ) {
		Alert::set($Language->g('Password must be at least 6 characters long'), ALERT_STATUS_FAIL);
		return false;
	}

	// Check new password and confirm password are equal
	if( $args['new_password'] != $args['confirm_password'] ) {
		Alert::set($Language->g('The password and confirmation password do not match'), ALERT_STATUS_FAIL);
		return false;
	}

	// Filter form fields
	$tmp = array();
	$tmp['username'] = $args['new_username'];
	$tmp['password'] = $args['new_password'];
	$tmp['role']	 = $args['role'];
	$tmp['email']	 = $args['email'];

	// Add the user to the database
	if( $dbUsers->add($tmp) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'new-user',
			'notes'=>$tmp['username']
		));

		return true;
	}

	return false;
}

function editSettings($args) {
	global $Site;
	global $Syslog;

	if (isset($args['language'])) {
		if ($args['language']!=$Site->language()) {
			$tmp = new dbJSON(PATH_LANGUAGES.$args['language'].'.json', false);
			if (isset($tmp->db['language-data']['locale'])) {
				$args['locale'] = $tmp->db['language-data']['locale'];
			} else {
				$args['locale'] = $args['language'];
			}
		}
	}

	if( $Site->set($args) ) {
		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'changes-on-settings',
			'notes'=>''
		));

		return true;
	}

	return false;
}

function editCategory($oldCategoryKey, $newCategory) {
	global $Language;
	global $dbPages;
	global $dbCategories;
	global $Syslog;

	if( Text::isEmpty($oldCategoryKey) || Text::isEmpty($newCategory) ) {
		Alert::set($Language->g('Empty fields'));
		return false;
	}

	if( $dbCategories->edit($oldCategoryKey, $newCategory) == false ) {
		Alert::set($Language->g('Already exist a category'));
		return false;
	}

	$dbPages->changeCategory($oldCategoryKey, $newCategory);

	$Syslog->add(array(
		'dictionaryKey'=>'category-edited',
		'notes'=>$newCategory
	));

	Alert::set($Language->g('The changes have been saved'));
	return true;
}

function deleteCategory($categoryKey) {
	global $Language;
	global $dbCategories;
	global $Syslog;

	// Remove the category by key
	$dbCategories->remove($categoryKey);

	$Syslog->add(array(
		'dictionaryKey'=>'category-deleted',
		'notes'=>$categoryKey
	));

	Alert::set($Language->g('The changes have been saved'));
	return true;
}