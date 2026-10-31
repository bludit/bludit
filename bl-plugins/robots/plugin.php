<?php

class pluginRobots extends Plugin {

	private $aiTrainingCrawlers = array(
		'GPTBot',
		'ClaudeBot',
		'Google-Extended',
		'Applebot-Extended',
		'meta-externalagent',
		'CCBot',
		'Bytespider',
		'Amazonbot',
		'cohere-ai'
	);

	private $aiSearchCrawlers = array(
		'OAI-SearchBot',
		'ChatGPT-User',
		'Claude-SearchBot',
		'Claude-User',
		'PerplexityBot',
		'Perplexity-User',
		'meta-externalfetcher'
	);

	public function init()
	{
		$this->dbFields = array(
			'robotstxt'=>'User-agent: *'.PHP_EOL.'Allow: /',
			'blockAITraining'=>false,
			'blockAISearch'=>false,
			'llmstxt'=>false
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.DOMAIN.'/robots.txt</label>';
		$html .= '<textarea name="robotstxt" id="jsrobotstxt">'.$this->getValue('robotstxt').'</textarea>';
		$html .= '</div>';

		$html .= $this->formSelect('blockAITraining', $L->get('block-ai-training'), $L->get('block-ai-training-tip'));
		$html .= $this->formSelect('blockAISearch', $L->get('block-ai-search'), $L->get('block-ai-search-tip'));
		$html .= $this->formSelect('llmstxt', $L->get('llms-txt'), $L->get('llms-txt-tip'));

		return $html;
	}

	private function formSelect($field, $label, $tip)
	{
		global $L;

		$html  = '<div>';
		$html .= '<label>'.$label.'</label>';
		$html .= '<select name="'.$field.'">';
		$html .= '<option value="true" '.($this->getValue($field)===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue($field)===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">'.$tip.'</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		global $WHERE_AM_I;

		$html = '';
		if ($WHERE_AM_I=='page') {
			global $page;
			$robots = array();

			if ($page->noindex()) {
				$robots['noindex'] = 'noindex';
			}

			if ($page->nofollow()) {
				$robots['nofollow'] = 'nofollow';
			}

			if ($page->noarchive()) {
				$robots['noarchive'] = 'noarchive';
			}

			if (!empty($robots)) {
				$robots = implode(',', $robots);
				$html .= '<meta name="robots" content="'.$robots.'">'.PHP_EOL;
			}
		}

		return $html;
	}

	public function beforeAll()
	{
		$webhook = 'robots.txt';
		if ($this->webhook($webhook)) {
			header('Content-type: text/plain');
			// Include link to sitemap in robots.txt if the plugin is enabled
			if (pluginActivated('pluginSitemap')) {
				echo 'Sitemap: '.DOMAIN_BASE.'sitemap.xml'.PHP_EOL;
			}
			echo $this->getValue('robotstxt', false).PHP_EOL;
			if ($this->getValue('blockAITraining')) {
				echo $this->crawlerRules($this->aiTrainingCrawlers);
			}
			if ($this->getValue('blockAISearch')) {
				echo $this->crawlerRules($this->aiSearchCrawlers);
			}
			exit(0);
		}

		if ($this->getValue('llmstxt') && $this->webhook('llms.txt')) {
			header('Content-type: text/plain; charset=utf-8');
			echo $this->llmstxt();
			exit(0);
		}
	}

	private function crawlerRules($crawlers)
	{
		$rules = PHP_EOL;
		foreach ($crawlers as $crawler) {
			$rules .= 'User-agent: '.$crawler.PHP_EOL;
		}

		return $rules.'Disallow: /'.PHP_EOL;
	}

	// Built from the database, the content of the pages is not read
	private function llmstxt()
	{
		global $site;
		global $pages;

		$text = '# '.Sanitize::htmlDecode($site->title()).PHP_EOL.PHP_EOL;
		if ($site->description()) {
			$text .= '> '.Sanitize::htmlDecode($site->description()).PHP_EOL.PHP_EOL;
		}

		$text .= '## Pages'.PHP_EOL.PHP_EOL;
		foreach ($pages->getDB(false) as $key=>$page) {
			if (!in_array($page['type'], array('published', 'static', 'sticky')) || $page['noindex']) {
				continue;
			}

			$text .= '- ['.Sanitize::htmlDecode($page['title']).']('.DOMAIN_PAGES.$key.')';
			if (!empty($page['description'])) {
				$text .= ': '.Sanitize::htmlDecode($page['description']);
			}
			$text .= PHP_EOL;
		}

		return $text;
	}

}
