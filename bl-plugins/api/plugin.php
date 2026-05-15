<?php

class pluginAPI extends Plugin
{

	private $method;

	// HTTP status applied to the final response. Inner handlers may override it
	// by calling setStatus() before returning their result. The body still carries
	// the legacy status:'0'|'1' field for backwards compatibility.
	private $httpCode = 200;
	private $httpMessage = 'OK';

	private function setStatus($code)
	{
		$messages = array(
			200 => 'OK',
			201 => 'Created',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found',
			409 => 'Conflict',
			500 => 'Internal Server Error'
		);
		$this->httpCode = $code;
		$this->httpMessage = isset($messages[$code]) ? $messages[$code] : 'Error';
	}

	public function init()
	{
		// Generate the API Token (32 bytes = 64 hex chars = 256 bits of entropy)
		$token = bin2hex( random_bytes(32) );

		$this->dbFields = array(
			'token' => $token,	// API Token
			'numberOfItems' => 15	// Amount of items to return
		);
	}

	public function getToken()
	{
		return $this->getValue('token');
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('URL') . '</label>';
		$html .= '<p class="text-muted">' . DOMAIN_BASE . 'api/{endpoint}</p>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('API Token') . '</label>';
		$html .= '<input name="token" type="text" dir="auto" value="' . $this->getValue('token') . '">';
		$html .= '<span class="tip">' . $L->get('This token is for read only and is regenerated every time you install the plugin') . '</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('Amount of pages') . '</label>';
		$html .= '<input id="jsnumberOfItems" name="numberOfItems" type="text" dir="auto" value="' . $this->getValue('numberOfItems') . '">';
		$html .= '<span class="tip">' . $L->get('This is the maximum of pages to return when you call to') . '</span>';
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
		$URI = $this->webhook('api', $returnsAfterURI = true, $fixed = false);
		if ($URI === false) {
			return false;
		}

		// METHOD
		// ------------------------------------------------------------
		$method = $this->getMethod();

		// METHOD INPUTS
		// ------------------------------------------------------------
		$inputs = $this->getMethodInputs();
		if (empty($inputs)) {
			$this->response(400, 'Bad Request', array('message' => 'Missing method inputs.'));
		}

		// ENDPOINT PARAMETERS
		// ------------------------------------------------------------
		$parameters = $this->getEndpointParameters($URI);
		if (empty($parameters)) {
			$this->response(400, 'Bad Request', array('message' => 'Missing endpoint parameters.'));
		}

		// API TOKEN
		// ------------------------------------------------------------
		// Token from the plugin, the user can change it on the settings of the plugin
		$tokenAPI = $this->getValue('token');

		// Check empty token
		if (empty($inputs['token'])) {
			$this->response(400, 'Bad Request', array('message' => 'Missing API token.'));
		}

		// Check if the token is valid
		if ($inputs['token'] !== $tokenAPI) {
			$this->response(401, 'Unauthorized', array('message' => 'Invalid API token.'));
		}

		// AUTHENTICATION TOKEN
		// ------------------------------------------------------------
		$writePermissions = false;
		if (!empty($inputs['authentication'])) {

			// Get the user with the authentication token, FALSE if it doesn't exist
			$username = $users->getByAuthToken($inputs['authentication']);
			if ($username !== false) {
				try {
					$user = new User($username);
					if (($user->role() == 'admin') && ($user->enabled())) {
						// Loggin the user to create the session
						$login = new Login();
						$login->setLogin($username, 'admin');
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

		// (GET) /api/pages
		if (($method === 'GET') && ($parameters[0] === 'pages') && empty($parameters[1])) {
			$data = $this->getPages($inputs);
		}
		// (GET) /api/pages/<key>
		elseif (($method === 'GET') && ($parameters[0] === 'pages') && !empty($parameters[1])) {
			$pageKey = $parameters[1];
			if (isset($parameters[2])) {
				$pageKey = $parameters[1] . '/' . $parameters[2];
			}
			$data = $this->getPage($pageKey);
		}
		// (PUT) /api/pages/<key>
		elseif (($method === 'PUT') && ($parameters[0] === 'pages') && !empty($parameters[1]) && $writePermissions) {
			$pageKey = $parameters[1];
			$data = $this->editPage($pageKey, $inputs);
		}
		// (DELETE) /api/pages/<key>
		elseif (($method === 'DELETE') && ($parameters[0] === 'pages') && !empty($parameters[1]) && $writePermissions) {
			$pageKey = $parameters[1];
			$data = $this->deletePage($pageKey);
		}
		// (POST) /api/pages
		elseif (($method === 'POST') && ($parameters[0] === 'pages') && empty($parameters[1]) && $writePermissions) {
			$data = $this->createPage($inputs);
		}
		// (GET) /api/settings
		elseif (($method === 'GET') && ($parameters[0] === 'settings') && empty($parameters[1]) && $writePermissions) {
			$data = $this->getSettings();
		}
		// (PUT) /api/settings
		elseif (($method === 'PUT') && ($parameters[0] === 'settings') && empty($parameters[1]) && $writePermissions) {
			$data = $this->editSettings($inputs);
		}
		// (POST) /api/images
		elseif (($method === 'POST') && ($parameters[0] === 'images') && $writePermissions) {
			$data = $this->uploadImage($inputs);
		}
		// (GET) /api/tags
		elseif (($method === 'GET') && ($parameters[0] === 'tags') && empty($parameters[1])) {
			$data = $this->getTags();
		}
		// (GET) /api/tags/<key>
		elseif (($method === 'GET') && ($parameters[0] === 'tags') && !empty($parameters[1])) {
			$tagKey = $parameters[1];
			$data = $this->getTag($tagKey);
		}
		// (GET) /api/categories
		elseif (($method === 'GET') && ($parameters[0] === 'categories') && empty($parameters[1])) {
			$data = $this->getCategories();
		}
		// (GET) /api/categories/<key>
		elseif (($method === 'GET') && ($parameters[0] === 'categories') && !empty($parameters[1])) {
			$categoryKey = $parameters[1];
			$data = $this->getCategory($categoryKey);
		}
		// (GET) /api/users
		elseif (($method === 'GET') && ($parameters[0] === 'users') && empty($parameters[1])) {
			$data = $this->getUsers();
		}
		// (GET) /api/users/<username>
		elseif (($method === 'GET') && ($parameters[0] === 'users') && !empty($parameters[1])) {
			$username = $parameters[1];
			$data = $this->getUser($username);
		}
		// (GET) /api/files/<page-key>
		elseif (($method === 'GET') && ($parameters[0] === 'files') && !empty($parameters[1])) {
			$pageKey = $parameters[1];
			if (!$this->isValidPageKey($pageKey)) {
				$this->response(400, 'Bad Request', array('message' => 'Invalid page key.'));
			}
			$data = $this->getFiles($pageKey);
		}
		// (POST) /api/files/<page-key>
		elseif (($method === 'POST') && ($parameters[0] === 'files') && !empty($parameters[1]) && $writePermissions) {
			$pageKey = $parameters[1];
			if (!$this->isValidPageKey($pageKey)) {
				$this->response(400, 'Bad Request', array('message' => 'Invalid page key.'));
			}
			$data = $this->uploadFile($pageKey);
		} else {
			$this->response(401, 'Unauthorized', array('message' => 'Access denied or invalid endpoint.'));
		}

		$this->response($this->httpCode, $this->httpMessage, $data);
	}

	// PRIVATE METHODS
	// ----------------------------------------------------------------------------

	// Validate page key to prevent path traversal (CWE-22)
	private function isValidPageKey($pageKey)
	{
		if (strpos($pageKey, '..') !== false) {
			return false;
		}
		if (strpos($pageKey, "\0") !== false) {
			return false;
		}
		return true;
	}

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
		switch ($this->method) {
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
	//
	// Note: DB-bound values are sanitized by core (Pages::add/edit, Site::set);
	// sanitizing again here would double-encode form/query values like
	// "Cats & Dogs" into "Cats &amp;amp; Dogs".
	private function cleanInputs($inputs)
	{
		if (is_array($inputs)) {
			return $inputs;
		}
		if (is_string($inputs)) {
			$decoded = json_decode($inputs, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				return array();
			}
			return is_array($decoded) ? $decoded : array();
		}
		return array();
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
		foreach ($parameters as $key => $value) {
			$parameters[$key] = Sanitize::html($value);
		}

		return $parameters;
	}

	private function response($code = 200, $message = 'OK', $data = array())
	{
		header('HTTP/1.1 ' . $code . ' ' . $message);
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		$json = json_encode($data);
		exit($json);
	}

	private function getTags()
	{
		global $tags;
		$tmp = array(
			'status' => '0',
			'message' => 'List of tags.',
			'data' => array()
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
			$this->setStatus(404);
			return array(
				'status' => '1',
				'message' => 'Tag not found by the key: ' . $key
			);
		}

		$list = array();
		foreach ($tag->pages() as $pageKey) {
			try {
				$page = new Page($pageKey);
				array_push($list, $page->json($returnsArray = true));
			} catch (Exception $e) {
			}
		}

		$data = $tag->json($returnsArray = true);
		$data['pages'] = $list;

		return array(
			'status' => '0',
			'message' => 'Information about the tag and pages related.',
			'data' => $data
		);
	}

	private function getPages($args)
	{
		global $pages;

		// Parameters and the default values
		$published 	= (isset($args['published']) ? $args['published'] == 'true' : true);
		$static 	= (isset($args['static']) ? $args['static'] == 'true' : false);
		$draft 		= (isset($args['draft']) ? $args['draft'] == 'true' : false);
		$sticky 	= (isset($args['sticky']) ? $args['sticky'] == 'true' : false);
		$scheduled 	= (isset($args['scheduled']) ? $args['scheduled'] == 'true' : false);
		$untagged 	= (isset($args['untagged']) ? $args['untagged'] == 'true' : false);

		$numberOfItems = (isset($args['numberOfItems']) ? (int)$args['numberOfItems'] : (int)$this->getValue('numberOfItems'));
		$pageNumber = (isset($args['pageNumber']) ? (int)$args['pageNumber'] : 1);

		// Clamp to safe values. -1 is the documented "return all" sentinel for
		// numberOfItems; anything else <=0 falls back to the plugin default and
		// then to a hard fallback so getList() can never receive 0.
		if ($pageNumber < 1) {
			$pageNumber = 1;
		}
		if ($numberOfItems === 0) {
			$numberOfItems = (int)$this->getValue('numberOfItems');
		}
		if ($numberOfItems === 0 || $numberOfItems < -1) {
			$numberOfItems = 15;
		}

		$total = 0;
		$list = $pages->getList($pageNumber, $numberOfItems, $published, $static, $sticky, $draft, $scheduled, $untagged, $total);
		// getList() returns false when pageNumber is past the end; treat as empty.
		if ($list === false) {
			$list = array();
		}

		$hasMore = ($numberOfItems > 0) && (($pageNumber * $numberOfItems) < $total);

		$tmp = array(
			'status' => '0',
			'message' => 'List of pages',
			'numberOfItems' => $numberOfItems,
			'meta' => array(
				'total' => $total,
				'pageNumber' => $pageNumber,
				'pageSize' => $numberOfItems,
				'hasMore' => $hasMore
			),
			'data' => array()
		);

		foreach ($list as $pageKey) {
			try {
				$page = new Page($pageKey);
				array_push($tmp['data'], $page->json($returnsArray = true));
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
				'status' => '0',
				'message' => 'Page filtered by key: ' . $key,
				'data' => $page->json($returnsArray = true)
			);
		} catch (Exception $e) {
			$this->setStatus(404);
			return array(
				'status' => '1',
				'message' => 'Page not found.'
			);
		}
	}

	private function createPage($args)
	{
		// This function is defined on functions.php
		$key = createPage($args);
		if ($key === false) {
			$this->setStatus(400);
			return array(
				'status' => '1',
				'message' => 'Error trying to create the new page.'
			);
		}

		$this->setStatus(201);
		try {
			$page = new Page($key);
			return array(
				'status' => '0',
				'message' => 'Page created.',
				'data' => $page->json($returnsArray = true)
			);
		} catch (Exception $e) {
			// The page was created but failed to load. Fall back to the legacy
			// minimal payload so the caller still gets the key.
			return array(
				'status' => '0',
				'message' => 'Page created.',
				'data' => array('key' => $key)
			);
		}
	}

	private function editPage($key, $args)
	{
		global $pages;

		if (!$pages->exists($key)) {
			$this->setStatus(404);
			return array(
				'status' => '1',
				'message' => 'Page not found.'
			);
		}

		$args['key'] = $key;
		$newKey = editPage($args);

		if ($newKey === false) {
			$this->setStatus(400);
			return array(
				'status' => '1',
				'message' => 'Error trying to edit the page.'
			);
		}

		try {
			$page = new Page($newKey);
			return array(
				'status' => '0',
				'message' => 'Page edited.',
				'data' => $page->json($returnsArray = true)
			);
		} catch (Exception $e) {
			// The page was edited but failed to load. Fall back to the legacy
			// minimal payload so the caller still gets the key.
			return array(
				'status' => '0',
				'message' => 'Page edited.',
				'data' => array('key' => $newKey)
			);
		}
	}

	private function deletePage($key)
	{
		global $pages;

		if (!$pages->exists($key)) {
			$this->setStatus(404);
			return array(
				'status' => '1',
				'message' => 'Page not found.'
			);
		}

		if (deletePage($key)) {
			return array(
				'status' => '0',
				'message' => 'Page deleted.'
			);
		}

		$this->setStatus(500);
		return array(
			'status' => '1',
			'message' => 'Error trying to delete the page.'
		);
	}

	/*
	| Upload an image and generate the thumbnails
	| Returns the image and thumbnail URL
	|
	| @inputs		array
	| @inputs['uuid']	string	Page UUID
	| @_FILE		array	https://www.php.net/manual/en/reserved.variables.files.php
	|
	| @return		array
	*/
	private function uploadImage($inputs)
	{
		// Set upload directory
		if (isset($inputs['uuid']) && IMAGE_RESTRICT) {
			if (!$this->isValidPageKey($inputs['uuid'])) {
				$this->setStatus(400);
				return array('status' => '1', 'message' => 'Invalid UUID.');
			}
			$imageDirectory 	= PATH_UPLOADS_PAGES . $inputs['uuid'] . DS;
			$thumbnailDirectory 	= $imageDirectory . 'thumbnails' . DS;
			$imageEndpoint 		= DOMAIN_UPLOADS_PAGES . $inputs['uuid'] . '/';
			$thumbnailEndpoint 	= $imageEndpoint . 'thumbnails' . '/';
			if (!Filesystem::directoryExists($thumbnailDirectory)) {
				Filesystem::mkdir($thumbnailDirectory, true);
			}
		} else {
			$imageDirectory 	= PATH_UPLOADS;
			$thumbnailDirectory 	= PATH_UPLOADS_THUMBNAILS;
			$imageEndpoint 		= DOMAIN_UPLOADS;
			$thumbnailEndpoint 	= DOMAIN_UPLOADS_THUMBNAILS;
		}

		if (!isset($_FILES['image'])) {
			$this->setStatus(400);
			return array(
				'status' => '1',
				'message' => 'No image sent.'
			);
		}

		if ($_FILES['image']['error'] != 0) {
			$this->setStatus(400);
			return array(
				'status' => '1',
				'message' => 'Error uploading the image, maximum load file size allowed: ' . ini_get('upload_max_filesize')
			);
		}

		// Move from PHP tmp file to Bludit tmp directory
		Filesystem::mv($_FILES['image']['tmp_name'], PATH_TMP . $_FILES['image']['name']);

		// Transform image and create thumbnails
		$image = transformImage(PATH_TMP . $_FILES['image']['name'], $imageDirectory, $thumbnailDirectory);
		if ($image) {
			$filename = Filesystem::filename($image);
			return array(
				'status' => '0',
				'message' => 'Image uploaded.',
				'image' => $imageEndpoint . $filename,
				'thumbnail' => $thumbnailEndpoint . $filename
			);
		}

		$this->setStatus(400);
		return array(
			'status' => '1',
			'message' => 'Image extension not allowed.'
		);
	}

	/*
	 | Get the settings
	 |
	 | @args	array
	 |
	 | @return	array
         */
	private function getSettings()
	{
		global $site;
		return array(
			'status' => '0',
			'message' => 'Settings.',
			'data' => $site->get()
		);
	}

	/*
	 | Edit the settings
	 | You can edit any field defined in the class site.class.php variable $dbFields
         |
         | @args	array
	 |
	 | @return	array
         */
	private function editSettings($args)
	{
		if (editSettings($args)) {
			return array(
				'status' => '0',
				'message' => 'Settings edited.'
			);
		}
		$this->setStatus(400);
		return array(
			'status' => '1',
			'message' => 'Error trying to edit the settings.'
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
			'status' => '0',
			'message' => 'List of categories.',
			'data' => array()
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
			$this->setStatus(404);
			return array(
				'status' => '1',
				'message' => 'Category not found by the key: ' . $key
			);
		}

		$list = array();
		foreach ($category->pages() as $pageKey) {
			try {
				$page = new Page($pageKey);
				array_push($list, $page->json($returnsArray = true));
			} catch (Exception $e) {
			}
		}

		$data = $category->json($returnsArray = true);
		$data['pages'] = $list;

		return array(
			'status' => '0',
			'message' => 'Information about the category and pages related.',
			'data' => $data
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
			$this->setStatus(404);
			return array(
				'status' => '1',
				'message' => 'User not found by username: ' . $username
			);
		}

		$data = $user->json($returnsArray = true);
		return array(
			'status' => '0',
			'message' => 'User profile.',
			'data' => $data
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
		foreach ($users->db as $username => $profile) {
			try {
				$user = new User($username);
				$data[$username] = $user->json($returnsArray = true);
			} catch (Exception $e) {
				continue;
			}
		}

		return array(
			'status' => '0',
			'message' => 'Users profiles.',
			'data' => $data
		);
	}

	/*
	 | Returns all files uploaded for a specific page, includes any type of file.
	 |
	 | @return	array
         */
	private function getFiles($pageKey)
	{
		$chunk = false;
		$sortByDate = true;
		$path = PATH_UPLOADS_PAGES . $pageKey . DS;
		$listFiles = Filesystem::listFiles($path, '*', '*', $sortByDate, $chunk);

		$files = array();
		foreach ($listFiles as $file) {
			$info = array('thumbnail' => '');
			$info['file'] = $file;
			$info['filename'] = basename($file);
			$info['mime'] = Filesystem::mimeType($file);
			$info['size'] = Filesystem::getSize($file);

			// Check if thumbnail exists for the file
			$thumbnail = $path . 'thumbnails' . DS . $info['filename'];
			if (Filesystem::fileExists($thumbnail)) {
				$info['thumbnail'] = $thumbnail;
			}

			array_push($files, $info);
		}

		return array(
			'status' => '0',
			'message' => 'Files for the page key: ' . $pageKey,
			'data' => $files
		);
	}

	/*
	| Upload a file to a particular page
	| Returns the file URL
	|
	| @inputs		array
	| @inputs['uuid']	string	Page UUID
	| @_FILE		array	https://www.php.net/manual/en/reserved.variables.files.php
	|
	| @return		array
	*/
	private function uploadFile($pageKey)
	{
		if (!isset($_FILES['file'])) {
			$this->setStatus(400);
			return array(
				'status' => '1',
				'message' => 'File not sent.'
			);
		}

		if ($_FILES['file']['error'] != 0) {
			$this->setStatus(400);
			return array(
				'status' => '1',
				'message' => 'Error uploading the file.'
			);
		}

		$filename = $_FILES['file']['name'];

		// Block dotfiles
		if (strpos($filename, '.') === 0) {
			$this->setStatus(400);
			return array('status' => '1', 'message' => 'File type not allowed.');
		}

		// Check file extension
		$fileExtension = Filesystem::extension($filename);
		$fileExtension = Text::lowercase($fileExtension);
		if (!in_array($fileExtension, $GLOBALS['ALLOWED_FILE_EXTENSIONS'])) {
			$this->setStatus(400);
			return array('status' => '1', 'message' => 'File type not allowed.');
		}

		// Sanitize filename to prevent issues with special characters
		$filenameWithoutExt = Filesystem::filename($filename);
		$filenameWithoutExt = Text::removeSpecialCharacters($filenameWithoutExt, '-');
		$filenameWithoutExt = Text::removeQuotes($filenameWithoutExt);
		$filenameWithoutExt = Text::removeSpaces($filenameWithoutExt, '-');
		$filename = $filenameWithoutExt . '.' . $fileExtension;

		$absoluteURL = DOMAIN_UPLOADS_PAGES . $pageKey . '/' . $filename;
		$absolutePath = PATH_UPLOADS_PAGES . $pageKey . DS . $filename;
		if (Filesystem::mv($_FILES['file']['tmp_name'], $absolutePath)) {
			return array(
				'status' => '0',
				'message' => 'File uploaded.',
				'filename' => $filename,
				'absolutePath' => $absolutePath,
				'absoluteURL' => $absoluteURL
			);
		}

		$this->setStatus(500);
		return array(
			'status' => '1',
			'message' => 'Error moving the file to the final path.'
		);
	}
}
