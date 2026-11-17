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
  global $site;

  try {
    $pageKey = $url->slug();
    $page = new Page($pageKey);
  } catch (Exception $e) {
    $url->setNotFound();
    return false;
  }

  if ($page->draft() || $page->scheduled() || $page->autosave()) {
    $preview = $url->parameter('preview');
    if (!is_string($preview) || !hash_equals(hash_hmac('sha256', $page->uuid(), $site->previewKey()), $preview)) {
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

  // There are no items, invalid tag, invalid category, out of range, etc...
  if ($list === false) {
    $url->setNotFound();
    return false;
  }

  $content = array();
  $invalidPageFound = false;
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
      $invalidPageFound = true;
      // continue
    }
  }

  if (($for === 'category' || $for === 'tag') && (!empty($list)) && (empty($content) || $invalidPageFound)) {
    if ($for === 'category') {
      reindexCategories();
      $list = $categories->getList($categoryKey, $pageNumber, $numberOfItems);
    } else {
      reindexTags();
      $list = $tags->getList($tagKey, $pageNumber, $numberOfItems);
    }

    if (is_array($list)) {
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
    $plugin = $plugins['all'][$pluginClassName];
    return $plugin->installed() && !$plugin->locked();
  }
  return false;
}

// Installs a plugin and logs/alerts on success. Used both for direct activation
// and to restore a plugin without re-running activatePlugin()'s mutual-exclusion logic.
function installPlugin($plugin)
{
  global $syslog;
  global $L;

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

    if ($plugin->locked()) {
      Alert::set($L->g('This plugin requires Bludit PRO'), ALERT_STATUS_FAIL);
      return false;
    }

    // Only one content-editor plugin can be active at a time: they all bind to
    // the same #jseditor textarea and the same global JS functions.
    $deactivatedEditors = array();
    $deactivationFailed = false;
    if ($plugin->type() == 'editor') {
      foreach ($plugins['all'] as $className => $otherPlugin) {
        if ($className !== $pluginClassName && $otherPlugin->type() == 'editor' && $otherPlugin->installed()) {
          if (deactivatePlugin($className)) {
            $deactivatedEditors[] = $className;
          } else {
            $deactivationFailed = true;
            break;
          }
        }
      }
    }

    if (!$deactivationFailed && installPlugin($plugin)) {
      return true;
    }

    // Activation failed, or a sibling editor could not be deactivated: restore
    // any editor(s) deactivated above so the site isn't left without an active editor.
    // Restore directly, without going through activatePlugin(), so the mutual-exclusion
    // loop above doesn't re-run and deactivate an editor that was already restored.
    foreach ($deactivatedEditors as $deactivatedClassName) {
      if (isset($plugins['all'][$deactivatedClassName])) {
        installPlugin($plugins['all'][$deactivatedClassName]);
      }
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
	Executes the plugins hooked on $hook and checks if they allow the operation to continue

	(string) $hook, name of the hook to execute, one of the hooks before*
	(array)  $args, arguments passed to the plugins

	Returns TRUE when the operation is allowed, otherwise FALSE.
	When a plugin cancels the operation the reason is logged and, if there is a session
	running, an alert is set so the user knows why the operation did not complete.
	The reason is also available via Theme::lastVetoReason() for the callers without a
	session, such as the API, which need to return the reason to the client.
*/
function pluginsAllowOperation($hook, $args = array())
{
  $veto = Theme::pluginsVeto($hook, $args);
  if ($veto === true) {
    return true;
  }

  Log::set('Hook ' . $hook . LOG_SEP . 'Operation cancelled: ' . $veto, LOG_TYPE_INFO);

  // The reason comes from a plugin and the alert is printed inside a Javascript string,
  // escape the special characters before setting the alert
  if (Session::started()) {
    Alert::set(Sanitize::html($veto), ALERT_STATUS_FAIL);
  }

  return false;
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

  // The user is always the one logged
  $args['username'] = Session::get('username');
  if (empty($args['username'])) {
    Log::set('Function createPage()' . LOG_SEP . 'Empty username.', LOG_TYPE_ERROR);
    return false;
  }

  // Call the plugins before the page is created, the plugins can cancel the operation
  // This is checked before deleting the autosave page, when the operation is cancelled
  // the content is not stored and the autosave page is the only copy the user has left
  if (!pluginsAllowOperation('beforePageCreate', array($args))) {
    return false;
  }

  // Check if the autosave page exists for this new page and delete it
  if (isset($args['uuid'])) {
    $autosaveKey = $pages->getByUUID('autosave-' . $args['uuid']);
    if (!empty($autosaveKey)) {
      Log::set('Function createPage()' . LOG_SEP . 'Autosave deleted for ' . $args['title'], LOG_TYPE_INFO);
      deletePage($autosaveKey, false);
    }
  }

  $key = $pages->add($args);
  if ($key) {
    // Call the plugins after page created
    Theme::plugins('afterPageCreate', array($key));

    reindexCategories();
    reindexTags();

    // Add to syslog, the autosave pages are internal copies of the editor, not content of the user
    if (!isset($args['type']) || $args['type'] !== 'autosave') {
      $syslog->add(array(
        'dictionaryKey' => 'new-content-created',
        'notes' => (empty($args['title']) ? $key : $args['title'])
      ));
    }

    return $key;
  }

  Log::set('Function createNewPage()' . LOG_SEP . 'Error occurred when trying to create the page', LOG_TYPE_ERROR);
  Log::set('Function createNewPage()' . LOG_SEP . 'Cleaning database...', LOG_TYPE_ERROR);
  deletePage($key, false);
  Log::set('Function createNewPage()' . LOG_SEP . 'Cleaning finished...', LOG_TYPE_ERROR);

  return false;
}

/*
	Edit an existing page

	The array $args support all the keys from variable $dbFields of the class pages.class.php
	plus the key of the page to edit, the keys not passed keep their current value

	Returns the key of the page edited, FALSE when the page is not edited
*/
function editPage($args)
{
  global $pages;
  global $syslog;

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

  // Call the plugins before the page is modified, the plugins can cancel the operation
  // This is checked before deleting the autosave/preview page, when the operation is
  // cancelled the changes are not stored and the autosave page keeps the work of the user
  if (!pluginsAllowOperation('beforePageModify', array($args))) {
    return false;
  }

  // Check if the autosave/preview page exists for this new page and delete it
  if (isset($args['uuid'])) {
    $autosaveKey = $pages->getByUUID('autosave-' . $args['uuid']);
    if ($autosaveKey) {
      Log::set('Function editPage()' . LOG_SEP . 'Autosave/Preview deleted for ' . $autosaveKey, LOG_TYPE_INFO);
      deletePage($autosaveKey, false);
    }
  }

  $key = $pages->edit($args);
  if ($key) {
    // Call the plugins after page modified, the key is different from the previous one when the slug or parent changed
    Theme::plugins('afterPageModify', array($key, $args['key']));

    reindexCategories();
    reindexTags();

    // Add to syslog, the autosave pages are internal copies of the editor, not content of the user
    if (!isset($args['type']) || $args['type'] !== 'autosave') {
      $syslog->add(array(
        'dictionaryKey' => 'content-edited',
        'notes' => empty($args['title']) ? $key : $args['title']
      ));
    }

    return $key;
  }

  Log::set('Function editPage()' . LOG_SEP . 'Something happen when try to edit the page.', LOG_TYPE_ERROR);
  return false;
}

/*
	Delete a page

	(string)  $key, the key of the page to delete
	(boolean) $allowVeto, TRUE executes the hook beforePageDelete and the plugins can cancel
	          the operation, FALSE skips the hook. The internal clean up of autosave pages
	          calls this function with FALSE, those pages are not content written by the user,
	          so the deletion is not added to the syslog either.
*/
function deletePage($key, $allowVeto = true)
{
  global $pages;
  global $syslog;

  // Call the plugins before the page is deleted, the plugins can cancel the operation
  if ($allowVeto && !pluginsAllowOperation('beforePageDelete', array($key))) {
    return false;
  }

  if ($pages->delete($key)) {
    // Call the plugins after page deleted
    Theme::plugins('afterPageDelete', array($key));

    reindexCategories();
    reindexTags();

    // Add to syslog, only when the user deletes the page, not the internal clean up
    if ($allowVeto) {
      $syslog->add(array(
        'dictionaryKey' => 'content-deleted',
        'notes' => $key
      ));
    }

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
  if ($args['new_password'] !== $args['confirm_password']) {
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

  if (isset($args['adminColorMode']) && !in_array($args['adminColorMode'], array('system', 'light', 'dark'), true)) {
    $args['adminColorMode'] = 'system';
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
  global $login;
  global $L;
  global $syslog;

  // Arguments
  $username = $args['username'];

  // Authorization: only admins or the user themselves can change a password
  if ($login->role() !== 'admin' && $login->username() !== $username) {
    Alert::set($L->g('You do not have permissions to access this page'), ALERT_STATUS_FAIL);
    return false;
  }
  $newPassword = $args['newPassword'];
  $confirmPassword = $args['confirmPassword'];

  // Password length
  if (Text::length($newPassword) < PASSWORD_LENGTH) {
    Alert::set($L->g('Password must be at least ' . PASSWORD_LENGTH . ' characters long'), ALERT_STATUS_FAIL);
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
// Returns TRUE if the logged user can manage the images of the page with the uuid
// Administrators and editors can manage all pages, authors only their own pages
// A page not created yet is checked by its autosave, a new uuid without pages is allowed
function canManagePageImages($uuid)
{
  global $pages;
  global $login;

  if (checkRole(array('admin', 'editor'), false)) {
    return true;
  }

  $pageKey = $pages->getByUUID($uuid);
  if (empty($pageKey)) {
    $pageKey = $pages->getByUUID('autosave-' . $uuid);
  }
  if (empty($pageKey)) {
    return true;
  }
  return $pages->db[$pageKey]['username'] === $login->username();
}

function checkRole($allowRoles, $redirect = true)
{
  global $login;
  global $L;
  global $syslog;

  $userRole = $login->role();
  if (in_array($userRole, $allowRoles, true)) {
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
  global $pages;
  global $syslog;

  // Remove the category by key
  $categories->remove($args['oldKey']);

  // Remove the category from the pages
  $pages->changeCategory($args['oldKey'], '');

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

    if ($site->themeLocked($themeDirectory)) {
      Alert::set($L->g('This theme requires Bludit PRO'), ALERT_STATUS_FAIL);
      return false;
    }

    // Disable current theme, the saved one, theme() returns the default theme
    // when the saved theme is locked
    $currentTheme = $site->getField('theme');
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

// Strips active content from an SVG file in-place using DOMDocument.
// Removes <script>, <foreignObject>, on* event attributes, javascript: URIs,
// and <use> elements with external hrefs. Returns false if the file cannot be
// parsed as valid XML, so callers should treat false as a rejection.
function sanitizeSVG($file)
{
	$content = file_get_contents($file);
	if ($content === false) {
		return false;
	}

	$dom = new DOMDocument();
	$previousLibxmlErrors = libxml_use_internal_errors(true);
	$loaded = $dom->loadXML($content);
	libxml_clear_errors();
	libxml_use_internal_errors($previousLibxmlErrors);
	if (!$loaded) {
		return false;
	}

	// Reject any document with a DOCTYPE. Entity definitions would otherwise
	// survive into the output, and content entity references are not expanded
	// into the DOM — so the XPath filters never see them and a downstream
	// parser could reintroduce active content (XXE / entity expansion).
	if ($dom->doctype !== null) {
		return false;
	}

	$xpath = new DOMXPath($dom);

	// Remove <script> elements. Use local-name() so namespaced SVG elements
	// (xmlns="http://www.w3.org/2000/svg") are matched too.
	foreach (iterator_to_array($xpath->query('//*[local-name()="script"]')) as $node) {
		$node->parentNode->removeChild($node);
	}

	// Remove <foreignObject> — can embed arbitrary HTML (namespace-agnostic).
	foreach (iterator_to_array($xpath->query('//*[local-name()="foreignObject"]')) as $node) {
		$node->parentNode->removeChild($node);
	}

	// Remove on* event attributes and javascript: URIs from every element.
	// Collect the attribute nodes (not their names) and remove them with
	// removeAttributeNode() so namespaced attributes such as xlink:href are
	// handled — removeAttribute('href') cannot match a prefixed attribute,
	// and PHP reports their ->name as the bare local name anyway.
	foreach ($xpath->query('//*') as $element) {
		$toRemove = array();
		foreach ($element->attributes as $attr) {
			$name  = strtolower($attr->localName);
			// Strip control characters/whitespace before the scheme check —
			// browsers ignore them, so "java&#x0A;script:" would otherwise
			// slip past a plain strpos('javascript:').
			$value = strtolower(preg_replace('/[\x00-\x20\x7f]+/', '', $attr->value));
			if (strpos($name, 'on') === 0) {
				$toRemove[] = $attr;
			} elseif (in_array($name, array('href', 'action', 'formaction', 'src'), true)
				&& strpos($value, 'javascript:') !== false) {
				$toRemove[] = $attr;
			}
		}
		foreach ($toRemove as $attrNode) {
			$element->removeAttributeNode($attrNode);
		}
	}

	// Remove <use> elements that reference external documents (namespace-agnostic).
	foreach (iterator_to_array($xpath->query('//*[local-name()="use"]')) as $node) {
		$href = $node->getAttribute('href');
		if (empty($href)) {
			$href = $node->getAttributeNS('http://www.w3.org/1999/xlink', 'href');
		}
		// Only allow same-document fragment references (#id).
		if (!empty($href) && strpos($href, '#') !== 0) {
			$node->parentNode->removeChild($node);
		}
	}

	$sanitized = $dom->saveXML();
	if ($sanitized === false) {
		return false;
	}

	return file_put_contents($file, $sanitized) !== false;
}

function transformImage($file, $imageDir, $thumbnailDir = false)
{
  global $site;

  // Block dotfiles
  if (strpos(basename($file), '.') === 0) {
    if (file_exists($file)) {
      @unlink($file);
    }
    return false;
  }

  // Check image extension
  $fileExtension = Filesystem::extension($file);
  $fileExtension = Text::lowercase($fileExtension);
  if (!in_array($fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'])) {
    // Delete the rejected file, the same as the dotfiles
    if (file_exists($file)) {
      @unlink($file);
    }
    return false;
  }

  // Generate a filename to not overwrite current image if exists
  $filename = Filesystem::filename($file);

  // Additional sanitization for filenames to prevent issues with special characters
  $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
  $filenameWithoutExt = Text::removeSpecialCharacters($filenameWithoutExt, '-');
  $filenameWithoutExt = Text::removeQuotes($filenameWithoutExt);
  $filenameWithoutExt = Text::removeSpaces($filenameWithoutExt, '-');
  $filename = $filenameWithoutExt . '.' . $fileExtension;

  $nextFilename = Filesystem::nextFilename($filename, $imageDir);

  // Move the image to a proper place and rename
  $image = $imageDir . $nextFilename;
  Filesystem::mv($file, $image);
  chmod($image, 0644);

  // Sanitize SVG files to remove active content before storage.
  if ($fileExtension === 'svg') {
    if (sanitizeSVG($image) === false) {
      Log::set('SVG sanitization failed (invalid XML), rejecting file: ' . $image, LOG_TYPE_ERROR);
      @unlink($image);
      return false;
    }
  }

  // Generate Thumbnail
  if (!empty($thumbnailDir) && $site->thumbnailEnable()) {
    if (!Filesystem::directoryExists($thumbnailDir)) {
      Filesystem::mkdir($thumbnailDir, true);
    }
    if (($fileExtension == 'svg')) {
      Filesystem::symlink($image, $thumbnailDir . $nextFilename);
    } else {
      $Image = new Image();
      // setImage() returns false when GD cannot decode the source (missing
      // format support or corrupted file). Skip saveImage() in that case and
      // log so the operator can diagnose — the original image remains stored
      // so the Media Manager can still display it via the fallback path.
      if ($Image->setImage($image, $site->thumbnailWidth(), $site->thumbnailHeight(), 'crop') === false) {
        Log::set('Thumbnail generation skipped (GD cannot decode): ' . $image, LOG_TYPE_ERROR);
      } else {
        $Image->saveImage($thumbnailDir . $nextFilename, $site->thumbnailQuality());
      }
    }
  }

  return $image;
}

/*
| Replaces an existing image keeping its filename, so URLs already used in
| content and the cover image keep working. The new file must have the same
| extension as the current one.
|
| @file            string  Path to the new file (usually in PATH_TMP)
| @imageDir        string  Destination directory (trailing DS)
| @thumbnailDir    string  Thumbnail directory (trailing DS), or false
| @targetFilename  string  Existing filename to replace
|
| @return string|false  The image path on success, false otherwise
*/
function replaceImage($file, $imageDir, $thumbnailDir, $targetFilename)
{
  global $site;

  $fileExtension = Text::lowercase(Filesystem::extension($file));
  $targetExtension = Text::lowercase(Filesystem::extension($targetFilename));

  if ($fileExtension !== $targetExtension || !in_array($fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'])) {
    if (file_exists($file)) {
      @unlink($file);
    }
    return false;
  }

  $targetFilename = basename($targetFilename);
  if (!Sanitize::pathFile($imageDir, $targetFilename) || !is_file($imageDir . $targetFilename)) {
    if (file_exists($file)) {
      @unlink($file);
    }
    return false;
  }

  $image = $imageDir . $targetFilename;
  $backup = PATH_TMP . 'media-replace-' . uniqid() . '.' . $fileExtension;

  // Keep the current file until the new one is safely in place.
  if (!Filesystem::mv($image, $backup)) {
    if (file_exists($file)) {
      @unlink($file);
    }
    return false;
  }

  if (!Filesystem::mv($file, $image)) {
    Filesystem::mv($backup, $image);
    return false;
  }
  chmod($image, 0644);

  if ($fileExtension === 'svg') {
    if (sanitizeSVG($image) === false) {
      Log::set('SVG sanitization failed (invalid XML), rejecting replacement: ' . $image, LOG_TYPE_ERROR);
      @unlink($image);
      Filesystem::mv($backup, $image);
      return false;
    }
  }

  // Remove the old thumbnail, including legacy .jpg thumbnails. A candidate is
  // only removed when no other original owns that filename. The legacy names
  // are kept in $legacyThumbnails to generate them again, the content can link
  // to them.
  $legacyThumbnails = array();
  if (!empty($thumbnailDir)) {
    $base = pathinfo($targetFilename, PATHINFO_FILENAME);
    foreach ($GLOBALS['ALLOWED_IMG_EXTENSION'] as $ext) {
      $candidate = $base . '.' . $ext;
      if ($candidate === $targetFilename) {
        continue;
      }
      if (is_file($imageDir . $candidate)) {
        continue;
      }
      if (is_file($thumbnailDir . $candidate) || is_link($thumbnailDir . $candidate)) {
        @unlink($thumbnailDir . $candidate);
        $legacyThumbnails[] = $candidate;
      }
    }
    if (is_file($thumbnailDir . $targetFilename) || is_link($thumbnailDir . $targetFilename)) {
      @unlink($thumbnailDir . $targetFilename);
    }
  }

  // Generate the new thumbnail. If generation fails the original stays and the
  // client falls back to it.
  if (!empty($thumbnailDir) && $site->thumbnailEnable()) {
    if (!Filesystem::directoryExists($thumbnailDir)) {
      Filesystem::mkdir($thumbnailDir, true);
    }
    if ($fileExtension === 'svg') {
      Filesystem::symlink($image, $thumbnailDir . $targetFilename);
    } else {
      $Image = new Image();
      if ($Image->setImage($image, $site->thumbnailWidth(), $site->thumbnailHeight(), 'crop') === false) {
        Log::set('Thumbnail generation skipped (GD cannot decode): ' . $image, LOG_TYPE_ERROR);
      } else {
        $Image->saveImage($thumbnailDir . $targetFilename, $site->thumbnailQuality());
        // Same thumbnail with the legacy names, saved in the format of each name
        foreach ($legacyThumbnails as $legacy) {
          $Image->saveImage($thumbnailDir . $legacy, $site->thumbnailQuality());
        }
      }
    }
  }

  @unlink($backup);
  return $image;
}

/*
| Media Manager metadata.
|
| Alt text and the image properties are stored in a small JSON database
| (bl-content/databases/media.php). The key is the filename, prefixed with the
| page UUID when uploads are restricted to pages, so two pages can safely use
| the same filename.
*/
function mediaManagerMetadataKey($uuid, $filename)
{
  $restricted = defined('IMAGE_RESTRICT') && IMAGE_RESTRICT;
  return ($restricted && !empty($uuid) ? $uuid . '/' : '') . basename($filename);
}

function mediaManagerMetadata()
{
  static $db = null;
  if ($db === null) {
    $db = new dbJSON(PATH_DATABASES . 'media.php');
  }
  return $db;
}

function mediaManagerMetadataGet($uuid, $filename)
{
  $db = mediaManagerMetadata();
  $key = mediaManagerMetadataKey($uuid, $filename);
  if (isset($db->db[$key]) && is_array($db->db[$key])) {
    return $db->db[$key];
  }
  return array();
}

function mediaManagerMetadataSave($uuid, $filename, $metadata)
{
  $db = mediaManagerMetadata();
  $key = mediaManagerMetadataKey($uuid, $filename);
  $db->db[$key] = $metadata;
  return $db->save();
}

function mediaManagerMetadataDelete($uuid, $filename)
{
  $db = mediaManagerMetadata();
  $key = mediaManagerMetadataKey($uuid, $filename);
  if (!isset($db->db[$key])) {
    return true;
  }
  unset($db->db[$key]);
  return $db->save();
}

/*
| Builds the file list for the Media Manager. Scans originals (not thumbnails)
| and pairs each original with its matching thumbnail. Used by list-images.php
| and the Media Manager preload in media.php.
|
| The thumbnail filename usually matches the original, but can differ:
|  - Legacy uploads: before commit 82f30f8b thumbnails were forced to .jpg, so
|    pre-existing pairs may have different extensions (e.g., original test.png
|    with thumbnail test.jpg).
|  - Thumbnail generation disabled or failed: no thumbnail file exists at all.
|    In that case 'thumbnail' is returned as an empty string so the client can
|    fall back to the original image URL instead of building a broken
|    PAGE_THUMBNAILS_URL + filename that would 404.
|
| The search is applied before chunking so it covers every file, not only the
| files on the current page. Only the requested page is stat'ed and passed to
| getimagesize(), so a large library does not cost one filesystem read per
| image on every request. Width, height, size and mime type are cached in the
| metadata database and returned with each item, so the Media Manager details
| panel does not need another request per image.
|
| @imagePath     string  Filesystem path to the originals directory (trailing DS)
| @thumbnailPath string  Filesystem path to the thumbnails directory (trailing DS)
| @chunk         int     Chunk size for pagination
| @query         string  Optional filename search, case-insensitive
| @uuid          string  Page UUID used for the metadata key, when restricted
| @pageNumber    int     Page to return, starting at 1
|
| @return array  'files' (items with filename, thumbnail, alt, width, height,
|                bytes and mime) and 'numberOfPages'.
*/
function mediaManagerListImages($imagePath, $thumbnailPath, $chunk, $query = '', $uuid = '', $pageNumber = 1)
{
  global $site;

  $chunk = max(1, (int) $chunk);
  $pageNumber = max(1, (int) $pageNumber);

  // Filesystem::listFiles globs '*.*' which does not match the 'thumbnails'
  // subdirectory (no dot in the name), so scanning the originals directory
  // naturally skips it.
  $files = Filesystem::listFiles($imagePath, '*', '*', MEDIA_MANAGER_SORT_BY_DATE);
  if (empty($files)) {
    return array('files' => array(), 'numberOfPages' => 0);
  }

  // The site logo is stored in PATH_UPLOADS (not in a per-page directory)
  // alongside uploaded images when IMAGE_RESTRICT is off. Exclude it from the
  // Media Manager listing so it cannot be inserted/deleted as a media file.
  $logoFilename = ($site && method_exists($site, 'logo')) ? $site->logo(false) : '';
  $query = Text::lowercase(trim($query));

  // First pass: filter and resolve thumbnails, without reading image metadata.
  $filtered = array();
  foreach ($files as $file) {
    if (is_dir($file)) {
      continue;
    }
    $filename = basename($file);
    if ($logoFilename && $filename === $logoFilename) {
      continue;
    }
    $extension = Text::lowercase(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($extension, $GLOBALS['ALLOWED_IMG_EXTENSION'])) {
      continue;
    }
    if ($query !== '' && strpos(Text::lowercase($filename), $query) === false) {
      continue;
    }

    // Resolve thumbnail. Fast path: same filename. Legacy fallback: before
    // commit 82f30f8b thumbnails were forced to .jpg, so try that single
    // extension when the fast path misses. Only accept the .jpg candidate
    // when it does not exist as an independent original (i.e. it is truly a
    // legacy thumbnail, not another file's own thumbnail). If nothing matches,
    // return an empty string so the client falls back to the original URL.
    $thumbnail = '';
    if (is_file($thumbnailPath . $filename)) {
      $thumbnail = $filename;
    } else {
      $base = pathinfo($filename, PATHINFO_FILENAME);
      $candidate = $base . '.jpg';
      if ($candidate !== $filename
        && is_file($thumbnailPath . $candidate)
        && !is_file($imagePath . $candidate)
      ) {
        $thumbnail = $candidate;
      }
    }

    $filtered[] = array(
      'filename' => $filename,
      'thumbnail' => $thumbnail,
      'path' => $file,
    );
  }

  $numberOfPages = (count($filtered) === 0) ? 0 : (int) ceil(count($filtered) / $chunk);
  $pageItems = array_slice($filtered, ($pageNumber - 1) * $chunk, $chunk);
  if (empty($pageItems)) {
    return array('files' => array(), 'numberOfPages' => $numberOfPages);
  }

  $metaDB = mediaManagerMetadata();
  $metadataDirty = false;
  $items = array();

  foreach ($pageItems as $item) {
    $file = $item['path'];
    $filename = $item['filename'];

    // Read the cached properties and fill the gaps only once per file.
    $key = mediaManagerMetadataKey($uuid, $filename);
    $stored = isset($metaDB->db[$key]) && is_array($metaDB->db[$key]) ? $metaDB->db[$key] : array();
    $meta = $stored;
    if (!isset($meta['alt'])) {
      $meta['alt'] = '';
    }

    $bytes = @filesize($file);
    $meta['bytes'] = ($bytes === false) ? 0 : (int) $bytes;

    if (empty($meta['width']) || empty($meta['height']) || empty($meta['mime'])) {
      $info = @getimagesize($file);
      if ($info !== false) {
        $meta['width'] = (int) $info[0];
        $meta['height'] = (int) $info[1];
        if (!empty($info['mime'])) {
          $meta['mime'] = $info['mime'];
        }
      }
    }
    if (empty($meta['mime'])) {
      $mime = Filesystem::mimeType($file);
      $meta['mime'] = ($mime === false) ? '' : $mime;
    }
    if (!isset($meta['width'])) {
      $meta['width'] = 0;
    }
    if (!isset($meta['height'])) {
      $meta['height'] = 0;
    }

    // Compare without depending on the key order, otherwise every request
    // would look like a change and rewrite the database.
    ksort($meta);
    ksort($stored);
    if ($meta !== $stored) {
      $metaDB->db[$key] = $meta;
      $metadataDirty = true;
    }

    $items[] = array(
      'filename' => $filename,
      'thumbnail' => $item['thumbnail'],
      'alt' => $meta['alt'],
      'width' => (int) $meta['width'],
      'height' => (int) $meta['height'],
      'bytes' => (int) $meta['bytes'],
      'mime' => $meta['mime'],
    );
  }

  if ($metadataDirty) {
    $metaDB->save();
  }

  return array('files' => $items, 'numberOfPages' => $numberOfPages);
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
