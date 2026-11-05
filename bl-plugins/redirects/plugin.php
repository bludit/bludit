<?php

class pluginRedirects extends Plugin {

	private $rejected = array();

	public function init()
	{
		$this->dbFields = array(
			'redirects' => '',
			'rejected' => ''
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		if ($this->getValue('rejected')) {
			$html .= '<div class="alert alert-warning" role="alert">';
			$html .= $L->get('redirects-rejected') . '<br>' . nl2br($this->getValue('rejected'));
			$html .= '</div>';
		}

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
			$_POST['rejected'] = implode(PHP_EOL, $this->rejected);
		}

		return parent::post();
	}

	public function afterPageCreate($key = false)
	{
		$this->afterPageModify($key, $key);
	}

	public function afterPageModify($key = false, $previousKey = false)
	{
		global $pages;

		if ($key === false || !$pages->isPublic($key)) {
			return false;
		}

		$current = $this->getValue('redirects', false);
		$rules = $this->parse($current);
		$childrenKeys = $pages->getChildren($key);

		if ($previousKey !== false && $previousKey !== $key) {
			$rules[$this->pagePath($previousKey)] = $this->pagePath($key);
			foreach ($childrenKeys as $childKey) {
				$oldChildKey = $previousKey . Text::cut($childKey, Text::length($key), null);
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
		global $url, $pages;

		$redirects = $this->getValue('redirects', false);
		if (empty($redirects)) {
			return false;
		}

		$path = trim(Text::cut($url->uri(), Text::length(HTML_PATH_ROOT), null), '/');
		$rules = $this->parse($redirects);
		if (!isset($rules[$path])) {
			return false;
		}

		// A published page wins over the redirect, the scheduler publishes pages without a key for the hooks
		if ($pages->isPublic($this->pageKey($path))) {
			return false;
		}

		$target = $this->isAbsolute($rules[$path]) ? $rules[$path] : DOMAIN_BASE . $this->encode($rules[$path]);
		$target = Text::encodeURL($target);

		if (!empty($_SERVER['QUERY_STRING'])) {
			$target .= (Text::stringContains($target, '?') ? '&' : '?') . $_SERVER['QUERY_STRING'];
		}

		Redirect::url($target, 301);
	}

	// Returns an array like array('old-path' => 'new-path', ...)
	private function parse($text)
	{
		$rules = array();
		$this->rejected = array();
		foreach (preg_split('/\R/', $text, -1, PREG_SPLIT_NO_EMPTY) as $line) {
			$parts = preg_split('/\s+/', trim($line), -1, PREG_SPLIT_NO_EMPTY);
			if (empty($parts)) {
				continue;
			}

			$from = count($parts) === 2 ? $this->path($parts[0]) : '';
			if ($from === '' || $this->isAbsolute($from)) {
				$this->rejected[] = trim($line);
				continue;
			}

			$rules[$this->decode($from)] = $this->decode($this->path($parts[1]));
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
			$lines[] = $this->encode($from) . ' ' . ($to === '' ? '/' : $this->encode($to));
		}

		return implode(PHP_EOL, $lines);
	}

	// Returns the value relative to the site root when the value is from this site
	private function path($value)
	{
		if ($this->isAbsolute($value)) {
			$base = $this->withoutScheme(DOMAIN_BASE);
			$withoutScheme = $this->withoutScheme($value) . '/';
			if (stripos($withoutScheme, $base) !== 0) {
				return $value;
			}

			return trim(substr($withoutScheme, strlen($base)), '/');
		}

		// A path from the root of the domain, for a site in a subfolder
		if (Text::startsWith($value . '/', HTML_PATH_ROOT)) {
			$value = Text::cut($value . '/', Text::length(HTML_PATH_ROOT), null);
		}

		return trim($value, '/');
	}

	// The scheme and the www do not change the site
	private function withoutScheme($url)
	{
		return preg_replace('#^https?://(www\.)?#i', '', $url);
	}

	// Paths are decoded to compare them with each other and with $url->uri()
	private function decode($value)
	{
		return $this->keepEncoded($value) ? $value : urldecode($value);
	}

	// The characters of a decoded path that are not safe in the textarea or in a URL
	private function encode($value)
	{
		if ($this->keepEncoded($value)) {
			return $value;
		}

		return preg_replace_callback('/[\s%+]/', function ($match) {
			return rawurlencode($match[0]);
		}, $value);
	}

	private function keepEncoded($value)
	{
		return $this->isAbsolute($value) || Text::stringContains($value, '?');
	}

	private function pagePath($key)
	{
		return ltrim(trim(PAGE_URI_FILTER, '/') . '/' . $key, '/');
	}

	// Returns the key of the page in the path, or FALSE if the path is not for a page
	private function pageKey($path)
	{
		$prefix = $this->pagePath('');
		return Text::startsWith($path, $prefix) ? Text::cut($path, Text::length($prefix), null) : false;
	}

	private function isAbsolute($value)
	{
		return preg_match('#^https?://#i', $value) === 1;
	}
}
