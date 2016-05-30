<?php

class pluginAPI extends Plugin {

	public function getPost($key)
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

	public function getPage($key)
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