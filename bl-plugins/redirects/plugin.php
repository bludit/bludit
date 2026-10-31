<?php

class pluginRedirects extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'redirects' => ''
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>' . $L->get('redirects') . '</label>';
		$html .= '<textarea name="redirects" rows="12" placeholder="old-page new-page&#10;old/path https://www.example.com/page">' . $this->getValue('redirects') . '</textarea>';
		$html .= '<span class="tip">' . $L->get('redirects-tip') . '</span>';
		$html .= '</div>';

		return $html;
	}

	public function post()
	{
		if (isset($_POST['redirects'])) {
			$_POST['redirects'] = $this->format($this->resolve($this->parse($_POST['redirects'])));
		}

		return parent::post();
	}

	public function afterPageCreate($key = false)
	{
		$this->afterPageModify($key, $key);
	}

	public function afterPageModify($key = false, $previousKey = false)
	{
		if ($key === false || !$this->isPublic($key)) {
			return false;
		}

		global $pages;

		$current = $this->getValue('redirects', false);
		$rules = $this->parse($current);
		$childrenKeys = $pages->getChildren($key);

		if ($previousKey !== false && $previousKey !== $key) {
			$rules[$this->pagePath($previousKey)] = $this->pagePath($key);
			foreach ($childrenKeys as $childKey) {
				$oldChildKey = $previousKey . mb_substr($childKey, mb_strlen($key, CHARSET), null, CHARSET);
				$rules[$this->pagePath($oldChildKey)] = $this->pagePath($childKey);
			}
		}

		// The page lives in this URL now, a previous redirect from here is no longer valid
		unset($rules[$this->pagePath($key)]);
		foreach ($childrenKeys as $childKey) {
			unset($rules[$this->pagePath($childKey)]);
		}

		$updated = $this->format($this->resolve($rules));
		if ($updated !== $current) {
			$this->setField('redirects', $updated);
		}

		return false;
	}

	public function beforeAll()
	{
		global $url;

		$redirects = $this->getValue('redirects', false);
		if (empty($redirects)) {
			return false;
		}

		$path = trim(mb_substr($url->uri(), mb_strlen(HTML_PATH_ROOT, CHARSET), null, CHARSET), '/');
		$rules = $this->parse($redirects);
		if (!isset($rules[$path])) {
			return false;
		}

		$target = $this->isAbsolute($rules[$path]) ? $rules[$path] : DOMAIN_BASE . $rules[$path];
		$target = preg_replace_callback('/[\x80-\xFF]+/', function ($matches) {
			return rawurlencode($matches[0]);
		}, $target);

		if (!empty($_SERVER['QUERY_STRING'])) {
			$target .= (strpos($target, '?') === false ? '?' : '&') . $_SERVER['QUERY_STRING'];
		}

		Redirect::url($target, 301);
	}

	// Returns an array like array('old-path' => 'new-path', ...)
	private function parse($text)
	{
		$rules = array();
		foreach (preg_split('/\R/', $text, -1, PREG_SPLIT_NO_EMPTY) as $line) {
			$parts = preg_split('/\s+/', trim($line));
			if (count($parts) !== 2 || $this->isAbsolute($this->path($parts[0]))) {
				continue;
			}

			$from = urldecode($this->path($parts[0]));
			if ($from !== '') {
				$rules[$from] = $this->path($parts[1]);
			}
		}

		return $rules;
	}

	// Follows the redirects to the final target and removes the loops
	private function resolve($rules)
	{
		$resolved = array();
		foreach ($rules as $from => $to) {
			$visited = array($from => true);
			while (isset($rules[$to]) && !isset($visited[$to])) {
				$visited[$to] = true;
				$to = $rules[$to];
			}

			if (isset($visited[$to])) {
				Log::set(__METHOD__ . LOG_SEP . 'Redirect loop removed, ' . $from);
				continue;
			}

			$resolved[$from] = $to;
		}

		return $resolved;
	}

	private function format($rules)
	{
		$lines = array();
		foreach ($rules as $from => $to) {
			$lines[] = $from . ' ' . ($to === '' ? '/' : $to);
		}

		return implode(PHP_EOL, $lines);
	}

	// Returns the value relative to the site root when the value is from this site
	private function path($value)
	{
		if (strpos($value, DOMAIN_BASE) === 0) {
			$value = substr($value, strlen(DOMAIN_BASE));
		}

		return $this->isAbsolute($value) ? $value : trim($value, '/');
	}

	private function pagePath($key)
	{
		return ltrim(trim(PAGE_URI_FILTER, '/') . '/' . $key, '/');
	}

	private function isAbsolute($value)
	{
		return preg_match('#^https?://#i', $value) === 1;
	}

	private function isPublic($key)
	{
		global $pages;

		$page = $pages->getPageDB($key);
		return $page !== false && in_array($page['type'], array('published', 'static', 'sticky'));
	}
}
