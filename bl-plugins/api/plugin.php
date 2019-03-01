<?php

class pluginAPI extends Plugin {

	private $method;

	public function init()
	{
		// Generate the API Token
		$token = md5( uniqid().time().DOMAIN );

		$this->dbFields = array(
			'token'=>$token,	// API Token
			'numberOfItems'=>15	// Amount of items to return
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
		$html .= '<label>'.$L->get('URL').'</label>';
		$html .= '<p class="text-muted">'.DOMAIN.'/api/{endpoint}</p>';
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
						$login->setLogin($username, 'admin');
						// Enable write permissions
						$writePermissions = true;
					}
				} catch (Exception $e) {
					// Continue without permissions
				}
			}
		}

		// ENDPOINTS
		// ------------------------------------------------------------

		// (GET) /api/pages
		if ( ($method==='GET') && ($parameters[0]==='pages') && empty($parameters[1]) ) {
			$data = $this->getPages($inputs);
		}
		// (GET) /api/pages/<key>
		elseif ( ($method==='GET') && ($parameters[0]==='pages') && !empty($parameters[1]) ) {
			$pageKey = $parameters[1];
			$data = $this->getPage($pageKey);
		}
		// (PUT) /api/pages/<key>
		elseif ( ($method==='PUT') && ($parameters[0]==='pages') && !empty($parameters[1]) && $writePermissions ) {
			$pageKey = $parameters[1];
			$data = $this->editPage($pageKey, $inputs);
		}
		// (DELETE) /api/pages/<key>
		elseif ( ($method==='DELETE') && ($parameters[0]==='pages') && !empty($parameters[1]) && $writePermissions ) {
			$pageKey = $parameters[1];
			$data = $this->deletePage($pageKey);
		}
		// (POST) /api/pages
		elseif ( ($method==='POST') && ($parameters[0]==='pages') && empty($parameters[1]) && $writePermissions ) {
			$data = $this->createPage($inputs);
		}
		// (GET) /api/tags
		elseif ( ($method==='GET') && ($parameters[0]==='tags') && empty($parameters[1]) ) {
			$data = $this->getTags();
		}
		// (GET) /api/tags/<key>
		elseif ( ($method==='GET') && ($parameters[0]==='tags') && !empty($parameters[1]) ) {
			$tagKey = $parameters[1];
			$data = $this->getTag($tagKey);
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
				'message'=>'Tag not found by the tag key: '.$key
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
			'message'=>'Tag data and pages related to the tag.',
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

		$numberOfItems = $this->getValue('numberOfItems');
		$pageNumber = 1;
		$list = $pages->getList($pageNumber, $numberOfItems, $published, $static, $sticky, $draft, $scheduled);

		$tmp = array(
			'status'=>'0',
			'message'=>'List of pages, number of items: '.$numberOfItems,
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

	private function editPage($key, $args)
	{
		// Unsanitize content because all values are sanitized
		if (isset($args['content'])) {
			$args['content'] = Sanitize::htmlDecode($args['content']);
		}

		$args['key'] = $key;
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

	private function deletePage($key)
	{
		if (deletePage($key)) {
			return array(
				'status'=>'0',
				'message'=>'Page deleted.'
			);
		}

		return array(
			'status'=>'1',
			'message'=>'Error trying to delete the page.'
		);
	}

}