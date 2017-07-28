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
		$html .= '<span class="tip">'.$Language->get('The amount of pages to return when you call to /api/pages').'</span>';
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
			$this->response(404,'Not Found', array('message'=>'Missing method inputs.'));
		}

		// ENDPOINT PARAMETERS
		// ------------------------------------------------------------
		$parameters = $this->getEndpointParameters($URI);

		if ( empty($parameters) ) {
			$this->response(404,'Not Found', array('message'=>'Missing endpoint parameters.'));
		}

		// API TOKEN
		// ------------------------------------------------------------
		$tokenAPI = $this->getValue('token');

		// Check empty token
		if ( empty($inputs['token']) ) {
			$this->response(404,'Not Found', array('message'=>'Missing API token.'));
		}

		// Check the token is valid
		if ($inputs['token']!==$tokenAPI) {
			$this->response(401, 'Unauthorized', array('message'=>'Invalid API token.'));
		}

		// AUTHENTICATION TOKEN
		// ------------------------------------------------------------
		$writePermissions = false;
		if ( !empty($inputs['authentication']) ) {
			// Get the user with the authentication token
			$username = $dbUsers->getByAuthToken($inputs['authentication']);
			if ($username!==false) {
				// Enable write permissions
				$writePermissions = true;
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
			$data = $this->getPage($parameters[1]);
		}
		// (POST) /api/pages
		elseif ( ($method==='POST') && ($parameters[0]==='pages') && empty($parameters[1]) && $writePermissions ) {
			$data = $this->newPage($inputs);
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
				$inputs = file_get_contents("php://input");
				break;
			default:
				$inputs = json_encode(array());
				break;
		}

		return $this->cleanInputs($inputs);
	}

	private function getEndpointParameters($URI)
	{
		// ENDPOINT Parameters
		// ------------------------------------------------------------
		// /api/pages 		| GET  | returns all pages
		// /api/pages/{key}	| GET  | returns the page with the {key}
		// /api/pages 		| POST | create a new page

		$parameters = explode('/', $URI);

		// Sanitize parameters
		foreach ($parameters as $key=>$value) {
			$parameters[$key] = Sanitize::html($value);
		}

		return $parameters;
	}

	private function cleanInputs($inputs)
	{
		$tmp = array();
		if ( is_array($inputs) ) {
			foreach($inputs as $key=>$value) {
				$tmp[$key] = Sanitize::html($value);
			}
		} elseif ( is_string($inputs) ) {
			$tmp = json_decode($inputs, true);
			if(json_last_error()===0) {
				$tmp = array();
			}
		}
		return $tmp;
	}

	private function response($code=200, $message='OK', $data=array())
	{
		header('HTTP/1.1 '.$code.' '.$message);
		header('Content-Type: application/json');
		$json = json_encode($data);
		exit($json);
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

		$data = array();
		$data['status'] = '0';
		$data['message'] = 'Page filtered by key: '.$key;
		$data['data'] = $Page->json( $returnsArray=true );

		return $data;
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
		$keys = array_keys($list);
		foreach ($keys as $pageKey) {
			// Create the page object from the page key
			$page = buildPage($pageKey);
			array_push($tmp['data'], $page->json( $returnsArray=true ));
		}

		return $tmp;
	}

	private function createPage($args)
	{
		// This function is defined on functions.php
		return createPage($args);
	}

}