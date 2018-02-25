<?php

class pluginAPI extends Plugin {

	private $method;

	public function init()
	{
		// Generate the API Token
		$token = md5( uniqid().time().DOMAIN );

		$this->dbFields = array(
			'token'=>$token,	// API Token
			'amountOfItems'=>15	// Amount of items to return
		);
	}

	public function form()
	{
		global $Language;

		$html  = '<div>';
		$html .= '<label>'.$Language->get('API Token').'</label>';
		$html .= '<input name="token" type="text" value="'.$this->getValue('token').'">';
		$html .= '<span class="tip">'.$Language->get('This token is for read only and is regenerated every time you install the plugin').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$Language->get('Amount of pages').'</label>';
		$html .= '<input id="jsamountOfItems" name="amountOfItems" type="text" value="'.$this->getValue('amountOfItems').'">';
		$html .= '<span class="tip">'.$Language->get('This is the maximum of pages to return when you call to').'</span>';
		$html .= '</div>';

		return $html;
	}


// API HOOKS
// ----------------------------------------------------------------------------

	public function beforeAll()
	{
		global $Url;
		global $dbPages;
		global $dbUsers;
		global $Login;

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

		if ( empty($inputs) ) {
			$this->response(404, 'Not Found', array('message'=>'Missing method inputs.'));
		}

		// ENDPOINT PARAMETERS
		// ------------------------------------------------------------
		$parameters = $this->getEndpointParameters($URI);

		if ( empty($parameters) ) {
			$this->response(404, 'Not Found', array('message'=>'Missing endpoint parameters.'));
		}

		// API TOKEN
		// ------------------------------------------------------------
		// Token from the plugin, the user can change it on the settings of the plugin
		$tokenAPI = $this->getValue('token');

		// Check empty token
		if (empty($inputs['token'])) {
			$this->response(404, 'Not Found', array('message'=>'Missing API token.'));
		}

		// Check if the token is valid
		if ($inputs['token']!==$tokenAPI) {
			$this->response(401, 'Unauthorized', array('message'=>'Invalid API token.'));
		}

		// AUTHENTICATION TOKEN
		// ------------------------------------------------------------
		$writePermissions = false;
		if ( !empty($inputs['authentication']) ) {

			// Get the user with the authentication token, FALSE if doesn't exit
			$username = $dbUsers->getByAuthToken($inputs['authentication']);
			if ($username!==false) {

				// Get the object user to check the role
				$user = $dbUsers->getUser($username);
				if (($user->role()=='admin') && ($user->enabled())) {

					// Loggin the user to create the session
					$Login->setLogin($username, 'admin');
					// Enable write permissions
					$writePermissions = true;
				}
			}
		}

		// ENDPOINTS
		// ------------------------------------------------------------

		// (GET) /api/pages
		if ( ($method==='GET') && ($parameters[0]==='pages') && empty($parameters[1]) ) {
			$data = $this->getPages();
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

	// Returns an array with key=>value
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

	private function getPages()
	{
		global $dbPages;

		$onlyPublished = true;
		$amountOfItems = $this->getValue('amountOfItems');
		$pageNumber = 1;
		$list = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);

		$tmp = array(
			'status'=>'0',
			'message'=>'List of pages, amount of items: '.$amountOfItems,
			'data'=>array()
		);

		// Get keys of pages
		foreach ($list as $pageKey) {
			// Create the page object from the page key
			$page = buildPage($pageKey);
			array_push($tmp['data'], $page->json( $returnsArray=true ));
		}

		return $tmp;
	}

	private function getPage($key)
	{
		// Generate the object Page
		$Page = buildPage($key);

		if (!$Page) {
			return array(
				'status'=>'1',
				'message'=>'Page not found.'
			);
		}

		return array(
			'status'=>'0',
			'message'=>'Page filtered by key: '.$key,
			'data'=>$Page->json( $returnsArray=true )
		);
	}

	private function createPage($args)
	{
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