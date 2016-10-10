<?php

class pluginAPI extends Plugin {

	public function init()
	{
		global $Security;

		// This key is used for request such as get the list of all posts and pages
		$authKey = md5($Security->key1().time().DOMAIN);

		$this->dbFields = array(
			'ping'=>1,		// 0 = false, 1 = true
			'authKey'=>$authKey,	// Private key
			'showAllAmount'=>15	// Amount of posts and pages for return
		);
	}

	public function form()
	{
		$html = '';

		$html .= '<div>';
		$html .= '<input type="hidden" name="ping" value="0">';
		$html .= '<input name="ping" id="jsping" type="checkbox" value="1" '.($this->getDbField('ping')?'checked':'').'>';
		$html .= '<label class="forCheckbox" for="jsping">Ping Bludit.com</label>';
		$html .= '<div class="tip">Enable this feature to share your posts and pages with Bludit.com.</div>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<p><b>Authorization Key:</b> '.$this->getDbField('authKey').'</p>';
		$html .= '<div class="tip">This key is private, do not share it with anyone.</div>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<p><b>Show all posts:</b> <a href="'.DOMAIN_BASE.'api/show/all/posts/'.$this->getDbField('authKey').'">'.DOMAIN_BASE.'api/show/all/posts/'.$this->getDbField('authKey').'</a></p>';
		$html .= '<div class="tip">Get all posts from this site.</div>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<p><b>Show all pages:</b> <a href="'.DOMAIN_BASE.'api/show/all/pages/'.$this->getDbField('authKey').'">'.DOMAIN_BASE.'api/show/all/pages/'.$this->getDbField('authKey').'</a></p>';
		$html .= '<div class="tip">Get all pages from this site.</div>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<p><b>Show post:</b> <a href="'.DOMAIN_BASE.'api/show/post/{POST-NAME}">'.DOMAIN_BASE.'api/show/post/{POST-NAME}</a></p>';
		$html .= '<div class="tip">Get a particular post, change the {POST-NAME} with the post friendly url.</div>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<p><b>Show page:</b> <a href="'.DOMAIN_BASE.'api/show/page/{PAGE-NAME}">'.DOMAIN_BASE.'api/show/page/{PAGE-NAME}</a></p>';
		$html .= '<div class="tip">Get a particular page, change the {PAGE-NAME} with the page friendly url.</div>';
		$html .= '</div>';

		return $html;
	}

	public function afterFormSave()
	{
		$this->ping();
	}

	public function install($position=0)
	{
		parent::install($position);

		$this->ping();
	}

	private function ping()
	{
		if($this->getDbField('ping')) {

			// Get the authentication key
			$authKey = $this->getDbField('authKey');

			$url = 'https://api.bludit.com/ping?authKey='.$authKey.'&url='.DOMAIN_BASE;

			// Check if curl is installed
			if( function_exists('curl_version') ) {

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_HEADER, false);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$out = curl_exec($ch);

				if($out === false) {
					Log::set('Plugin API : '.'Curl error: '.curl_error($ch));
				}

				curl_close($ch);
			}
			else {
				$options = array(
					"ssl"=>array(
						"verify_peer"=>false,
						"verify_peer_name"=>false
					)
				);

				$stream = stream_context_create($options);
				$out = file_get_contents($url, false, $stream);
			}
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

	private function getAllPosts()
	{
		$posts = buildPostsForPage(0, $this->getDbField('showAllAmount'), true, false);

		$tmp = array();

		foreach($posts as $Post) {
			array_push($tmp, $Post->json( $returnsArray=true ));
		}

		return json_encode($tmp);
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

	private function getAllPages()
	{
		$pages = buildAllPages();

		$tmp = array();

		foreach($pages as $Page) {
			if($Page->published()) {
				array_push($tmp, $Page->json( $returnsArray=true ));
			}
		}

		return json_encode($tmp);
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
		// show all posts {AUTH KEY}
		// show all pages {AUTH KEY}

		// Get parameters
		$parameters = explode('/', $URI);

		for($i=0; $i<3; $i++) {
			if(empty($parameters[$i])) {
				return false;
			} else {
				// Sanizite
				$parameters[$i] = Sanitize::html($parameters[$i]);
			}
		}

		// Default JSON
		$json = json_encode(array(
			'status'=>'0',
			'bludit'=>'Bludit API plugin',
			'message'=>'Check the parameters'
		));



		if($parameters[0]==='show') {

			if($parameters[1]==='all') {

				// Authentication key from the URI
				$authKey = $parameters[3];

				// Compare keys
				if( $authKey===$this->getDbField('authKey') ) {

					if($parameters[2] === 'posts') {
						$json = $this->getAllPosts();
					}
					elseif($parameters[2] === 'pages') {
						$json = $this->getAllPages();
					}
				}
			}
			elseif($parameters[1]==='post' || $parameters[1]==='page') {

				$key = $parameters[2];

				if($parameters[1] === 'post') {
					$json = $this->getPost($key);
				}
				elseif($parameters[1] === 'page') {
					$json = $this->getPage($key);
				}
			}
		}

		// Print the JSON
		header('Content-Type: application/json');
		exit($json);
	}
}
