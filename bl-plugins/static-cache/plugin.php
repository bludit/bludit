<?php

class pluginStaticCache extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'enabled' => true
		);
	}

	// ── SERVE ──────────────────────────────────────────────────────────────
	// Fires before the router and page rules run, so cache hits skip the
	// expensive part of the request (page DB load, Markdown parse, theme).
	public function beforeAll()
	{
		if (!$this->getValue('enabled')) {
			return;
		}
		if (!$this->isCacheable()) {
			return;
		}

		$path = $this->cachePath();
		if (is_file($path)) {
			header('Content-Type: text/html; charset=UTF-8');
			header('X-Bludit-Cache: HIT');
			readfile($path);
			exit;
		}

		// Cache MISS: buffer the response so we can write it to disk after
		// the page has finished rendering.
		ob_start();
		register_shutdown_function(array($this, 'writeCache'));
	}

	public function writeCache()
	{
		if (!ob_get_level()) {
			return;
		}

		$html = ob_get_contents();
		ob_end_flush();

		if (empty($html)) {
			return;
		}
		if (http_response_code() !== 200) {
			return;
		}
		if (!$this->isCacheable()) {
			return;
		}

		global $url;
		if (isset($url) && $url->notFound()) {
			return;
		}

		@file_put_contents($this->cachePath(), $html, LOCK_EX);
	}

	// ── INVALIDATE ─────────────────────────────────────────────────────────
	public function afterPageCreate()
	{
		$this->clearAll();
	}

	public function afterPageModify()
	{
		$this->clearAll();
	}

	public function afterPageDelete()
	{
		$this->clearAll();
	}

	public function afterSiteSave()
	{
		$this->clearAll();
	}

	// ── ADMIN UI ───────────────────────────────────────────────────────────
	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('status').'</label>';
		$html .= '<select name="enabled">';
		$html .= '<option value="true" '.($this->getValue('enabled')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('enabled')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">'.$L->get('how-it-works').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('cached-files').'</label>';
		$html .= '<span>'.$this->countCachedFiles().'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<button type="submit" name="action" value="clear" class="btn btn-secondary">'.$L->get('clear-cache').'</button>';
		$html .= '<span class="tip">'.$L->get('clear-cache-tip').'</span>';
		$html .= '</div>';

		return $html;
	}

	public function post()
	{
		if (isset($_POST['action']) && $_POST['action'] === 'clear') {
			$this->clearAll();
			return true;
		}
		return parent::post();
	}

	// ── HELPERS ────────────────────────────────────────────────────────────
	private function isCacheable()
	{
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
		if ($method !== 'GET') {
			return false;
		}
		if (!empty($_SERVER['QUERY_STRING'])) {
			return false;
		}
		$sessionName = session_name();
		if ($sessionName && !empty($_COOKIE[$sessionName])) {
			return false;
		}
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		if (strpos($uri, HTML_PATH_ROOT.'admin') === 0) {
			return false;
		}
		if (strpos($uri, HTML_PATH_ROOT.'api') === 0) {
			return false;
		}
		return true;
	}

	private function cachePath()
	{
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
		return $this->workspace().md5($uri).'.html';
	}

	private function clearAll()
	{
		$files = glob($this->workspace().'*.html');
		if (!is_array($files)) {
			return;
		}
		foreach ($files as $f) {
			@unlink($f);
		}
	}

	private function countCachedFiles()
	{
		$files = glob($this->workspace().'*.html');
		return is_array($files) ? count($files) : 0;
	}
}
