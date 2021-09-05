<?php

class pluginAPI extends Plugin {

	private $method;

	public function init()
	{
		// Generate the API Token
		$token = $this->generateToken();

		$this->dbFields = array(
			'token'=>$token,	// API Token
			'numberOfItems'=>15	// Amount of items to return
		);
	}

	public function generateToken()
	{
		return md5( uniqid().time().DOMAIN );
	}

	public function token()
	{
		return $this->getValue('token');
	}

	public function newToken()
	{
		$this->db['token'] = $this->generateToken();
		$this->save();
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('URL').'</label>';
		$html .= '<p class="text-muted">'.DOMAIN_BASE.'api/{endpoint}</p>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('API Token').'</label>';
		$html .= '<input name="token" type="text" value="'.$this->getValue('token').'">';
		$html .= '<span class="tip">'.$L->get('This token is for read only and is regenerated every time you install the plugin').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Amount of pages').'</label>';
		$html .= '<input id="jsnumberOfItems" name="numberOfItems" type="text" value="'.$this->getValue('numberOfItems').'">';
		$html .= '<span class="tip">'.$L->get('This is the maximum of pages to return when you call to').'</span>';
		$html .= '</div>';

		return $html;
	}


// API HOOKS
// ----------------------------------------------------------------------------

	public function beforeAll()
	{
		global $url;
		global $pages;
		global $users;

		// CHECK URL
		// ------------------------------------------------------------
		$URI = $this->webhook('api', $returnsAfterURI=true, $fixed=false);
		if ($URI===false) {
			return false;
		}

		// METHOD
		// ------------------------------------------------------------
		$method = $this->getMethod();

		// METHOD INPUTS
		// ------------------------------------------------------------
		$inputs = $this->getMethodInputs();
		if (empty($inputs)) {
			$this->response(400, 'Bad Request', array('message'=>'Missing method inputs.'));
		}

		// ENDPOINT PARAMETERS
		// ------------------------------------------------------------
		$parameters = $this->getEndpointParameters($URI);
		if (empty($parameters)) {
			$this->response(400, 'Bad Request', array('message'=>'Missing endpoint parameters.'));
		}

		$parmA = isset($parameters[0])?$parameters[0]:'';
		$parmB = isset($parameters[1])?$parameters[1]:'';
		$parmC = isset($parameters[2])?$parameters[2]:'';
		$parmD = isset($parameters[3])?$parameters[3]:'';

		// API TOKEN
		// ------------------------------------------------------------
		// Token from the plugin, the user can change it on the settings of the plugin
		$tokenAPI = $this->getValue('token');

		// Check empty token
		if (empty($inputs['token'])) {
			$this->response(400, 'Bad Request', array('message'=>'Missing API token.'));
		}

		// Check if the token is valid
		if ($inputs['token']!==$tokenAPI) {
			$this->response(401, 'Unauthorized', array('message'=>'Invalid API token.'));
		}

		// AUTHENTICATION TOKEN
		// ------------------------------------------------------------
		$writePermissions = false;
		if (!empty($inputs['authentication'])) {

			// Get the user with the authentication token, FALSE if doesn't exit
			$username = $users->getByAuthToken($inputs['authentication']);
			if ($username!==false) {
				try {
					$user = new User($username);
					if (($user->role()=='admin') && ($user->enabled())) {
						// Loggin the user to create the session
						$login = new Login();
						$login->setLogin($username, 'admin', $user->tokenAuth());
						// Enable write permissions
						$writePermissions = true;
					}
				} catch (Exception $e) {
					// Continue without permissions
				}
			}
		}

		// Clean inputs
		// ------------------------------------------------------------
		unset($inputs['token']);
		unset($inputs['authentication']);

		// ENDPOINTS
		// ------------------------------------------------------------

		// /api/pages
		// /api/pages/files
		// /api/pages/files/:key
		// /api/pages/files/:parent/:key
		// /api/pages/:key
		// /api/pages/:parent/:key

		// (GET) /api/pages/files/:key
		if ( ($method==='GET') && ($parmA==='pages') && ($parmB==='files') && !empty($parmC) ) {
			$key = $parmC;
			if (!empty($parmD)) {
				$key = $parmC.'/'.$parmD;
			}
			$data = $this->getFiles($key);
		}
		// (POST) /api/pages/files/:key
		elseif ( ($method==='POST') && ($parmA==='pages') && ($parmB==='files') && !empty($parmC) && $writePermissions ) {
			$key = $parmC;
			if (!empty($parmD)) {
				$key = $parmC.'/'.$parmD;
			}
			$data = $this->uploadPageFile($key);
		}
		// (DELETE) /api/pages/files/:key
		elseif ( ($method==='DELETE') && ($parmA==='pages') && ($parmB==='files') && !empty($parmC) && $writePermissions ) {
			$key = $parmC;
			if (!empty($parmD)) {
				$key = $parmC.'/'.$parmD;
			}
			// Delete file function
		}
		// (GET) /api/pages/:key
		elseif ( ($method==='GET') && ($parmA==='pages') && !empty($parmB) ) {
			$key = $parmB;
			if (!empty($parmC)) {
				$key = $parmB.'/'.$parmC;
			}
			$data = $this->getPage($key);
		}
		// (POST) /api/pages
		elseif ( ($method==='POST') && ($parmA==='pages') && empty($parmB) && $writePermissions ) {
			$data = $this->createPage($inputs);
		}
		// (PUT) /api/pages/:key
		elseif ( ($method==='PUT') && ($parmA==='pages') && !empty($parmB) && $writePermissions ) {
			$inputs['key'] = $parmB;
			if (!empty($parmC)) {
				$inputs['key'] = $parmB.'/'.$parmC;
			}
			$data = $this->editPage($inputs);
		}
		// (DELETE) /api/pages/:key
		elseif ( ($method==='DELETE') && ($parmA==='pages') && !empty($parmB) && $writePermissions ) {
			$key = $parmB;
			if (!empty($parmC)) {
				$key = $parmB.'/'.$parmC;
			}
			$data = $this->deletePage(array('key'=>$key));
		}
		// (GET) /api/settings
		elseif ( ($method==='GET') && ($parmA==='settings') && empty($parmB) && $writePermissions ) {
			$data = $this->getSettings();
		}
		// (PUT) /api/settings
		elseif ( ($method==='PUT') && ($parmA==='settings') && empty($parmB) && $writePermissions ) {
			$data = $this->editSettings($inputs);
		}
		// (POST) /api/settings/logo
		elseif ( ($method==='POST') && ($parmA==='settings') && ($parmB==='logo') && $writePermissions ) {
			$data = $this->uploadSiteLogo($inputs);
		}
		// (DELETE) /api/settings/logo
		elseif ( ($method==='DELETE') && ($parmA==='settings') && ($parmB==='logo') && $writePermissions ) {
			$data = $this->deleteSiteLogo();
		}
		// (GET) /api/tags
		elseif ( ($method==='GET') && ($parmA==='tags') && empty($parmB) ) {
			$data = $this->getTags();
		}
		// (GET) /api/tags/:key
		elseif ( ($method==='GET') && ($parmA==='tags') && !empty($parmB) ) {
			$key = $parmB;
			$data = $this->getTag($key);
		}
		// (GET) /api/categories
		elseif ( ($method==='GET') && ($parmA==='categories') && empty($parmB) ) {
			$data = $this->getCategories();
		}
		// (GET) /api/categories/:key
		elseif ( ($method==='GET') && ($parmA==='categories') && !empty($parmB) ) {
			$key = $parmB;
			$data = $this->getCategory($key);
		}
		// (POST) /api/categories
		elseif ( ($method==='POST') && ($parmA==='categories') && empty($parmB) && $writePermissions ) {
			$data = $this->createCategory($inputs);
		}
		// (PUT) /api/categories/:key
		elseif ( ($method==='PUT') && ($parmA==='categories') && !empty($parmB) && $writePermissions ) {
			$inputs['key'] = $parmB;
			$data = $this->editCategory($inputs);
		}
		// (DELETE) /api/categories/:key
		elseif ( ($method==='DELETE') && ($parmA==='categories') && !empty($parmB) && $writePermissions ) {
			$inputs['key'] = $parmB;
			$data = $this->deleteCategory($inputs);
		}
		// (GET) /api/users
		elseif ( ($method==='GET') && ($parmA==='users') && empty($parmB) ) {
			$data = $this->getUsers();
		}
		// (POST) /api/users
		elseif ( ($method==='POST') && ($parmA==='users') && empty($parmB) && $writePermissions ) {
			$data = $this->createUser($inputs);
		}
		// (POST) /api/users/picture/:username
		elseif ( ($method==='POST') && ($parmA==='users') && ($parmB==='picture') && !empty($parmC) && $writePermissions ) {
			$username = $parmC;
			$data = $this->uploadProfilePicture($username);
		}
		// (DELETE) /api/users/picture/:username
		elseif ( ($method==='DELETE') && ($parmA==='users') && ($parmB==='picture') && !empty($parmC) && $writePermissions ) {
			$username = $parmC;
			$data = $this->deleteProfilePicture($username);
		}
		// (GET) /api/users/:username
		elseif ( ($method==='GET') && ($parmA==='users') && !empty($parmB) ) {
			$username = $parmB;
			$data = $this->getUser($username);
		}
		// (PUT) /api/users/:username
		elseif ( ($method==='PUT') && ($parmA==='users') && !empty($parmB) ) {
			$inputs['username'] = $parmB;
			$data = $this->editUser($inputs);
		}
		// (POST) /api/plugins/:key
		elseif ( ($method==='POST') && ($parmA==='plugins') && !empty($parmB) ) {
			$pluginClassName = $parmB;
			$data = $this->activatePlugin($pluginClassName);
		}
		// (DELETE) /api/plugins/:key
		elseif ( ($method==='DELETE') && ($parmA==='plugins') && !empty($parmB) ) {
			$pluginClassName = $parmB;
			$data = $this->deactivatePlugin($pluginClassName);
		}
		// (PUT) /api/plugins/:key
		elseif ( ($method==='PUT') && ($parmA==='plugins') && !empty($parmB) ) {
			$inputs['className'] = $parmB;
			$data = $this->configurePlugin($inputs);
		}
		// (GET) /api/helper/:name
		elseif ( ($method==='GET') && ($parmA==='helper') && !empty($parmB) ) {
			$name = $parmB;
			if ($name=='friendly-url') {
				$data = $this->getFriendlyURL($inputs);
			}
		}
		else {
			$this->response(401, 'Unauthorized', array('message'=>'Access denied or invalid endpoint.'));
		}

		$this->response(200, 'OK', $data);
	}

// PRIVATE METHODS
// ----------------------------------------------------------------------------

	private function getMethod()
	{
		// METHODS
		// ------------------------------------------------------------
		// GET
		// POST
		// PUT
		// DELETE

		$this->method = $_SERVER['REQUEST_METHOD'];
		return $this->method;
	}

	private function getMethodInputs()
	{
		switch($this->method) {
			case "POST":
				$inputs = $_POST;
				break;
			case "GET":
			case "DELETE":
				$inputs = $_GET;
				break;
			case "PUT":
				$inputs = '';
				break;
			default:
				$inputs = json_encode(array());
				break;
		}

		// Try to get raw/json data
		if (empty($inputs)) {
			$inputs = file_get_contents('php://input');
		}

		return $this->cleanInputs($inputs);
	}

	// Returns an array with key=>value with the inputs
	// If the content is JSON is parsed to array
	private function cleanInputs($inputs)
	{
		$tmp = array();
		if (is_array($inputs)) {
			foreach ($inputs as $key=>$value) {
				$tmp[$key] = Sanitize::html($value);
			}
		} elseif (is_string($inputs)) {
			$tmp = json_decode($inputs, true);
			if (json_last_error()!==JSON_ERROR_NONE) {
				$tmp = array();
			}
		}

		return $tmp;
	}

	private function getEndpointParameters($URI)
	{
		// ENDPOINT Parameters
		// ------------------------------------------------------------
		// /api/pages 		| GET  | returns all pages
		// /api/pages/{key}	| GET  | returns the page with the {key}
		// /api/pages 		| POST | create a new page

		$URI = ltrim($URI, '/');
		$parameters = explode('/', $URI);

		// Sanitize parameters
		foreach ($parameters as $key=>$value) {
			$parameters[$key] = Sanitize::html($value);
		}

		return $parameters;
	}

	private function response($code=200, $message='OK', $data=array())
	{
		header('HTTP/1.1 '.$code.' '.$message);
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		$json = json_encode($data);
		exit($json);
	}

	private function getTags()
	{
		global $tags;
		$tmp = array(
			'status'=>'0',
			'message'=>'List of tags.',
			'data'=>array()
		);
		foreach ($tags->keys() as $key) {
			$tag = $tags->getMap($key);
			array_push($tmp['data'], $tag);
		}
		return $tmp;
	}

	// Returns the tag information and the pages releated to the tag
	// The array with the pages has the complete information of each page
	private function getTag($key)
	{
		try {
			$tag = new Tag($key);
		} catch (Exception $e) {
			return array(
				'status'=>'1',
				'message'=>'Tag not found by the key: '.$key
			);
		}

		$list = array();
		foreach ($tag->pages() as $pageKey) {
			try {
				$page = new Page($pageKey);
				array_push($list, $page->json($returnsArray=true));
			} catch (Exception $e){}
		}

		$data = $tag->json($returnsArray=true);
		$data['pages'] = $list;

		return array(
			'status'=>'0',
			'message'=>'Information about the tag and pages related.',
			'data'=>$data
		);
	}

	private function getPages($args)
	{
		global $pages;

		// Parameters and the default values
		$published 	= (isset($args['published'])?$args['published']=='true':true);
		$static 	= (isset($args['static'])?$args['static']=='true':false);
		$draft 		= (isset($args['draft'])?$args['draft']=='true':false);
		$sticky 	= (isset($args['sticky'])?$args['sticky']=='true':false);
		$scheduled 	= (isset($args['scheduled'])?$args['scheduled']=='true':false);
		$untagged 	= (isset($args['untagged'])?$args['untagged']=='true':false);

		$numberOfItems = (isset($args['numberOfItems'])?$args['numberOfItems']:10);
		$pageNumber = (isset($args['pageNumber'])?$args['pageNumber']:1);
		$list = $pages->getList($pageNumber, $numberOfItems, $published, $static, $sticky, $draft, $scheduled);

		$tmp = array(
			'status'=>'0',
			'message'=>'List of pages',
			'numberOfItems'=>$numberOfItems,
			'data'=>array()
		);

		foreach ($list as $pageKey) {
			try {
				// Create the page object from the page key
				$page = new Page($pageKey);
				if ($untagged) {
				 	if (empty($page->tags())) {
						// Push the page to the data array for the response
						array_push($tmp['data'], $page->json($returnsArray=true));
					}
				} else{
					array_push($tmp['data'], $page->json($returnsArray=true));
				}
			} catch (Exception $e) {
				// Continue
			}
		}

		return $tmp;
	}

	private function getPage($key)
	{
		try {
			$page = new Page($key);
			return array(
				'status'=>'0',
				'message'=>'Page filtered by key: '.$key,
				'data'=>$page->json( $returnsArray=true )
			);
		} catch (Exception $e) {
			return array(
				'status'=>'1',
				'message'=>'Page not found.'
			);
		}
	}

	private function createPage($args)
	{
		// Unsanitize content because all values are sanitized
		if (isset($args['content'])) {
			$args['content'] = Sanitize::htmlDecode($args['content']);
		}

		// This function is defined on functions.php
		$key = createPage($args);
		if ($key===false) {
			return array(
				'status'=>'1',
				'message'=>'Error trying to create the new page.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'Page created.',
			'data'=>array('key'=>$key)
		);
	}

	private function editPage($args)
	{
		// Unsanitize content because all values are sanitized
		if (isset($args['content'])) {
			$args['content'] = Sanitize::htmlDecode($args['content']);
		}

		$newKey = editPage($args);

		if ($newKey===false) {
			return array(
				'status'=>'1',
				'message'=>'Error trying to edit the page.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'Page edited.',
			'data'=>array('key'=>$newKey)
		);
	}

	/**
	 * Delete a page
	 * @param		array		$args		Parameters for the function
	 * @return		array
	 */
	private function deletePage($args)
	{
		if (deletePage($args)) {
			return array(
				'status'=>'0',
				'message'=>'Page deleted.',
				'data'=>array('key'=>$args['key'])
			);
		}

		return array(
			'status'=>'1',
			'message'=>'An error occurred while trying to delete the page.'
		);
	}

	/**
	 * Get settings
	 * @return		array
	 */
	private function getSettings()
	{
		global $site;
		return array(
			'status'=>'0',
			'message'=>'Settings.',
			'data'=>$site->get()
		);
	}

	/**
	 * Edit settings
	 * @param			array			$args			All supported parameters are defined in the class site.class.php, variable $dbFields
	 * @return			array
	 */
	private function editSettings($args)
	{
		if (editSettings($args)) {
			return array(
				'status'=>'0',
				'message'=>'Settings edited.'
			);
		}
		return array(
			'status'=>'1',
			'message'=>'Error trying to edit the settings.'
		);
	}

	/*
	 | Returns the categories in the system
	 | Included the category name, key, description and the list of pages
	 | The list of pages are the page's key
	 |
	 | @return	array
		 */
	private function getCategories()
	{
		global $categories;
		$tmp = array(
			'status'=>'0',
			'message'=>'List of categories.',
			'data'=>array()
		);
		foreach ($categories->keys() as $key) {
			$category = $categories->getMap($key);
			array_push($tmp['data'], $category);
		}
		return $tmp;
	}

	/*
	 | Returns information about the category and pages related
	 | The pages are expanded which mean the title, content and more fields are returned in the query
	 | This can degrade the performance
	 |
	 | @key		string	Category key
	 |
	 | @return	array
		 */
	private function getCategory($key)
	{
		try {
			$category = new Category($key);
		} catch (Exception $e) {
			return array(
				'status'=>'1',
				'message'=>'Category not found by the key: '.$key
			);
		}

		$list = array();
		foreach ($category->pages() as $pageKey) {
			try {
				$page = new Page($pageKey);
				array_push($list, $page->json($returnsArray=true));
			} catch (Exception $e){}
		}

		$data = $category->json($returnsArray=true);
		$data['pages'] = $list;

		return array(
			'status'=>'0',
			'message'=>'Information about the category and pages related.',
			'data'=>$data
		);
	}

	/*	Create a new category === Bludit v4
		Referer to the function createCategory() from functions.php
	*/
	private function createCategory($args)
	{
		$key = createCategory($args);
		if ($key===false) {
			return array(
				'status'=>'1',
				'message'=>'An error occurred while trying to create the category.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'Category created.',
			'data'=>array('key'=>$key)
		);
	}

	/*	Edit a category === Bludit v4
		Referer to the function editCategory() from functions.php
	*/
	private function editCategory($args)
	{
		$key = editCategory($args);
		if ($key===false) {
			return array(
				'status'=>'1',
				'message'=>'An error occurred while trying to edit the category.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'Category edited.',
			'data'=>array('key'=>$key)
		);
	}

	/*	Delete a category === Bludit v4
		Referer to the function deleteCategory() from functions.php
	*/
	private function deleteCategory($args)
	{
		if (deleteCategory($args)) {
			return array(
				'status'=>'0',
				'message'=>'Category deleted.',
				'data'=>array('key'=>$args['key'])
			);
		}

		return array(
			'status'=>'1',
			'message'=>'An error occurred while trying to delete the category.'
		);
	}

	/*
	 | Returns the user profile
	 |
	 | @username	string	Username
	 |
	 | @return	array
		 */
	private function getUser($username)
	{
		try {
			$user = new User($username);
		} catch (Exception $e) {
			return array(
				'status'=>'1',
				'message'=>'User not found by username: '.$username
			);
		}

		$data = $user->json($returnsArray=true);
		return array(
			'status'=>'0',
			'message'=>'User profile.',
			'data'=>$data
		);
	}

	/*
	 | Returns all the users
	 |
	 | @return	array
		 */
	private function getUsers()
	{
		global $users;
		$data = array();
		foreach ($users->db as $username=>$profile) {
			try {
				$user = new User($username);
				$data[$username] = $user->json($returnsArray=true);
			} catch (Exception $e) {
				continue;
			}
		}

		return array(
			'status'=>'0',
			'message'=>'Users profiles.',
			'data'=>$data
		);
	}

	/*	Create a new user === Bludit v4
		Referer to the function createUser() from functions.php
	*/
	private function createUser($args)
	{
		$key = createUser($args);
		if ($key===false) {
			return array(
				'status'=>'1',
				'message'=>'An error occurred while trying to create the user.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'User created.',
			'data'=>array('key'=>$key)
		);
	}

	/*	Edit an user === Bludit v4
		Referer to the function editUser() from functions.php
	*/
	private function editUser($args)
	{
		$key = editUser($args);
		if ($key===false) {
			return array(
				'status'=>'1',
				'message'=>'An error occurred while trying to edit the user.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'User edited.',
			'data'=>array('key'=>$key)
		);
	}

	/*	Upload a profile picture === Bludit v4
		Referer to the function uploadProfilePicture() from functions.php
	*/
	private function uploadProfilePicture($username)
	{
		$data = uploadProfilePicture($username);
		if ($data===false) {
			return array(
				'status'=>'1',
				'message'=>'An error occurred while trying to upload the profile picture.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'Profile picture uploaded.',
			'data'=>$data
		);
	}

	/*	Delete a profile picture === Bludit v4
		Referer to the function deleteProfilePicture() from functions.php
	*/
	private function deleteProfilePicture($username)
	{
		if (deleteProfilePicture($username)) {
			return array(
				'status'=>'0',
				'message'=>'Profile picture deleted.',
				'data'=>array('username'=>$username)
			);
		}

		return array(
			'status'=>'1',
			'message'=>'An error occurred while trying to delete the profile picture.'
		);
	}

	/*	Upload the site logo === Bludit v4
		Referer to the function uploadSiteLogo() from functions.php
	*/
	private function uploadSiteLogo($username)
	{
		$data = uploadSiteLogo($username);
		if ($data===false) {
			return array(
				'status'=>'1',
				'message'=>'An error occurred while trying to upload the site logo.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'Site logo uploaded.',
			'data'=>$data
		);
	}

	/*	Delete the site logo === Bludit v4
		Referer to the function deleteSiteLogo() from functions.php
	*/
	private function deleteSiteLogo()
	{
		if (deleteSiteLogo()) {
			return array(
				'status'=>'0',
				'message'=>'Site logo deleted.'
			);
		}

		return array(
			'status'=>'1',
			'message'=>'An error occurred while trying to delete the site logo.'
		);
	}

	/*	Upload a file to a particular page === Bludit v4
		Referer to the function uploadPageFile() from functions.php
	*/
	private function uploadPageFile($pageKey)
	{
		$data = uploadPageFile($pageKey);
		if ($data===false) {
			return array(
				'status'=>'1',
				'message'=>'An error occurred while trying to upload the file.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'File uploaded to the page.',
			'data'=>$data
		);
	}

	/*
		Generates unique slug text for the a page

		@args['text']		string
		@args['parentKey']	string
		@args['pageKey']	string

		@return['data']	string	The slug string
	*/
	private function getFriendlyURL($args)
	{
		global $pages;
		$slug = $pages->generateKey($args['text'], $args['parentKey'], true, $args['pageKey']);

		return array(
			'status'=>'0',
			'message'=>'Friendly URL generated.',
			'data'=>array('slug'=>$slug)
		);
	}

	/*
		Returns all files uploaded for a specific page.
		Includes all files types.

		@pageKey			string	The page's key

		@return['data']	array	The list of files
	*/
	private function getFiles($pageKey)
	{
		$chunk = false;
		$sortByDate = true;
		$path = PATH_UPLOADS_PAGES.$pageKey.DS;

		if (Sanitize::pathFile($path) === false) {
			return array(
				'status'=>'1',
				'message'=>'Invalid path.'
			);
		}

		$files = array();
		$listFiles = Filesystem::listFiles($path, '*', '*', $sortByDate, $chunk);
		foreach ($listFiles as $file) {
			if (Text::stringContains($file, '-thumbnail-')) {
				continue;
			}

			$filename = Filesystem::filename($file);
			$fileExtension = Filesystem::extension($file);
			$absoluteURL = DOMAIN_UPLOADS_PAGES.$pageKey.DS.$filename.'.'.$fileExtension;
			$absolutePath = PATH_UPLOADS_PAGES.$pageKey.DS.$filename.'.'.$fileExtension;

			$thumbnailSmall = '';
			if (Filesystem::fileExists(PATH_UPLOADS_PAGES.$pageKey.DS.$filename.'-thumbnail-s.'.$fileExtension)) {
				$thumbnailSmall = DOMAIN_UPLOADS_PAGES.$pageKey.DS.$filename.'-thumbnail-s.'.$fileExtension;
			}

			$thumbnailMedium = '';
			if (Filesystem::fileExists(PATH_UPLOADS_PAGES.$pageKey.DS.$filename.'-thumbnail-m.'.$fileExtension)) {
				$thumbnailMedium = DOMAIN_UPLOADS_PAGES.$pageKey.DS.$filename.'-thumbnail-m.'.$fileExtension;
			}

			$data = array(
				'filename'=>$filename.'.'.$fileExtension,
				'absolutePath'=>$absolutePath,
				'absoluteURL'=>$absoluteURL,
				'mime'=>Filesystem::mimeType($absolutePath),
				'size'=>Filesystem::getSize($absolutePath),
				'thumbnailSmall'=>$thumbnailSmall,
				'thumbnailMedium'=>$thumbnailMedium
			);

			array_push($files, $data);
		}

		return array(
			'status'=>'0',
			'message'=>'Files for the page key: '.$pageKey,
			'data'=>$files
		);
	}

	/*	Install and activate a plugin === Bludit v4
		Referer to the function activatePlugin() from functions.php
	*/
	private function activatePlugin($pluginClassName)
	{
		if (activatePlugin($pluginClassName)) {
			return array(
				'status'=>'0',
				'message'=>'Plugin installed and activated.',
				'data'=>array('key'=>$pluginClassName)
			);
		}

		return array(
			'status'=>'1',
			'message'=>'An error occurred while trying to install the plugin.'
		);
	}

	/*	Uninstall and deactivate a plugin === Bludit v4
		Referer to the function deactivatePlugin() from functions.php
	*/
	private function deactivatePlugin($pluginClassName)
	{
		if (deactivatePlugin($pluginClassName)) {
			return array(
				'status'=>'0',
				'message'=>'Plugin uninstalled and deactivated.',
				'data'=>array('key'=>$pluginClassName)
			);
		}

		return array(
			'status'=>'1',
			'message'=>'An error occurred while trying to uninstall the plugin.'
		);
	}


	/*	Configure a plugin === Bludit v4
		Referer to the function configurePlugin() from functions.php
	*/
	private function configurePlugin($args)
	{
		if (configurePlugin($args)) {
			return array(
				'status'=>'0',
				'message'=>'Plugin configured.',
				'data'=>array('key'=>$args['className'])
			);
		}

		return array(
			'status'=>'1',
			'message'=>'An error occurred while trying to configure the plugin.'
		);
	}
}