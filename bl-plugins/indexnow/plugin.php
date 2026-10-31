<?php

class pluginIndexNow extends Plugin {

	const ENDPOINT = 'https://api.indexnow.org/indexnow';

	public function init()
	{
		$this->dbFields = array(
			'key' => '',
			'lastResponse' => ''
		);

		$this->formButtons = false;
	}

	public function install($position = 1)
	{
		$this->dbFields['key'] = bin2hex(random_bytes(16));

		return parent::install($position);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		if (!$this->isPublicSite()) {
			$html .= '<div class="alert alert-warning" role="alert">';
			$html .= $L->get('not-public-site');
			$html .= '</div>';
		}

		$html .= '<div>';
		$html .= '<label>' . $L->get('key-file') . '</label>';
		$html .= '<a href="' . $this->keyLocation() . '" target="_blank">' . $this->keyLocation() . '</a>';
		$html .= '<span class="tip">' . $L->get('key-file-tip') . '</span>';
		$html .= '</div>';

		$lastResponse = $this->getValue('lastResponse');

		$html .= '<div>';
		$html .= '<label>' . $L->get('last-response') . '</label>';
		$html .= '<p>' . (empty($lastResponse) ? $L->get('no-submissions-yet') : $lastResponse) . '</p>';
		$html .= '</div>';

		return $html;
	}

	public function beforeAll()
	{
		$key = $this->getValue('key');

		if ($this->webhook($key . '.txt')) {
			header('Content-Type: text/plain; charset=utf-8');
			echo $key;
			exit(0);
		}
	}

	// The scheduler also calls this hook when a scheduled page is published
	public function afterPageCreate($key = false)
	{
		if ($this->isIndexable($key)) {
			$this->submit(array($key));
		}
	}

	public function afterPageModify($key = false, $previousKey = false)
	{
		if (!$this->isIndexable($key)) {
			return;
		}

		$keys = array($key);
		if ($previousKey !== false && $previousKey !== $key) {
			$keys[] = $previousKey;
		}

		$this->submit($keys);
	}

	// After the delete the page is gone and there is no way to know if it was public
	public function beforePageDelete($key = false)
	{
		if ($this->isIndexable($key)) {
			$this->submit(array($key));
		}
	}

	private function submit($keys)
	{
		if (!$this->isPublicSite()) {
			return;
		}

		foreach ($keys as $key) {
			$request = self::ENDPOINT . '?url=' . rawurlencode(DOMAIN_PAGES . $key);
			$request .= '&key=' . $this->getValue('key');
			$request .= '&keyLocation=' . rawurlencode($this->keyLocation());

			// A short timeout, a slow response must not block the save of the page
			$response = TCP::http($request, 'GET', true, 3, false, true, true);
		}

		$status = $response === false ? 'No response' : strtok($response, "\r\n");
		$this->setField('lastResponse', Date::current(DB_DATE_FORMAT) . ' ' . $status);
	}

	private function keyLocation()
	{
		return DOMAIN_BASE . $this->getValue('key') . '.txt';
	}

	private function isIndexable($key)
	{
		global $pages;

		$page = $pages->getPageDB($key);
		return $pages->isPublic($key) && !$page['noindex'];
	}

	// Sites in localhost or in an intranet can't be reached by the search engines
	private function isPublicSite()
	{
		$host = parse_url(DOMAIN_BASE, PHP_URL_HOST);

		if (Valid::ip($host)) {
			return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
		}

		return Text::stringContains($host, '.') && !Text::endsWith($host, '.local');
	}
}
