<?php defined('BLUDIT') or die('Bludit CMS.');

/*
 *	Global functions
 *	These functions provide connectivity between different objects and databases.
 *	These functions should provide different checks and logic before add/edit/delete into the databases.
 *
 *	For example, the creation of a user should check:
 *	- if the user already exists
 *	- if the username is not empty
 *	- if the password match with the security rules such as min length
*/

/**
 * Create a new page. === bludit v4
 * @param       array           $args       All supported parameters are defined in the class pages.class.php, variable $dbFields
 * @return      string|bool                 Returns the page key on successful create, FALSE otherwise
 */
function createPage($args) {
    global $pages;
    global $syslog;

    // The user is always the one logged
    $args['username'] = Session::get('username');
    if (empty($args['username'])) {
        Log::set(__FUNCTION__.LOG_SEP.'Empty username.', LOG_TYPE_ERROR);
        return false;
    }

    $key = $pages->add($args);
    if ($key) {
        // Call the plugins after page created
        execPluginsByHook('afterPageCreate', array($key));

        // Reindex categories and tags
        reindexCategories();
        reindexTags();

        // Add to syslog
        $syslog->add(array(
            'dictionaryKey'=>'new-content-created',
            'notes'=>(empty($args['title'])?$key:$args['title'])
        ));

        Log::set(__FUNCTION__.LOG_SEP.'Page created.', LOG_TYPE_INFO);
        return $key;
    }

    Log::set(__FUNCTION__.LOG_SEP.'Something happened when you tried to create the page.', LOG_TYPE_ERROR);
    deletePage(array('key'=>$key));
    return false;
}

/**
 * Edit a page. === Bludit v4
 * @param		array		$args			All supported parameters are defined in the class pages.class.php, variable $dbFields
 * @param		string		$args['key']	The key of the page to be edited
 * @return		string|bool					Returns the page key on successful edit, FALSE otherwise
 */
function editPage($args) {
    global $pages;
    global $syslog;

    // Check if the key is not empty
    if (empty($args['key'])) {
        Log::set(__FUNCTION__.LOG_SEP.'Empty page key.', LOG_TYPE_ERROR);
        return false;
    }

    // Check if the page key exist
    if (!$pages->exists($args['key'])) {
        Log::set(__FUNCTION__.LOG_SEP.'Page key doesn\'t exist: '.$args['key'], LOG_TYPE_ERROR);
        return false;
    }

    // Call the plugins before the page is edited
    execPluginsByHook('beforePageModify', array($args['key']));

    $key = $pages->edit($args);
    if ($key) {
        // Call the plugins after page modified
        execPluginsByHook('afterPageModify', array($key));

        // Reindex categories and tags
        reindexCategories();
        reindexTags();

        // Add to syslog
        $syslog->add(array(
            'dictionaryKey'=>'content-edited',
            'notes'=>empty($args['title'])?$key:$args['title']
        ));

        Log::set(__FUNCTION__.LOG_SEP.'Page edited.', LOG_TYPE_INFO);
        return $key;
    }

    Log::set(__FUNCTION__.LOG_SEP.'Something happened when you tried to edit the page.', LOG_TYPE_ERROR);
    return false;
}

/**
 * Delete a page. === Bludit v4
 * @param		array		$args			[string $key]
 * @return		string|bool					Returns the page key on successful delete, FALSE otherwise
 */
function deletePage($args) {
    global $pages;
    global $syslog;

    // Call the plugins before the page is deleted
    execPluginsByHook('beforePageDelete', array($args['key']));

    if ($pages->delete($args['key'])) {
        // Call the plugins after page deleted
        execPluginsByHook('afterPageDelete', array($args['key']));

        // Reindex categories and tags
        reindexCategories();
        reindexTags();

        // Add to syslog
        $syslog->add(array(
            'dictionaryKey'=>'content-deleted',
            'notes'=>$args['key']
        ));

        Log::set(__FUNCTION__.LOG_SEP.'Page deleted.', LOG_TYPE_INFO);
        return true;
    }

    Log::set(__FUNCTION__.LOG_SEP.'Something happened when you tried to delete the page.', LOG_TYPE_ERROR);
    return false;
}

/**
 * Create a new category. === Bludit v4
 * @param		array		$args			[string $name, string $template, string $description]
 * @return		string|bool					Returns the category key on successful create, FALSE otherwise
 */
function createCategory($args) {
    global $categories;
    global $syslog;

    if (Text::isEmpty($args['name'])) {
        Log::set(__FUNCTION__.LOG_SEP.'The category name is empty.', LOG_TYPE_ERROR);
        return false;
    }

    $key = $categories->add($args);
    if ($key) {
        $syslog->add(array(
            'dictionaryKey'=>'new-category-created',
            'notes'=>$args['name']
        ));

        Log::set(__FUNCTION__.LOG_SEP.'Category created.', LOG_TYPE_INFO);
        return $key;
    }

    Log::set(__FUNCTION__.LOG_SEP.'The category already exists or some issue saving the database.', LOG_TYPE_ERROR);
    return false;
}

/**
 * Edit a category. === Bludit v4
 * @param		array		$args			[string $key, string $name, string $friendlyURL, string $template, string $description]
 * @return		string|bool					Returns the category key on successful edit, FALSE otherwise
 */
function editCategory($args) {
    global $pages;
    global $categories;
    global $syslog;

    if (Text::isEmpty($args['key'])) {
        Log::set(__FUNCTION__.LOG_SEP.'The category key is empty.', LOG_TYPE_ERROR);
        return false;
    }

    if (Text::isEmpty($args['name'])) {
        Log::set(__FUNCTION__.LOG_SEP.'The category name is empty.', LOG_TYPE_ERROR);
        return false;
    }

    if (Text::isEmpty($args['friendlyURL'])) {
        Log::set(__FUNCTION__.LOG_SEP.'The category friendlyURL is empty.', LOG_TYPE_ERROR);
        return false;
    }

    $args['oldKey'] = $args['key'];
    $args['newKey'] = $args['friendlyURL'];
    $finalKey = $categories->edit($args);

    if ($finalKey==false) {
        Log::set(__FUNCTION__.LOG_SEP.'The category already exists.', LOG_TYPE_ERROR);
        return false;
    }

    // Re-link all pages with the new category key
    if ($args['key']!==$finalKey) {
        $pages->changeCategory($args['key'], $finalKey);
    }

    $syslog->add(array(
        'dictionaryKey'=>'category-edited',
        'notes'=>$finalKey
    ));

    Log::set(__FUNCTION__.LOG_SEP.'Category edited.', LOG_TYPE_INFO);
    return $finalKey;
}

/**
 * Delete a category. === Bludit v4
 * @param		array		$args			[string $key]
 * @return		string|bool					Returns TRUE on successful delete, FALSE otherwise
 */
function deleteCategory($args) {
    global $categories;
    global $syslog;

    if (Text::isEmpty($args['key'])) {
        Log::set(__FUNCTION__.LOG_SEP.'The category key is empty.', LOG_TYPE_ERROR);
        return false;
    }

    if ($categories->remove($args['key'])===false) {
        Log::set(__FUNCTION__.LOG_SEP.'Something happened when you tried to delete the category.', LOG_TYPE_ERROR);
        return false;
    }

    $syslog->add(array(
        'dictionaryKey'=>'category-deleted',
        'notes'=>$args['key']
    ));

    Log::set(__FUNCTION__.LOG_SEP.'Category deleted.', LOG_TYPE_INFO);
    return true;
}

/**
 * Create an user. === Bludit v4
 * This function should check everthing, such as empty username, empty password, password lenght, etc
 * @param array $args All supported parameters are defined in the class users.class.php variable $dbFields
 * @return string|bool Returns the username on successful create, FALSE otherwise
 */
function createUser($args) {
    global $users;
    global $syslog;

    $args['username'] = Text::removeSpecialCharacters($args['username']);

    if (Text::isEmpty($args['username'])) {
        Log::set(__FUNCTION__.LOG_SEP.'Empty username.', LOG_TYPE_ERROR);
        return false;
    }

    if (Text::length($args['password']) < PASSWORD_LENGTH) {
        Log::set(__FUNCTION__.LOG_SEP.'The password is to short.', LOG_TYPE_ERROR);
        return false;
    }

    $key = $users->add($args);
    if ($key) {
        $syslog->add(array(
            'dictionaryKey'=>'new-user-created',
            'notes'=>$args['username']
        ));

        Log::set(__FUNCTION__.LOG_SEP.'User created.', LOG_TYPE_INFO);
        return true;
    }

    Log::set(__FUNCTION__.LOG_SEP.'The user already exists or some issue saving the database.', LOG_TYPE_ERROR);
    return false;
}

/**
 * Edit an user. === Bludit v4
 * @param		array		$args				All supported parameters are defined in the class users.class.php, variable $dbFields
 * @param		bool		$args['disable']	If you set this variable the user will be disabled
 * @param		string		$args['password']	If you set this variable a new password will be set for the user
 * @return		string|bool						Returns TRUE on successful delete, FALSE otherwise
 */
function editUser($args) {
    global $users;
    global $syslog;

    if (Text::isEmpty($args['username'])) {
        Log::set(__FUNCTION__.LOG_SEP.'Empty username.', LOG_TYPE_ERROR);
        return false;
    }

    if (!$users->exists($args['username'])) {
        Log::set(__FUNCTION__.LOG_SEP.'Username doesn\'t exist.', LOG_TYPE_ERROR);
        return false;
    }

    // Disable the user
    // Your should pass the argument 'disable'
    if (isset($args['disable'])) {
        if (Session::get('role')!=='admin') {
            Log::set(__FUNCTION__.LOG_SEP.'Only the administrator can disable users.', LOG_TYPE_ERROR);
            return false;
        }

        $key = $users->disableUser($args['username']);
        if ($key) {
            Log::set(__FUNCTION__.LOG_SEP.'User disabled.', LOG_TYPE_INFO);
            return $key;
        }
    }

    $key = $users->edit($args);
    if ($key) {
        $syslog->add(array(
            'dictionaryKey'=>'user-edited',
            'notes'=>$args['username']
        ));

        Log::set(__FUNCTION__.LOG_SEP.'User edited.', LOG_TYPE_INFO);
        return $key;
    }

    Log::set(__FUNCTION__.LOG_SEP.'An error occurred while trying to edit the user.', LOG_TYPE_ERROR);
    return false;
}

/*	Upload a profile picture === Bludit v4
    The profile picture is store in PATH_UPLOADS_PROFILES.$username.png

    @username		string		Username
    @_FILE			array		https://www.php.net/manual/en/reserved.variables.files.php
    @return			array
*/
function uploadProfilePicture($username) {
    if (!isset($_FILES['file'])) {
        Log::set(__FUNCTION__.LOG_SEP.'File not sent.', LOG_TYPE_ERROR);
        return false;
    }

    if ($_FILES['file']['error'] != 0) {
        Log::set(__FUNCTION__.LOG_SEP.'Error uploading the file.', LOG_TYPE_ERROR);
        return false;
    }

    // Check path traversal
    if (Text::stringContains($username, DS, false)) {
        Log::set(__FUNCTION__.LOG_SEP.'Path traversal detected.', LOG_TYPE_ERROR);
        return false;
    }

    // Check file extension
    $fileExtension = Filesystem::extension($_FILES['file']['name']);
    $fileExtension = Text::lowercase($fileExtension);
    if (!in_array($fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSIONS']) ) {
        Log::set(__FUNCTION__.LOG_SEP.'Image type is not supported.', LOG_TYPE_ERROR);
        return false;
    }

    // Check file MIME Type
    $fileMimeType = Filesystem::mimeType($_FILES['file']['tmp_name']);
    if ($fileMimeType!==false) {
        if (!in_array($fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'])) {
            Log::set(__FUNCTION__.LOG_SEP.'Image mime type is not supported.', LOG_TYPE_ERROR);
            return false;
        }
    }

    // Move the image from PHP tmp folder to Bludit tmp folder
    $filename = $username.'.'.$fileExtension;
    Filesystem::mv($_FILES['file']['tmp_name'], PATH_TMP.$filename);

    $finalFilename = $username.'.png';
    $absolutePath = PATH_UPLOADS_PROFILES.$finalFilename;
    $absoluteURL = DOMAIN_UPLOADS_PROFILES.$finalFilename;

    try {
        $image = new \claviska\SimpleImage();
        $image
            ->fromFile(PATH_TMP.$filename)
            ->autoOrient()
            ->thumbnail(PROFILE_IMG_WIDTH, PROFILE_IMG_HEIGHT, 'center')
            ->toFile($absolutePath, 'image/png');
    } catch(Exception $e) {
        Log::set(__FUNCTION__.LOG_SEP.$e->getMessage(), LOG_TYPE_ERROR);
        return false;
    }

    Log::set(__FUNCTION__.LOG_SEP.'Image profile uploaded to the user.', LOG_TYPE_INFO);
    return array(
        'filename'=>$filename,
        'absolutePath'=>$absolutePath,
        'absoluteURL'=>$absoluteURL,
        'mime'=>Filesystem::mimeType($absolutePath),
        'size'=>Filesystem::getSize($absolutePath)
    );
}

/*	Delete a profile picture === Bludit v4

    @username		string		Username
    @return			bool		Returns TRUE on successful delete, FALSE otherwise
*/
function deleteProfilePicture($username) {
    // Check path traversal
    if (Text::stringContains($username, DS, false)) {
        Log::set(__FUNCTION__.LOG_SEP.'Path traversal detected.', LOG_TYPE_ERROR);
        return false;
    }

    $finalFilename = $username.'.png';
    $absolutePath = PATH_UPLOADS_PROFILES.$finalFilename;

    if (Sanitize::pathFile($absolutePath)) {
        Filesystem::rmfile($absolutePath);
        Log::set(__FUNCTION__.LOG_SEP.'Profile picture deleted.', LOG_TYPE_INFO);
        return true;
    }

    Log::set(__FUNCTION__.LOG_SEP.'Error when try to delete the profile picture, the file doesn\'t exists.', LOG_TYPE_ERROR);
    return false;
}

/*	Upload the site logo === Bludit v4
    The site logo is store in PATH_UPLOADS/<site title>.<extension>

    @_FILE			array		https://www.php.net/manual/en/reserved.variables.files.php
    @return			array
*/
function uploadSiteLogo() {
    global $site;

    if (!isset($_FILES['file'])) {
        Log::set(__FUNCTION__.LOG_SEP.'File not sent.', LOG_TYPE_ERROR);
        return false;
    }

    if ($_FILES['file']['error'] != 0) {
        Log::set(__FUNCTION__.LOG_SEP.'Error uploading the file.', LOG_TYPE_ERROR);
        return false;
    }

    // Check path traversal
    if (Text::stringContains($_FILES['file']['name'], DS, false)) {
        Log::set(__FUNCTION__.LOG_SEP.'Path traversal detected.', LOG_TYPE_ERROR);
        return false;
    }

    // Check file extension
    $fileExtension = Filesystem::extension($_FILES['file']['name']);
    $fileExtension = Text::lowercase($fileExtension);
    if (!in_array($fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSIONS']) ) {
        Log::set(__FUNCTION__.LOG_SEP.'Image type is not supported.', LOG_TYPE_ERROR);
        return false;
    }

    // Check file MIME Type
    $fileMimeType = Filesystem::mimeType($_FILES['file']['tmp_name']);
    if ($fileMimeType!==false) {
        if (!in_array($fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'])) {
            Log::set(__FUNCTION__.LOG_SEP.'Image mime type is not supported.', LOG_TYPE_ERROR);
            return false;
        }
    }

    // Final filename
    $filename = 'logo.'.$fileExtension;
    $siteTitle = Text::cleanUrl($site->title(), '-', $allowExtremeFriendlyURL=false);
    if (Text::isNotEmpty($siteTitle)) {
        $filename = $siteTitle.'.'.$fileExtension;
    }

    // Move the image from PHP tmp folder to Bludit tmp folder
    Filesystem::mv($_FILES['file']['tmp_name'], PATH_TMP.$filename);

    $absolutePath = PATH_UPLOADS.$filename;
    $absoluteURL = DOMAIN_UPLOADS.$filename;

    try {
        $image = new \claviska\SimpleImage();
        $image
            ->fromFile(PATH_TMP.$filename)
            ->autoOrient()
            ->thumbnail(PROFILE_IMG_WIDTH, PROFILE_IMG_HEIGHT, 'center')
            ->toFile($absolutePath, 'image/png');
    } catch(Exception $e) {
        Log::set(__FUNCTION__.LOG_SEP.$e->getMessage(), LOG_TYPE_ERROR);
        return false;
    }

    $site->set(array('logo'=>$filename));

    Log::set(__FUNCTION__.LOG_SEP.'Site logo uploaded and cropped.', LOG_TYPE_INFO);
    return array(
        'filename'=>$filename,
        'absolutePath'=>$absolutePath,
        'absoluteURL'=>$absoluteURL,
        'mime'=>Filesystem::mimeType($absolutePath),
        'size'=>Filesystem::getSize($absolutePath)
    );
}

/*	Delete the site logo === Bludit v4

    @return			bool		Returns TRUE on successful delete, FALSE otherwise
*/
function deleteSiteLogo() {
    global $site;

    if ($site->logo()) {
        Filesystem::rmfile(PATH_UPLOADS.$site->logo(false));
        $site->set(array('logo'=>''));
        Log::set(__FUNCTION__.LOG_SEP.'Site logo deleted.', LOG_TYPE_INFO);
        return true;
    }

    Log::set(__FUNCTION__.LOG_SEP.'Error when try to delete the site logo, the file doesn\'t exists.', LOG_TYPE_ERROR);
    return false;
}

/*	Upload a file to a page === Bludit v4
    The files is saved in

    @pageKey		string		Page key
    @_FILE			array		https://www.php.net/manual/en/reserved.variables.files.php

    @return			array
*/
function uploadPageFile($pageKey) {
    global $site;

    if (!isset($_FILES['file'])) {
        Log::set(__FUNCTION__.LOG_SEP.'File not sent.', LOG_TYPE_ERROR);
        return false;
    }

    if ($_FILES['file']['error'] != 0) {
        Log::set(__FUNCTION__.LOG_SEP.'Error uploading the file.', LOG_TYPE_ERROR);
        return false;
    }

    // Check path traversal
    if (Text::stringContains($pageKey, DS, false)) {
        Log::set(__FUNCTION__.LOG_SEP.'Path traversal detected.', LOG_TYPE_ERROR);
        return false;
    }

    // Check file extension
    $fileExtension = Filesystem::extension($_FILES['file']['name']);
    $fileExtension = Text::lowercase($fileExtension);
    if (!in_array($fileExtension, $GLOBALS['ALLOWED_FILE_EXTENSIONS']) ) {
        Log::set(__FUNCTION__.LOG_SEP.'File type is not supported.', LOG_TYPE_ERROR);
        return false;
    }

    // Check file MIME Type
    $fileMimeType = Filesystem::mimeType($_FILES['file']['tmp_name']);
    if ($fileMimeType!==false) {
        if (!in_array($fileMimeType, $GLOBALS['ALLOWED_FILE_MIMETYPES'])) {
            Log::set(__FUNCTION__.LOG_SEP.'File mime type is not supported.', LOG_TYPE_ERROR);
            return false;
        }
    }

    $filename = Filesystem::filename($_FILES['file']['name']);
    $absoluteURL = DOMAIN_UPLOADS_PAGES.$pageKey.DS.$filename.'.'.$fileExtension;
    $absolutePath = PATH_UPLOADS_PAGES.$pageKey.DS.$filename.'.'.$fileExtension;
    // Move the original file to a page folder
    if (Filesystem::mv($_FILES['file']['tmp_name'], $absolutePath)) {
        // Change permissions files to rw-r-r
        Filesystem::chmod($absolutePath, 0644);
        // Create the thumbnails only if the file is an image
        $thumbnail = '';
        if (in_array($fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'])) {
            try {
                $image = new \claviska\SimpleImage();

                $thumbnailSmall = PATH_UPLOADS_PAGES.$pageKey.DS.$filename.'-thumbnail-s.'.$fileExtension;
                $image
                    ->fromFile($absolutePath)
                    ->thumbnail($site->thumbnailSmallWidth(), $site->thumbnailSmallHeight(), 'center')
                    ->toFile($thumbnailSmall, 'image/jpeg');

                $thumbnailMedium = PATH_UPLOADS_PAGES.$pageKey.DS.$filename.'-thumbnail-m.'.$fileExtension;
                $image
                    ->fromFile($absolutePath)
                    ->thumbnail($site->thumbnailMediumWidth(), $site->thumbnailMediumHeight(), 'center')
                    ->toFile($thumbnailMedium, 'image/jpeg');
            } catch(Exception $e) {
                Log::set(__FUNCTION__.LOG_SEP.$e->getMessage(), LOG_TYPE_ERROR);
                return false;
            }
        }

        Log::set(__FUNCTION__.LOG_SEP.'File uploaded to the page.', LOG_TYPE_INFO);
        return array(
            'filename'=>$filename.'.'.$fileExtension,
            'absolutePath'=>$absolutePath,
            'absoluteURL'=>$absoluteURL,
            'mime'=>Filesystem::mimeType($absolutePath),
            'size'=>Filesystem::getSize($absolutePath),
            'thumbnail'=>$thumbnail
        );
    }

    Log::set(__FUNCTION__.LOG_SEP.'Error uploading the file.', LOG_TYPE_ERROR);
    return false;
}

/**
 * Delete a file from a page.
 * @param       string           $pageKey   Page key
 * @param       string           $file      Filename to delete, filename and extension without path.
 * @return      bool                        Returns the page key on successful create, FALSE otherwise
 */
function deletePageFile($pageKey, $file) {
    if (Text::stringContains($pageKey, DS, false)) {
        Log::set(__FUNCTION__.LOG_SEP.'Path traversal detected.', LOG_TYPE_ERROR);
        return false;
    }

    $fileName = Filesystem::filename($file);
    $fileExtension = Filesystem::extension($file);
    $fileExtension = Text::lowercase($fileExtension);

    Filesystem::rmfile(PATH_UPLOADS_PAGES.$pageKey.DS.$fileName.'.'.$fileExtension);
    Filesystem::rmfile(PATH_UPLOADS_PAGES.$pageKey.DS.$fileName.'-thumbnail-s.'.$fileExtension);
    Filesystem::rmfile(PATH_UPLOADS_PAGES.$pageKey.DS.$fileName.'-thumbnail-m.'.$fileExtension);

    return true;
}

/*	Install and activate a plugin === Bludit v4

    @className			string			The plugin PHP class name
    @return				string/bool		Returns TRUE on successful install, FALSE otherwise
*/
function activatePlugin($className) {
    global $plugins;
    global $syslog;

    if (!isset($plugins['all'][$className])) {
        Log::set(__FUNCTION__.LOG_SEP.'The plugin doesn\'t exist: '.$className, LOG_TYPE_ERROR);
        return false;
    }

    $plugin = $plugins['all'][$className];
    if ($plugin->install()) {
        $syslog->add(array(
            'dictionaryKey'=>'plugin-activated',
            'notes'=>$plugin->name()
        ));
        Log::set(__FUNCTION__.LOG_SEP.'Plugin installed.', LOG_TYPE_INFO);
        return true;
    }

    Log::set(__FUNCTION__.LOG_SEP.'It was not possible to install the plugin:'.$className, LOG_TYPE_ERROR);
    return false;
}

/*	Uninstall and deactivate a plugin === Bludit v4

    @className			string			The plugin PHP class name
    @return				string/bool		Returns TRUE on successful install, FALSE otherwise
*/
function deactivatePlugin($className) {
    global $plugins;
    global $syslog;

    if (!isset($plugins['all'][$className])) {
        Log::set(__FUNCTION__.LOG_SEP.'The plugin doesn\'t exist: '.$className, LOG_TYPE_ERROR);
        return false;
    }

    $plugin = $plugins['all'][$className];
    if ($plugin->uninstall()) {
        $syslog->add(array(
            'dictionaryKey'=>'plugin-deactivated',
            'notes'=>$plugin->name()
        ));
        Log::set(__FUNCTION__.LOG_SEP.'Plugin uninstalled.', LOG_TYPE_INFO);
        return true;
    }

    Log::set(__FUNCTION__.LOG_SEP.'It was not possible to uninstall the plugin:'.$className, LOG_TYPE_ERROR);
    return false;
}

/*	Change plugins settings === Bludit v4

    @args				array			The array $args supports all the keys from the plugin database
    @return				bool			Returns TRUE on successful configure, FALSE otherwise
*/
function configurePlugin($args) {
    global $plugins;
    global $syslog;

    // Plugin class name
    $className = $args['className'];

    // Check if the plugin exists
    if (!isset($plugins['all'][$className])) {
        Log::set(__FUNCTION__.LOG_SEP.'The plugin doesn\'t exist: '.$className, LOG_TYPE_ERROR);
        return false;
    }

    $plugin = $plugins['all'][$className];
    $plugin->configure($args);
    $syslog->add(array(
        'dictionaryKey'=>'plugin-configured',
        'notes'=>$plugin->name()
    ));
    Log::set(__FUNCTION__.LOG_SEP.'Plugin configured: '.$className, LOG_TYPE_INFO);
    return true;
}

// Re-index database of categories
// If you create/edit/remove a page is necessary regenerate the database of categories
function reindexCategories() {
    global $categories;
    return $categories->reindex();
}

// Re-index database of tags
// If you create/edit/remove a page is necessary regenerate the database of tags
function reindexTags() {
    global $tags;
    return $tags->reindex();
}

// Generate the page 404 Not found
function buildErrorPage() {
    global $site;
    global $L;

    try {
        $pageNotFoundKey = $site->pageNotFound();
        $pageNotFound = New Page($pageNotFoundKey);
    } catch (Exception $e) {
        $pageNotFound = New Page(false);
        $pageNotFound->setField('title', 	$L->get('page-not-found'));
        $pageNotFound->setField('content', 	$L->get('page-not-found-content'));
        $pageNotFound->setField('username', 	'admin');
    }

    return $pageNotFound;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
// This function generate a particular page from the current slug of the url
// If the slug has not a page associated returns FALSE and set not-found as true
function buildThePage() {
    global $url;

    try {
        $pageKey = $url->slug();
        $page = New Page($pageKey);
    } catch (Exception $e) {
        $url->setNotFound();
        return false;
    }

    if ($page->draft() || $page->scheduled() || $page->autosave()) {
        if ($url->parameter('preview')!==md5($page->uuid())) {
            $url->setNotFound();
            return false;
        }
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
    global $pages;
    global $categories;
    global $tags;
    global $site;
    global $url;

    // Get the page number from URL
    $pageNumber = $url->pageNumber();

    if ($for=='home') {
        $onlyPublished = true;
        $numberOfItems = $site->itemsPerPage();
        $list = $pages->getList($pageNumber, $numberOfItems, $onlyPublished);

        // Include sticky pages only in the first page
        if ($pageNumber==1) {
            $sticky = $pages->getStickyDB();
            $list = array_merge($sticky, $list);
        }
    }
    elseif ($for=='category') {
        $numberOfItems = $site->itemsPerPage();
        $list = $categories->getList($categoryKey, $pageNumber, $numberOfItems);
    }
    elseif ($for=='tag') {
        $numberOfItems = $site->itemsPerPage();
        $list = $tags->getList($tagKey, $pageNumber, $numberOfItems);
    }

    // There are not items, invalid tag, invalid category, out of range, etc...
    if ($list===false) {
        $url->setNotFound();
        return false;
    }

    $content = array();
    foreach ($list as $pageKey) {
        try {
            $page = new Page($pageKey);
            if ( 	($page->type()=='published') ||
                ($page->type()=='sticky') ||
                ($page->type()=='static')
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
function buildStaticPages() {
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
function buildPage($pageKey) {
    try {
        $page = new Page($pageKey);
        return $page;
    } catch (Exception $e) {
        return false;
    }
}

// Returns an array with all the parent pages as Page-Object
// The pages are order by the settings on the system
function buildParentPages() {
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
function getPlugin($pluginClassName) {
    global $plugins;

    if (isPluginActive($pluginClassName)) {
        return $plugins['all'][$pluginClassName];
    }
    return false;
}

/**
 * Returns True if the plugin is installed
 *
 * @param string        $pluginClassName        Plugin class name
 * @return boolean
 */
function isPluginActive(string $pluginClassName): bool {
    global $plugins;

    if (isset($plugins['all'][$pluginClassName])) {
        return $plugins['all'][$pluginClassName]->installed();
    }
    return false;
}


function deactivateAllPlugin() {
    global $plugins;
    global $syslog;
    global $L;

    // Check if the plugin exists
    foreach ($plugins['all'] as $plugin) {
        if ($plugin->uninstall()) {
            // Add to syslog
            $syslog->add(array(
                'dictionaryKey'=>'plugin-deactivated',
                'notes'=>$plugin->name()
            ));
        }
    }
    return false;
}



// Execute the plugins by hook
function execPluginsByHook($hook, $args = array()) {
    global $plugins;
    foreach ($plugins[$hook] as $plugin) {
        echo call_user_func_array(array($plugin, $hook), $args);
    }
}





function deleteUser($args) {
    global $users, $pages;
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
    if (!$users->exists($username)) {
        return false;
    }

    if ($deleteContent) {
        $pages->deletePagesByUser(array('username'=>$username));
    } else {
        $pages->transferPages(array('oldUsername'=>$username));
    }

    if ($users->delete($username)) {
        // Add to syslog
        $syslog->add(array(
            'dictionaryKey'=>'user-deleted',
            'notes'=>$username
        ));

        return true;
    }

    return false;
}



function editSettings($args) {
    global $site;
    global $syslog;
    global $L;
    global $pages;

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
        $args['extremeFriendly'] = (($args['extremeFriendly']=='true')?true:false);
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
        if ($site->orderBy()!=ORDER_BY) {
            if ($site->orderBy()=='date') {
                $pages->sortByDate();
            } else {
                $pages->sortByPosition();
            }
            $pages->save();
        }

        // Add syslog
        $syslog->add(array(
            'dictionaryKey'=>'settings-changes',
            'notes'=>''
        ));

        return true;
    }

    return false;
}


// Returns true if the user is allowed to proceed
function checkRole($allowRoles, $redirect=true) {
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
            'dictionaryKey'=>'access-denied',
            'notes'=>$login->username()
        ));

        Alert::set($L->g('You do not have sufficient permissions'));
        Redirect::page('dashboard');
    }
    return false;
}







// Returns an array with all the categories
// By default, the database of categories is alphanumeric sorted
function getCategories() {
    global $categories;

    $list = array();
    foreach ($categories->keys() as $key) {
        $category = new Category($key);
        array_push($list, $category);
    }
    return $list;
}

// Returns the object category if the category exists, FALSE otherwise
function getCategory($key) {
    try {
        $category = new Category($key);
        return $category;
    } catch (Exception $e) {
        return false;
    }
}

// Returns an array with all the tags
// By default, the database of tags is alphanumeric sorted
function getTags() {
    global $tags;

    $list = array();
    foreach ($tags->db as $key=>$fields) {
        $tag = new Tag($key);
        array_push($list, $tag);
    }
    return $list;
}

// Returns the object tag if the tag exists, FALSE otherwise
function getTag($key) {
    try {
        $tag = new Tag($key);
        return $tag;
    } catch (Exception $e) {
        return false;
    }
}

// Activate a theme
function activateTheme($themeDirectory) {
    global $site;
    global $syslog;
    global $L;

    if (Sanitize::pathFile(PATH_THEMES.$themeDirectory)) {
        // Disable current theme
        $currentTheme = $site->theme();
        deactivatePlugin($currentTheme);

        // Install new theme
        if (Filesystem::fileExists(PATH_THEMES.$themeDirectory.DS.'install.php')) {
            include_once(PATH_THEMES.$themeDirectory.DS.'install.php');
        }

        activatePlugin($themeDirectory);

        $site->set(array('theme'=>$themeDirectory));

        $syslog->add(array(
            'dictionaryKey'=>'new-theme-configured',
            'notes'=>$themeDirectory
        ));

        Alert::set( $L->g('The changes have been saved') );
        return true;
    }
    return false;
}

function ajaxResponse($status=0, $message="", $data=array()) {
    $default = array('status'=>$status, 'message'=>$message);
    $output = array_merge($default, $data);
    exit (json_encode($output));
}



function downloadRestrictedFile($file) {
    if (is_file($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit(0);
    }
}
