<?php

class pluginAPI extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'ping'=>false,
			'authKey'=>''
		);
	}

	public function form()
	{
		$html = '';

		$html .= '<div>';
		$html .= '<p>Authorization Key: '.$this->getDbField('authKey').'</p>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<input name="ping" id="jsping" type="checkbox" value="true" '.($this->getDbField('ping')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsping">Ping Bludit.com</label>';
		$html .= '</div>';


		return $html;
	}

	public function afterFormSave()
	{
		$this->ping();
	}

	private function ping()
	{
		if($this->getDbField('ping')) {
			// Just a request HTTP with the website URL
			Log::set( file_get_contents('https://www.bludit.com/api.php') );
		}
	}

	private function getPost($key)
	{
		// Generate the object Post
		$Post = buildPost($key);

		if(!$Post) {
			return json_encode(array(
				'status'=>'0',
				'bludit'=>'Bludit API plugin',
				'message'=>'The post doesn\'t exist'
			));
		}

		return $Post->json();
	}

	private function getPage($key)
	{
		// Generate the object Page
		$Page = buildPage($key);

		if(!$Page) {
			return json_encode(array(
				'status'=>'0',
				'bludit'=>'Bludit API plugin',
				'message'=>'The page doesn\'t exist'
			));
		}

		return $Page->json();
	}

	public function beforeRulesLoad()
	{
		global $Url;

		// The URI start with /api/
		$startString = HTML_PATH_ROOT.'api/';
		$URI = $Url->uri();
		$length = mb_strlen($startString, CHARSET);
		if( mb_substr($URI, 0, $length)!=$startString ) {
			return false;
		}

		// Remove the first part of the URI
		$URI = ltrim($URI, HTML_PATH_ROOT.'api/');

		// Parameters
		// ------------------------------------------------------------
		// show post {post slug}
		// show page {page slug}
		// show all posts
		// show all pages

		// Get parameters
		$parameters = explode('/', $URI);

		// Check parameters are sended
		for($i=0; $i<3; $i++) {
			if(empty($parameters[$i])) {
				return false;
			}
		}

		// Default JSON
		$json = json_encode(array(
			'status'=>'0',
			'bludit'=>'Bludit API plugin',
			'message'=>'Check the parameters'
		));

		if($parameters[0] === 'show') {

			$key = $parameters[2];

			if($parameters[1] === 'post') {
				$json = $this->getPost($key);
			}
			elseif($parameters[1] === 'page') {
				$json = $this->getPage($key);
			}
		}

		// Print the JSON
		header('Content-Type: application/json');
		exit($json);
	}
}