<?php defined('BLUDIT') or die('Bludit CMS.');

// Returns a Page-Object, the class is page.class.php, FALSE if something fail to load the page
function buildPage($key) {
	global $dbPages;
	global $dbUsers;
	global $dbCategories;
	global $Parsedown;
	global $site;

	if (empty($key)) {
		return false;
	}

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
	$parsedown = new Parsedown();
	$content = $parsedown->text($content); // Parse Markdown
	$content = Text::imgRel2Abs($content, DOMAIN_UPLOADS); // Parse img src relative to absolute (with domain)
	$page->setField('content', $content, true);

	// Pagebrake
	$explode = explode(PAGE_BREAK, $content);
	$page->setField('contentBreak', $explode[0], true);
	$page->setField('readMore', !empty($explode[1]), true);

	// Date format
	$pageDate = $page->date();
	$page->setField('dateRaw', $pageDate, true);

	$pageDateFormated = $page->dateRaw( $site->dateFormat() );
	$page->setField('date', $pageDateFormated, true);

	// Generate and set the User object
	$username = $page->username();
	$page->setField('user', $dbUsers->getUser($username));

	// Category
	$categoryKey = $page->categoryKey();
	$page->setField('categoryMap', $dbCategories->getMap($categoryKey));

	// Get the keys of the child
	$page->setField('childrenKeys', $dbPages->getChildren($key));

	// Set previous and next page key
	$page->setField('previousKey', $dbPages->previousPageKey($key));
	$page->setField('nextKey', $dbPages->nextPageKey($key));

	return $page;
}

// Re-index database of categories
// If you create/edit/remove a page is necessary regenerate the database of categories
function reindexCategories() {
	global $dbCategories;
	return $dbCategories->reindex();
}

// Re-index database of tags
// If you create/edit/remove a page is necessary regenerate the database of tags
function reindexTags() {
	global $dbTags;
	return $dbTags->reindex();
}

// Generate the page 404 Not found
function buildErrorPage() {
	global $site;
	global $language;
	global $dbUsers;

	try {
		$pageNotFoundKey = $site->pageNotFound();
		$pageNotFound = New PageX($pageNotFoundKey);
	} catch (Exception $e) {
		$pageNotFound = New PageX(false);
		$pageNotFound->setField('title', 	$language->get('page-not-found'));
		$pageNotFound->setField('content', 	$language->get('page-not-found-content'));
		$pageNotFound->setField('user', 	$dbUsers->getUser('admin'));
	}

	return $pageNotFound;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
// This function generate a particular page from the current slug of the url
// If the slug has not a page associacted returns FALSE and is set not-found as true
function buildThePage() {
	global $url;

	try {
		$pageKey = $url->slug();
		$page = New PageX($pageKey);
	} catch (Exception $e) {
		$url->setNotFound();
		return false;
	}

	// Check if the page is NOT published
	if ( !$page->published() ) {
		$url->setNotFound();
		return false;
	}

	return $page;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesForHome() {
	return buildPagesFor('home');
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesByCategory() {
	global $url;

	$categoryKey = $url->slug();
	return buildPagesFor('category', $categoryKey, false);
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesByTag() {
	global $url;

	$tagKey = $url->slug();
	return buildPagesFor('tag', false, $tagKey);
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
// Generate the global variables $content / $content, defined on 69.pages.php
// This function is use for buildPagesForHome(), buildPagesByCategory(), buildPagesByTag()
function buildPagesFor($for, $categoryKey=false, $tagKey=false) {
	global $dbPages;
	global $dbCategories;
	global $dbTags;
	global $site;
	global $url;

	// Get the page number from URL
	$pageNumber = $url->pageNumber();

	if ($for=='home') {
		$onlyPublished = true;
		$amountOfItems = $site->itemsPerPage();
		$list = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);

		// Include sticky pages only in the first page
		if ($pageNumber==1) {
			$sticky = $dbPages->getStickyDB();
			$list = array_merge($sticky, $list);
		}
	}
	elseif ($for=='category') {
		$amountOfItems = $site->itemsPerPage();
		$list = $dbCategories->getList($categoryKey, $pageNumber, $amountOfItems);
	}
	elseif ($for=='tag') {
		$amountOfItems = $site->itemsPerPage();
		$list = $dbTags->getList($tagKey, $pageNumber, $amountOfItems);
	}

	// There are not items, invalid tag, invalid category, out of range, etc...
	if ($list===false) {
		$url->setNotFound();
		return false;
	}

	$content = array();
	foreach ($list as $pageKey) {
		try {
			$page = new PageX($pageKey);
			array_push($content, $page);
		} catch (Exception $e) {
			// continue
		}
	}
	return $content;
}

// Returns an array with all the static pages as Page-Object
// The static pages are order by position all the time
function buildStaticPages() {
	global $dbPages;

	$list = array();
	$staticPages = $dbPages->getStaticDB();
	foreach ($staticPages as $staticPageKey) {
		try {
			$staticPage = new PageX($staticPageKey);
			array_push($list, $staticPage);
		} catch (Exception $e) {
			// continue
		}
	}

	return $list;
}

// Returns an array with all the parent pages as Page-Object
// The pages are order by the settings on the system
function buildParentPages() {
	global $dbPages;

	$list = array();
	$pagesKey = $dbPages->getPublishedDB();
	foreach ($pagesKey as $pageKey) {
		$page = buildPage($pageKey);
		if ($page->isParent()) {
			array_push($list, $page);
		}
	}

	return $list;
}

// DEPRECATED
// Generate the global variable $pagesByParent, defined on 69.pages.php
function buildPagesByParent($publishedPages=true, $staticPages=true) {
	global $dbPages;
	global $pagesByParent;
	global $pagesByParentByKey;

	$onlyKeys = true;
	$keys = array();
	if ($publishedPages) {
		$keys = array_merge($keys, $dbPages->getPublishedDB($onlyKeys));
	}

	foreach ($keys as $pageKey) {
		$page = buildPage($pageKey);
		if ($page!==false) {
			$parentKey = $page->parentKey();
			// FALSE if the page is parent
			if ($parentKey===false) {
				array_push($pagesByParent[PARENT], $page);
				$pagesByParentByKey[PARENT][$page->key()] = $page;
			} else {
				if (!isset($pagesByParent[$parentKey])) {
					$pagesByParent[$parentKey] = array();
				}
				array_push($pagesByParent[$parentKey], $page);
				$pagesByParentByKey[$parentKey][$page->key()] = $page;
			}
		}
	}
}

// DEPRECATED
// Returns an Array with all pages existing on the system
/*
	array(
		pageKey1 => Page object,
		pageKey2 => Page object,
		...
		pageKeyN => Page object,
	)
*/
function buildAllpages($publishedPages=true, $staticPages=true, $draftPages=true, $scheduledPages=true) {
	global $dbPages;

	// Get DB
	$onlyKeys = true;
	$keys = array();
	if ($publishedPages) {
		$keys = array_merge($keys, $dbPages->getPublishedDB($onlyKeys));
	}
	if ($staticPages) {
		$keys = array_merge($keys, $dbPages->getStaticDB($onlyKeys));
	}
	if ($draftPages) {
		$keys = array_merge($keys, $dbPages->getDraftDB($onlyKeys));
	}
	if ($scheduledPages) {
		$keys = array_merge($keys, $dbPages->getScheduledDB($onlyKeys));
	}

	$tmp = array();
	foreach ($keys as $pageKey) {
		$page = buildPage($pageKey);
		if ($page!==false) {
			$tmp[$page->key()] = $page;
		}
	}
	return $tmp;
}

// Returns the Plugin-Object if is enabled and installed, FALSE otherwise
function getPlugin($pluginClassName) {
	global $plugins;

	if (pluginEnabled($pluginClassName)) {
		return $plugins['all'][$pluginClassName];
	}
	return false;
}

// DEPRACTED
// Returns TRUE if the plugin is enabled and installed, FALSE otherwise
function pluginEnabled($pluginClassName) {
	return pluginActivated($pluginClassName);
}

// Returns TRUE if the plugin is activaed / installed, FALSE otherwise
function pluginActivated($pluginClassName) {
        global $plugins;

        if (isset($plugins['all'][$pluginClassName])) {
                return $plugins['all'][$pluginClassName]->installed();
        }
        return false;
}

function activatePlugin($pluginClassName) {
	global $plugins;
	global $syslog;
	global $Language;

	// Check if the plugin exists
	if (isset($plugins['all'][$pluginClassName])) {
		$plugin = $plugins['all'][$pluginClassName];
		if ($plugin->install()) {
			// Add to syslog
			$syslog->add(array(
				'dictionaryKey'=>'plugin-activated',
				'notes'=>$plugin->name()
			));

			// Create an alert
			Alert::set($Language->g('plugin-activated'));
			return true;
		}
	}
	return false;
}

function deactivatePlugin($pluginClassName) {
	global $plugins;
	global $syslog;
	global $Language;

	// Check if the plugin exists
	if (isset($plugins['all'][$pluginClassName])) {
		$plugin = $plugins['all'][$pluginClassName];

		if ($plugin->uninstall()) {
			// Add to syslog
			$syslog->add(array(
				'dictionaryKey'=>'plugin-deactivated',
				'notes'=>$plugin->name()
			));

			// Create an alert
			Alert::set($Language->g('plugin-deactivated'));
			return true;
		}
	}
	return false;
}

function changePluginsPosition($pluginClassList) {
	global $plugins;
	global $syslog;
	global $Language;

	foreach ($pluginClassList as $position=>$pluginClassName) {
		if (isset($plugins['all'][$pluginClassName])) {
			$plugin = $plugins['all'][$pluginClassName];
			$plugin->setPosition(++$position);
		}
	}

	// Add to syslog
	$syslog->add(array(
		'dictionaryKey'=>'plugins-sorted',
		'notes'=>''
	));

	return true;
}

function createPage($args) {
	global $dbPages;
	global $syslog;
	global $Language;

	// Check if the autosave page exists for this new page and delete it
	if (isset($args['uuid'])) {
		$autosaveKey = $dbPages->getByUUID('autosave-'.$args['uuid']);
		if (!empty($autosaveKey)) {
			Log::set('Function createPage()'.LOG_SEP.'Autosave deleted for '.$args['title'], LOG_TYPE_INFO);
			deletePage($autosaveKey);
		}
	}

	// The user is always the one loggued
	$args['username'] = Session::get('username');
	if (empty($args['username'])) {
		Log::set('Function createPage()'.LOG_SEP.'Empty username.', LOG_TYPE_ERROR);
		return false;
	}

	$key = $dbPages->add($args);
	if ($key) {
		// Call the plugins after page created
		Theme::plugins('afterPageCreate');

		reindexCategories();
		reindextags();

		// Add to syslog
		$syslog->add(array(
			'dictionaryKey'=>'new-content-created',
			'notes'=>$args['title']
		));

		Alert::set( $Language->g('new-content-created') );
		return $key;
	}

	Log::set('Function createNewPage()'.LOG_SEP.'Error occurred when trying to create the page', LOG_TYPE_ERROR);
	Log::set('Function createNewPage()'.LOG_SEP.'Cleaning database...', LOG_TYPE_ERROR);
	deletePage($key);
	Log::set('Function createNewPage()'.LOG_SEP.'Cleaning finished...', LOG_TYPE_ERROR);

	return false;
}

function editPage($args) {
	global $dbPages;
	global $syslog;

	// Check if the autosave page exists for this new page and delete it
	if (isset($args['uuid'])) {
		$pageKey = $dbPages->getByUUID('autosave-'.$args['uuid']);
		if (!empty($pageKey)) {
			Log::set('Function editPage()'.LOG_SEP.'Autosave deleted for '.$args['title'], LOG_TYPE_INFO);
			deletePage($pageKey);
		}
	}

	// Check if the key is not empty
	if (empty($args['key'])) {
		Log::set('Function editPage()'.LOG_SEP.'Empty key.', LOG_TYPE_ERROR);
		return false;
	}

	// Check if the page key exist
	if (!$dbPages->exists($args['key'])) {
		Log::set('Function editPage()'.LOG_SEP.'Page key does not exist, '.$args['key'], LOG_TYPE_ERROR);
		return false;
	}

	// Title and content need to be here because from inside the dbPages is not visible
	if (empty($args['title']) || empty($args['content'])) {
		$page = buildPage($args['key']);
		if (empty($args['title'])) {
			$args['title'] = $page->title();
		}
		if (empty($args['content'])) {
			$args['content'] = $page->contentRaw();
		}
	}

	$key = $dbPages->edit($args);
	if ($key) {
		// Call the plugins after page modified
		Theme::plugins('afterPageModify');

		reindexCategories();
		reindextags();

		// Add to syslog
		$syslog->add(array(
			'dictionaryKey'=>'content-edited',
			'notes'=>$args['title']
		));

		return $key;
	}

	Log::set('Function editPage()'.LOG_SEP.'Something happen when try to edit the page.', LOG_TYPE_ERROR);
	return false;
}

function deletePage($key) {
	global $dbPages;
	global $syslog;

	if( $dbPages->delete($key) ) {
		// Call the plugins after page deleted
		Theme::plugins('afterPageDelete');

		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Add to syslog
		$syslog->add(array(
			'dictionaryKey'=>'content-deleted',
			'notes'=>$key
		));

		return true;
	}

	return false;
}

function editUser($args) {
	global $dbUsers;
	global $syslog;

	if ($dbUsers->set($args)) {
		$syslog->add(array(
			'dictionaryKey'=>'user-edited',
			'notes'=>$args['username']
		));
		return true;
	}

	return false;
}

function disableUser($args) {
	global $dbUsers;
	global $login;
	global $syslog;

	// Arguments
	$username = $args['username'];

	// Only administrators can disable users
	if ($login->role()!=='admin') {
		return false;
	}

	// Check if the username exists
	if (!$dbUsers->exists($username)) {
		return false;
	}

	// Disable the user
	if ($dbUsers->disableUser($username)) {
		$syslog->add(array(
			'dictionaryKey'=>'user-disabled',
			'notes'=>$username
		));
		return true;
	}

	return false;
}

function deleteUser($args) {
	global $dbUsers, $dbPages;
	global $login;
	global $syslog;

	// Arguments
	$username = $args['username'];
	$deleteContent = isset($args['deleteContent']) ? $args['deleteContent'] : false;

	// Only administrators can delete users
	if ($login->role()!=='admin') {
		return false;
	}

	// The user admin cannot be deleted
	if ($username=='admin') {
		return false;
	}

	// Check if the username exists
	if (!$dbUsers->exists($username)) {
		return false;
	}

	if ($deleteContent) {
		$dbPages->deletePagesByUser(array('username'=>$username));
	} else {
		$dbPages->transferPages(array('oldUsername'=>$username));
	}

	if ($dbUsers->delete($username)) {
		$syslog->add(array(
			'dictionaryKey'=>'user-deleted',
			'notes'=>$username
		));
		return true;
	}

	return false;
}

function createUser($args) {
	global $dbUsers;
	global $Language;
	global $syslog;

	// Check empty username
	if (Text::isEmpty($args['new_username'])) {
		Alert::set($Language->g('username-field-is-empty'), ALERT_STATUS_FAIL);
		return false;
	}

	// Check already exist username
	if ($dbUsers->exists($args['new_username'])) {
		Alert::set($Language->g('username-already-exists'), ALERT_STATUS_FAIL);
		return false;
	}

	// Password length
	if (Text::length($args['new_password']) < PASSWORD_LENGTH) {
		Alert::set($Language->g('Password must be at least '.PASSWORD_LENGTH.' characters long'), ALERT_STATUS_FAIL);
		return false;
	}

	// Check new password and confirm password are equal
	if ($args['new_password'] != $args['confirm_password']) {
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
	if ($dbUsers->add($tmp)) {
		// Add to syslog
		$syslog->add(array(
			'dictionaryKey'=>'new-user-created',
			'notes'=>$tmp['username']
		));

		return true;
	}

	return false;
}

function editSettings($args) {
	global $site;
	global $syslog;
	global $Language;
	global $dbPages;

	if (isset($args['language'])) {
		if ($args['language']!=$site->language()) {
			$tmp = new dbJSON(PATH_LANGUAGES.$args['language'].'.json', false);
			if (isset($tmp->db['language-data']['locale'])) {
				$args['locale'] = $tmp->db['language-data']['locale'];
			} else {
				$args['locale'] = $args['language'];
			}
		}
	}

	if (isset($args['uriPage'])) {
		$args['uriPage'] = Text::addSlashes($args['uriPage']);
	}

	if (isset($args['uriTag'])) {
		$args['uriTag'] = Text::addSlashes($args['uriTag']);
	}

	if (isset($args['uriCategory'])) {
		$args['uriCategory'] = Text::addSlashes($args['uriCategory']);
	}

	if (isset($args['uriBlog'])) {
		$args['uriBlog'] = Text::addSlashes($args['uriBlog']);
	} else {
		$args['uriBlog'] = '';
	}

	if ($site->set($args)) {
		// Check current order-by if changed it reorder the content
		if ($site->orderBy()!=ORDER_BY) {
			if ($site->orderBy()=='date') {
				$dbPages->sortByDate();
			} else {
				$dbPages->sortByPosition();
			}
			$dbPages->save();
		}

		// Add syslog
		$syslog->add(array(
			'dictionaryKey'=>'changes-on-settings',
			'notes'=>''
		));

		// Create alert
		Alert::set($Language->g('The changes have been saved'));
		return true;
	}

	return false;
}

function changeUserPassword($args) {
	global $dbUsers;
	global $Language;
	global $syslog;

	// Arguments
	$username = $args['username'];
	$newPassword = $args['newPassword'];
	$confirmPassword = $args['confirmPassword'];

	// Password length
	if (Text::length($newPassword) < 6) {
		Alert::set($Language->g('Password must be at least 6 characters long'), ALERT_STATUS_FAIL);
		return false;
	}

	if ($newPassword!=$confirmPassword) {
		Alert::set($Language->g('The password and confirmation password do not match'), ALERT_STATUS_FAIL);
		return false;
	}

	if ($dbUsers->setPassword(array('username'=>$username, 'password'=>$newPassword))) {
		$syslog->add(array(
			'dictionaryKey'=>'user-password-changed',
			'notes'=>$username
		));
		Alert::set($Language->g('The changes have been saved'), ALERT_STATUS_OK);
		return true;
	}

	return false;
}

// Returns true if the user is allowed to procceded
function checkRole($allowRoles, $redirect=true) {
	global $login;
	global $Language;
	global $syslog;

	$userRole = $login->role();
	if (in_array($userRole, $allowRoles)) {
		return true;
	}

	if ($redirect) {
		$syslog->add(array(
			'dictionaryKey'=>'access-deny',
			'notes'=>$login->username()
		));
		Alert::set($Language->g('You do not have sufficient permissions'));
		Redirect::page('dashboard');
	}
	return false;
}

// Add a new category to the system
// Returns TRUE is successfully added, FALSE otherwise
function createCategory($category) {
	global $dbCategories;
	global $Language;
	global $syslog;

	if (Text::isEmpty($category)) {
		Alert::set($Language->g('Category name is empty'), ALERT_STATUS_FAIL);
		return false;
	}

	if ($dbCategories->add(array('name'=>$category))) {
		$syslog->add(array(
			'dictionaryKey'=>'new-category-created',
			'notes'=>$category
		));

		Alert::set($Language->g('Category added'), ALERT_STATUS_OK);
		return true;
	}

	Alert::set($Language->g('The category already exists'), ALERT_STATUS_FAIL);
	return false;
}

function editCategory($args) {
	global $Language;
	global $dbPages;
	global $dbCategories;
	global $syslog;

	if (Text::isEmpty($args['name']) || Text::isEmpty($args['newKey']) ) {
		Alert::set($Language->g('Empty fields'));
		return false;
	}

	$newCategoryKey = $dbCategories->edit($args);

	if ($newCategoryKey==false) {
		Alert::set($Language->g('The category already exists'));
		return false;
	}

	// Change the category key in the pages database
	$dbPages->changeCategory($args['oldKey'], $newCategoryKey);

	$syslog->add(array(
		'dictionaryKey'=>'category-edited',
		'notes'=>$newCategoryKey
	));

	Alert::set($Language->g('The changes have been saved'));
	return true;
}

function deleteCategory($args) {
	global $Language;
	global $dbCategories;
	global $syslog;

	// Remove the category by key
	$dbCategories->remove($args['oldCategoryKey']);

	// Remove the category from the pages ? or keep it if the user want to recovery the category ?

	$syslog->add(array(
		'dictionaryKey'=>'category-deleted',
		'notes'=>$args['oldCategoryKey']
	));

	Alert::set($Language->g('The changes have been saved'));
	return true;
}

// Returns an array with all the categories
// By default, the database of categories is alphanumeric sorted
function getCategories() {
	global $dbCategories;

	$list = array();
	foreach ($dbCategories->db as $key=>$fields) {
		$category = new Category($key);
		array_push($list, $category);
	}
	return $list;
}

// Returns the object category if the category exists, FALSE otherwise
function getCategory($key) {
	$category = new Category($key);
	if (!$category->isValid()) {
		return false;
	}
	return $category;
}

// Returns an array with all the tags
// By default, the database of tags is alphanumeric sorted
function getTags() {
	global $dbTags;

	$list = array();
	foreach ($dbTags->db as $key=>$fields) {
		$tag = new Tag($key);
		array_push($list, $tag);
	}
	return $list;
}

function activateTheme($themeDirectory) {
	global $site;
	global $syslog;

	if (Sanitize::pathFile(PATH_THEMES.$themeDirectory)) {
		$site->set(array('theme'=>$themeDirname));

		$syslog->add(array(
			'dictionaryKey'=>'new-theme-configured',
			'notes'=>$themeDirname
		));

		Alert::set( $Language->g('The changes have been saved') );
		return true;
	}
	return false;
}
