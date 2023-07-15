<?php defined('BLUDIT') or die('Bludit CMS.');

// Re-index database of categories
// If you create/edit/remove a page is necessary regenerate the database of categories
function reindexCategories()
{
  global $categories;
  return $categories->reindex();
}

// Re-index database of tags
// If you create/edit/remove a page is necessary regenerate the database of tags
function reindexTags()
{
  global $tags;
  return $tags->reindex();
}

// Generate the page 404 Not found
function buildErrorPage()
{
  global $site;
  global $L;

  try {
    $pageNotFoundKey = $site->pageNotFound();
    $pageNotFound = new Page($pageNotFoundKey);
  } catch (Exception $e) {
    $pageNotFound = new Page(false);
    $pageNotFound->setField('title',   $L->get('page-not-found'));
    $pageNotFound->setField('content',   $L->get('page-not-found-content'));
    $pageNotFound->setField('username',   'admin');
  }

  return $pageNotFound;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
// This function generate a particular page from the current slug of the url
// If the slug has not a page associated returns FALSE and set not-found as true
function buildThePage()
{
  global $url;

  try {
    $pageKey = $url->slug();
    $page = new Page($pageKey);
  } catch (Exception $e) {
    $url->setNotFound();
    return false;
  }

  if ($page->draft() || $page->scheduled() || $page->autosave()) {
    if ($url->parameter('preview') !== md5($page->uuid())) {
      $url->setNotFound();
      return false;
    }
  }

  return $page;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesForHome()
{
  return buildPagesFor('home');
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesByCategory()
{
  global $url;

  $categoryKey = $url->slug();
  return buildPagesFor('category', $categoryKey, false);
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesByTag()
{
  global $url;

  $tagKey = $url->slug();
  return buildPagesFor('tag', false, $tagKey);
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
// Generate the global variables $content / $content, defined on 69.pages.php
// This function is use for buildPagesForHome(), buildPagesByCategory(), buildPagesByTag()
function buildPagesFor($for, $categoryKey = false, $tagKey = false)
{
  global $pages;
  global $categories;
  global $tags;
  global $site;
  global $url;

  // Get the page number from URL
  $pageNumber = $url->pageNumber();

  if ($for == 'home') {
    $onlyPublished = true;
    $numberOfItems = $site->itemsPerPage();
    $list = $pages->getList($pageNumber, $numberOfItems, $onlyPublished);

    // Include sticky pages only in the first page
    if ($pageNumber == 1) {
      $sticky = $pages->getStickyDB();
      $list = array_merge($sticky, $list);
    }
  } elseif ($for == 'category') {
    $numberOfItems = $site->itemsPerPage();
    $list = $categories->getList($categoryKey, $pageNumber, $numberOfItems);
  } elseif ($for == 'tag') {
    $numberOfItems = $site->itemsPerPage();
    $list = $tags->getList($tagKey, $pageNumber, $numberOfItems);
  }

  // There are not items, invalid tag, invalid category, out of range, etc...
  if ($list === false) {
    $url->setNotFound();
    return false;
  }

  $content = array();
  foreach ($list as $pageKey) {
    try {
      $page = new Page($pageKey);
      if (($page->type() == 'published') ||
        ($page->type() == 'sticky') ||
        ($page->type() == 'static')
      ) {
        array_push($content, $page);
      }
    } catch (Exception $e) {
      // continue
    }
  }

  return $content;
}

// Returns an array with all the static pages as Page-Object
// The static pages are order by position all the time
function buildStaticPages()
{
  global $pages;

  $list = array();
  $pagesKey = $pages->getStaticDB();
  foreach ($pagesKey as $pageKey) {
    try {
      $page = new Page($pageKey);
      array_push($list, $page);
    } catch (Exception $e) {
      // continue
    }
  }

  return $list;
}

// Returns the Page-Object if exists, FALSE otherwise
function buildPage($pageKey)
{
  try {
    $page = new Page($pageKey);
    return $page;
  } catch (Exception $e) {
    return false;
  }
}

// Returns an array with all the parent pages as Page-Object
// The pages are order by the settings on the system
function buildParentPages()
{
  global $pages;

  $list = array();
  $pagesKey = $pages->getPublishedDB();
  foreach ($pagesKey as $pageKey) {
    try {
      $page = new Page($pageKey);
      if ($page->isParent()) {
        array_push($list, $page);
      }
    } catch (Exception $e) {
      // continue
    }
  }

  return $list;
}

// Returns the Plugin-Object if is enabled and installed, FALSE otherwise
function getPlugin($pluginClassName)
{
  global $plugins;

  if (pluginActivated($pluginClassName)) {
    return $plugins['all'][$pluginClassName];
  }
  return false;
}

// Returns TRUE if the plugin is activaed / installed, FALSE otherwise
function pluginActivated($pluginClassName)
{
  global $plugins;

  if (isset($plugins['all'][$pluginClassName])) {
    return $plugins['all'][$pluginClassName]->installed();
  }
  return false;
}

function activatePlugin($pluginClassName)
{
  global $plugins;
  global $syslog;
  global $L;

  // Check if the plugin exists
  if (isset($plugins['all'][$pluginClassName])) {
    $plugin = $plugins['all'][$pluginClassName];
    if ($plugin->install()) {
      // Add to syslog
      $syslog->add(array(
        'dictionaryKey' => 'plugin-activated',
        'notes' => $plugin->name()
      ));

      // Create an alert
      Alert::set($L->g('plugin-activated'));
      return true;
    }
  }
  return false;
}

function deactivatePlugin($pluginClassName)
{
  global $plugins;
  global $syslog;
  global $L;

  // Check if the plugin exists
  if (isset($plugins['all'][$pluginClassName])) {
    $plugin = $plugins['all'][$pluginClassName];

    if ($plugin->uninstall()) {
      // Add to syslog
      $syslog->add(array(
        'dictionaryKey' => 'plugin-deactivated',
        'notes' => $plugin->name()
      ));

      // Create an alert
      Alert::set($L->g('plugin-deactivated'));
      return true;
    }
  }
  return false;
}

function deactivateAllPlugin()
{
  global $plugins;
  global $syslog;
  global $L;

  // Check if the plugin exists
  foreach ($plugins['all'] as $plugin) {
    if ($plugin->uninstall()) {
      // Add to syslog
      $syslog->add(array(
        'dictionaryKey' => 'plugin-deactivated',
        'notes' => $plugin->name()
      ));
    }
  }
  return false;
}

function changePluginsPosition($pluginClassList)
{
  global $plugins;
  global $syslog;
  global $L;

  foreach ($pluginClassList as $position => $pluginClassName) {
    if (isset($plugins['all'][$pluginClassName])) {
      $plugin = $plugins['all'][$pluginClassName];
      $plugin->setPosition(++$position);
    }
  }

  // Add to syslog
  $syslog->add(array(
    'dictionaryKey' => 'plugins-sorted',
    'notes' => ''
  ));

  Alert::set($L->g('The changes have been saved'));
  return true;
}

/*
	Create a new page

	The array $args support all the keys from variable $dbFields of the class pages.class.php
	If you don't pass all the keys, the default values are used, the default values are from $dbFields in the class pages.class.php
*/
function createPage($args)
{
  global $pages;
  global $syslog;
  global $L;

  // Check if the autosave page exists for this new page and delete it
  if (isset($args['uuid'])) {
    $autosaveKey = $pages->getByUUID('autosave-' . $args['uuid']);
    if (!empty($autosaveKey)) {
      Log::set('Function createPage()' . LOG_SEP . 'Autosave deleted for ' . $args['title'], LOG_TYPE_INFO);
      deletePage($autosaveKey);
    }
  }

  // The user is always the one logged
  $args['username'] = Session::get('username');
  if (empty($args['username'])) {
    Log::set('Function createPage()' . LOG_SEP . 'Empty username.', LOG_TYPE_ERROR);
    return false;
  }

  $key = $pages->add($args);
  if ($key) {
    // Call the plugins after page created
    Theme::plugins('afterPageCreate', array($key));

    reindexCategories();
    reindexTags();

    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'new-content-created',
      'notes' => (empty($args['title']) ? $key : $args['title'])
    ));

    return $key;
  }

  Log::set('Function createNewPage()' . LOG_SEP . 'Error occurred when trying to create the page', LOG_TYPE_ERROR);
  Log::set('Function createNewPage()' . LOG_SEP . 'Cleaning database...', LOG_TYPE_ERROR);
  deletePage($key);
  Log::set('Function createNewPage()' . LOG_SEP . 'Cleaning finished...', LOG_TYPE_ERROR);

  return false;
}

function editPage($args)
{
  global $pages;
  global $syslog;

  // Check if the autosave/preview page exists for this new page and delete it
  if (isset($args['uuid'])) {
    $autosaveKey = $pages->getByUUID('autosave-' . $args['uuid']);
    if ($autosaveKey) {
      Log::set('Function editPage()' . LOG_SEP . 'Autosave/Preview deleted for ' . $autosaveKey, LOG_TYPE_INFO);
      deletePage($autosaveKey);
    }
  }

  // Check if the key is not empty
  if (empty($args['key'])) {
    Log::set('Function editPage()' . LOG_SEP . 'Empty key.', LOG_TYPE_ERROR);
    return false;
  }

  // Check if the page key exist
  if (!$pages->exists($args['key'])) {
    Log::set('Function editPage()' . LOG_SEP . 'Page key does not exist, ' . $args['key'], LOG_TYPE_ERROR);
    return false;
  }

  $key = $pages->edit($args);
  if ($key) {
    // Call the plugins after page modified
    Theme::plugins('afterPageModify', array($key));

    reindexCategories();
    reindexTags();

    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'content-edited',
      'notes' => empty($args['title']) ? $key : $args['title']
    ));

    return $key;
  }

  Log::set('Function editPage()' . LOG_SEP . 'Something happen when try to edit the page.', LOG_TYPE_ERROR);
  return false;
}

function deletePage($key)
{
  global $pages;
  global $syslog;

  if ($pages->delete($key)) {
    // Call the plugins after page deleted
    Theme::plugins('afterPageDelete', array($key));

    reindexCategories();
    reindexTags();

    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'content-deleted',
      'notes' => $key
    ));

    return true;
  }

  return false;
}

function editUser($args)
{
  global $users;
  global $syslog;

  if ($users->set($args)) {
    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'user-edited',
      'notes' => $args['username']
    ));

    return true;
  }

  return false;
}

function disableUser($args)
{
  global $users;
  global $login;
  global $syslog;

  // Arguments
  $username = $args['username'];

  // Only administrators can disable users
  if ($login->role() !== 'admin') {
    return false;
  }

  // Check if the username exists
  if (!$users->exists($username)) {
    return false;
  }

  // Disable the user
  if ($users->disableUser($username)) {
    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'user-disabled',
      'notes' => $username
    ));

    return true;
  }

  return false;
}

function deleteUser($args)
{
  global $users, $pages;
  global $login;
  global $syslog;

  // Arguments
  $username = $args['username'];
  $deleteContent = isset($args['deleteContent']) ? $args['deleteContent'] : false;

  // Only administrators can delete users
  if ($login->role() !== 'admin') {
    return false;
  }

  // The user admin cannot be deleted
  if ($username == 'admin') {
    return false;
  }

  // Check if the username exists
  if (!$users->exists($username)) {
    return false;
  }

  if ($deleteContent) {
    $pages->deletePagesByUser(array('username' => $username));
  } else {
    $pages->transferPages(array('oldUsername' => $username));
  }

  if ($users->delete($username)) {
    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'user-deleted',
      'notes' => $username
    ));

    return true;
  }

  return false;
}

function createUser($args)
{
  global $users;
  global $L;
  global $syslog;

  $args['new_username'] = Text::removeSpecialCharacters($args['new_username']);

  // Check empty username
  if (Text::isEmpty($args['new_username'])) {
    Alert::set($L->g('username-field-is-empty'), ALERT_STATUS_FAIL);
    return false;
  }

  // Check already exist username
  if ($users->exists($args['new_username'])) {
    Alert::set($L->g('username-already-exists'), ALERT_STATUS_FAIL);
    return false;
  }

  // Password length
  if (Text::length($args['new_password']) < PASSWORD_LENGTH) {
    Alert::set($L->g('Password must be at least ' . PASSWORD_LENGTH . ' characters long'), ALERT_STATUS_FAIL);
    return false;
  }

  // Check new password and confirm password are equal
  if ($args['new_password'] != $args['confirm_password']) {
    Alert::set($L->g('The password and confirmation password do not match'), ALERT_STATUS_FAIL);
    return false;
  }

  // Filter form fields
  $tmp = array();
  $tmp['username'] = $args['new_username'];
  $tmp['password'] = $args['new_password'];
  $tmp['role']   = $args['role'];
  $tmp['email']   = $args['email'];

  // Add the user to the database
  if ($users->add($tmp)) {
    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'new-user-created',
      'notes' => $tmp['username']
    ));

    return true;
  }

  return false;
}

function editSettings($args)
{
  global $site;
  global $syslog;
  global $L;
  global $pages;

  if (isset($args['language'])) {
    if ($args['language'] != $site->language()) {
      $tmp = new dbJSON(PATH_LANGUAGES . $args['language'] . '.json', false);
      if (isset($tmp->db['language-data']['locale'])) {
        $args['locale'] = $tmp->db['language-data']['locale'];
      } else {
        $args['locale'] = $args['language'];
      }
    }
  }

  if (empty($args['homepage'])) {
    $args['homepage'] = '';
    $args['uriBlog'] = '';
  }

  if (empty($args['pageNotFound'])) {
    $args['pageNotFound'] = '';
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

  if (!empty($args['uriBlog'])) {
    $args['uriBlog'] = Text::addSlashes($args['uriBlog']);
  } else {
    if (!empty($args['homepage']) && empty($args['uriBlog'])) {
      $args['uriBlog'] = '/blog/';
    } else {
      $args['uriBlog'] = '';
    }
  }

  if (isset($args['extremeFriendly'])) {
    $args['extremeFriendly'] = (($args['extremeFriendly'] == 'true') ? true : false);
  }

  if (isset($args['customFields'])) {
    // Custom fields need to be JSON format valid, also the empty JSON need to be "{}"
    json_decode($args['customFields']);
    if (json_last_error() != JSON_ERROR_NONE) {
      return false;
    }
    $pages->setCustomFields($args['customFields']);
  }

  if ($site->set($args)) {
    // Check current order-by if changed it reorder the content
    if ($site->orderBy() != ORDER_BY) {
      if ($site->orderBy() == 'date') {
        $pages->sortByDate();
      } else {
        $pages->sortByPosition();
      }
      $pages->save();
    }

    // Add syslog
    $syslog->add(array(
      'dictionaryKey' => 'settings-changes',
      'notes' => ''
    ));

    // Create alert
    Alert::set($L->g('The changes have been saved'));
    return true;
  }

  return false;
}

function changeUserPassword($args)
{
  global $users;
  global $L;
  global $syslog;

  // Arguments
  $username = $args['username'];
  $newPassword = $args['newPassword'];
  $confirmPassword = $args['confirmPassword'];

  // Password length
  if (Text::length($newPassword) < 6) {
    Alert::set($L->g('Password must be at least 6 characters long'), ALERT_STATUS_FAIL);
    return false;
  }

  if ($newPassword != $confirmPassword) {
    Alert::set($L->g('The password and confirmation password do not match'), ALERT_STATUS_FAIL);
    return false;
  }

  if ($users->setPassword(array('username' => $username, 'password' => $newPassword))) {
    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'user-password-changed',
      'notes' => $username
    ));

    Alert::set($L->g('The changes have been saved'), ALERT_STATUS_OK);
    return true;
  }

  return false;
}

// Returns true if the user is allowed to proceed
function checkRole($allowRoles, $redirect = true)
{
  global $login;
  global $L;
  global $syslog;

  $userRole = $login->role();
  if (in_array($userRole, $allowRoles)) {
    return true;
  }

  if ($redirect) {
    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'access-denied',
      'notes' => $login->username()
    ));

    Alert::set($L->g('You do not have sufficient permissions'));
    Redirect::page('dashboard');
  }
  return false;
}

// Add a new category to the system
// Returns TRUE is successfully added, FALSE otherwise
function createCategory($args)
{
  global $categories;
  global $L;
  global $syslog;

  if (Text::isEmpty($args['name'])) {
    Alert::set($L->g('Category name is empty'), ALERT_STATUS_FAIL);
    return false;
  }

  if ($categories->add(array('name' => $args['name'], 'description' => $args['description']))) {
    // Add to syslog
    $syslog->add(array(
      'dictionaryKey' => 'new-category-created',
      'notes' => $args['name']
    ));

    Alert::set($L->g('Category added'), ALERT_STATUS_OK);
    return true;
  }

  Alert::set($L->g('The category already exists'), ALERT_STATUS_FAIL);
  return false;
}

function editCategory($args)
{
  global $L;
  global $pages;
  global $categories;
  global $syslog;

  if (Text::isEmpty($args['name']) || Text::isEmpty($args['newKey'])) {
    Alert::set($L->g('Empty fields'));
    return false;
  }

  $newCategoryKey = $categories->edit($args);

  if ($newCategoryKey == false) {
    Alert::set($L->g('The category already exists'));
    return false;
  }

  // Change the category key in the pages database
  $pages->changeCategory($args['oldKey'], $newCategoryKey);

  // Add to syslog
  $syslog->add(array(
    'dictionaryKey' => 'category-edited',
    'notes' => $newCategoryKey
  ));

  Alert::set($L->g('The changes have been saved'));
  return true;
}

function deleteCategory($args)
{
  global $L;
  global $categories;
  global $syslog;

  // Remove the category by key
  $categories->remove($args['oldKey']);

  // Remove the category from the pages ? or keep it if the user want to recovery the category ?

  // Add to syslog
  $syslog->add(array(
    'dictionaryKey' => 'category-deleted',
    'notes' => $args['oldKey']
  ));

  Alert::set($L->g('The changes have been saved'));
  return true;
}

// Returns an array with all the categories
// By default, the database of categories is alphanumeric sorted
function getCategories()
{
  global $categories;

  $list = array();
  foreach ($categories->keys() as $key) {
    $category = new Category($key);
    array_push($list, $category);
  }
  return $list;
}

// Returns the object category if the category exists, FALSE otherwise
function getCategory($key)
{
  try {
    $category = new Category($key);
    return $category;
  } catch (Exception $e) {
    return false;
  }
}

// Returns an array with all the tags
// By default, the database of tags is alphanumeric sorted
function getTags()
{
  global $tags;

  $list = array();
  foreach ($tags->db as $key => $fields) {
    $tag = new Tag($key);
    array_push($list, $tag);
  }
  return $list;
}

// Returns the object tag if the tag exists, FALSE otherwise
function getTag($key)
{
  try {
    $tag = new Tag($key);
    return $tag;
  } catch (Exception $e) {
    return false;
  }
}

// Activate a theme
function activateTheme($themeDirectory)
{
  global $site;
  global $syslog;
  global $L, $language;

  if (Sanitize::pathFile(PATH_THEMES . $themeDirectory)) {

    // Disable current theme
    $currentTheme = $site->theme();
    deactivatePlugin($currentTheme);

    // Install new theme
    if (Filesystem::fileExists(PATH_THEMES . $themeDirectory . DS . 'install.php')) {
      include_once(PATH_THEMES . $themeDirectory . DS . 'install.php');
    }

    // Install theme's plugin
    activatePlugin($themeDirectory);

    $site->set(array('theme' => $themeDirectory));

    $syslog->add(array(
      'dictionaryKey' => 'new-theme-configured',
      'notes' => $themeDirectory
    ));

    Alert::set($L->g('The changes have been saved'));
    return true;
  }
  return false;
}

function ajaxResponse($status = 0, $message = "", $data = array())
{
  $default = array('status' => $status, 'message' => $message);
  $output = array_merge($default, $data);
  exit(json_encode($output));
}

/*
| This function checks the image extension,
| generate a new filename to not overwrite the exists,
| generate the thumbnail,
| and move the image to a proper place
|
| @file		string	Path and filename of the image
| @imageDir	string	Path where the image is going to be stored
| @thumbnailDir	string	Path where the thumbnail is going to be stored, if you don't set the variable is not going to create the thumbnail
|
| @return	string/boolean	Path and filename of the new image or FALSE if there were some error
*/
function transformImage($file, $imageDir, $thumbnailDir = false)
{
  global $site;

  // Check image extension
  $fileExtension = Filesystem::extension($file);
  $fileExtension = Text::lowercase($fileExtension);
  if (!in_array($fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'])) {
    return false;
  }

  // Generate a filename to not overwrite current image if exists
  $filename = Filesystem::filename($file);
  $nextFilename = Filesystem::nextFilename($filename, $imageDir);

  // Move the image to a proper place and rename
  $image = $imageDir . $nextFilename;
  Filesystem::mv($file, $image);
  chmod($image, 0644);

  // Generate Thumbnail
  if (!empty($thumbnailDir)) {
    if (($fileExtension == 'svg') || ($fileExtension == 'webp')) {
      Filesystem::symlink($image, $thumbnailDir . $nextFilename);
    } else {
      $Image = new Image();
      $Image->setImage($image, $site->thumbnailWidth(), $site->thumbnailHeight(), 'crop');
      $Image->saveImage($thumbnailDir . $nextFilename, $site->thumbnailQuality(), true);
    }
  }

  return $image;
}

function downloadRestrictedFile($file)
{
  if (is_file($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit(0);
  }
}
